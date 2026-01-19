<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class WhatsAppService
{
    private string $apiUrl;
    private string $accessToken;
    private string $phoneNumberId;
    private bool $enabled;

    public function __construct()
    {
        $this->apiUrl = config('services.whatsapp.api_url', 'https://graph.facebook.com/v18.0');
        $this->accessToken = config('services.whatsapp.access_token', '');
        $this->phoneNumberId = config('services.whatsapp.phone_number_id', '');
        $this->enabled = config('services.whatsapp.enabled', false);
    }

    /**
     * Send a text message via WhatsApp Business API
     */
    public function sendTextMessage(string $to, string $message): array
    {
        if (!$this->enabled) {
            Log::info('WhatsApp service is disabled');
            return ['success' => false, 'error' => 'WhatsApp service is disabled'];
        }

        try {
            $response = Http::withToken($this->accessToken)
                ->post("{$this->apiUrl}/{$this->phoneNumberId}/messages", [
                    'messaging_product' => 'whatsapp',
                    'to' => $this->formatPhoneNumber($to),
                    'type' => 'text',
                    'text' => [
                        'body' => $message,
                    ],
                ]);

            if ($response->successful()) {
                Log::info('WhatsApp message sent successfully', [
                    'to' => $to,
                    'message_id' => $response->json('messages.0.id'),
                ]);

                return [
                    'success' => true,
                    'message_id' => $response->json('messages.0.id'),
                ];
            }

            Log::error('WhatsApp API error', [
                'to' => $to,
                'status' => $response->status(),
                'error' => $response->json(),
            ]);

            return [
                'success' => false,
                'error' => $response->json('error.message', 'Unknown error'),
            ];
        } catch (\Exception $e) {
            Log::error('WhatsApp service exception', [
                'to' => $to,
                'error' => $e->getMessage(),
            ]);

            return [
                'success' => false,
                'error' => $e->getMessage(),
            ];
        }
    }

    /**
     * Send a template message via WhatsApp Business API
     */
    public function sendTemplateMessage(string $to, string $templateName, array $parameters = []): array
    {
        if (!$this->enabled) {
            return ['success' => false, 'error' => 'WhatsApp service is disabled'];
        }

        try {
            $components = [];
            if (!empty($parameters)) {
                $components[] = [
                    'type' => 'body',
                    'parameters' => collect($parameters)->map(fn($param) => [
                        'type' => 'text',
                        'text' => $param,
                    ])->toArray(),
                ];
            }

            $response = Http::withToken($this->accessToken)
                ->post("{$this->apiUrl}/{$this->phoneNumberId}/messages", [
                    'messaging_product' => 'whatsapp',
                    'to' => $this->formatPhoneNumber($to),
                    'type' => 'template',
                    'template' => [
                        'name' => $templateName,
                        'language' => [
                            'code' => 'en',
                        ],
                        'components' => $components,
                    ],
                ]);

            if ($response->successful()) {
                Log::info('WhatsApp template message sent', [
                    'to' => $to,
                    'template' => $templateName,
                    'message_id' => $response->json('messages.0.id'),
                ]);

                return [
                    'success' => true,
                    'message_id' => $response->json('messages.0.id'),
                ];
            }

            return [
                'success' => false,
                'error' => $response->json('error.message', 'Unknown error'),
            ];
        } catch (\Exception $e) {
            Log::error('WhatsApp template service exception', [
                'to' => $to,
                'template' => $templateName,
                'error' => $e->getMessage(),
            ]);

            return [
                'success' => false,
                'error' => $e->getMessage(),
            ];
        }
    }

    /**
     * Send invoice notification
     */
    public function sendInvoiceNotification(string $to, array $invoiceData): array
    {
        $message = "Invoice #{$invoiceData['invoice_number']}\n\n"
            . "Amount: \${$invoiceData['amount']}\n"
            . "Due Date: {$invoiceData['due_date']}\n"
            . "Status: {$invoiceData['status']}\n\n"
            . "Please pay your invoice on time to avoid service interruption.";

        return $this->sendTextMessage($to, $message);
    }

    /**
     * Send payment confirmation
     */
    public function sendPaymentConfirmation(string $to, array $paymentData): array
    {
        $message = "Payment Received ✓\n\n"
            . "Amount: \${$paymentData['amount']}\n"
            . "Date: {$paymentData['date']}\n"
            . "Receipt: {$paymentData['receipt_number']}\n\n"
            . "Thank you for your payment!";

        return $this->sendTextMessage($to, $message);
    }

    /**
     * Send service expiration warning
     */
    public function sendExpirationWarning(string $to, array $serviceData): array
    {
        $message = "⚠️ Service Expiration Warning\n\n"
            . "Your service will expire in {$serviceData['days_remaining']} days.\n"
            . "Package: {$serviceData['package_name']}\n"
            . "Expiry Date: {$serviceData['expiry_date']}\n\n"
            . "Please renew to avoid service interruption.";

        return $this->sendTextMessage($to, $message);
    }

    /**
     * Send account locked notification
     */
    public function sendAccountLockedNotification(string $to, string $reason): array
    {
        $message = "🔒 Account Locked\n\n"
            . "Your account has been locked due to: {$reason}\n\n"
            . "Please contact support or make payment to unlock your account.";

        return $this->sendTextMessage($to, $message);
    }

    /**
     * Format phone number to E.164 format
     */
    private function formatPhoneNumber(string $phone): string
    {
        // Remove all non-numeric characters
        $phone = preg_replace('/[^0-9]/', '', $phone);

        // Add country code if not present (assuming Bangladesh +880)
        if (!str_starts_with($phone, '880') && !str_starts_with($phone, '+880')) {
            // Remove leading zero if present
            $phone = ltrim($phone, '0');
            $phone = '880' . $phone;
        }

        return $phone;
    }

    /**
     * Verify webhook signature
     */
    public function verifyWebhookSignature(string $signature, string $payload): bool
    {
        $appSecret = config('services.whatsapp.app_secret', '');
        $expectedSignature = 'sha256=' . hash_hmac('sha256', $payload, $appSecret);

        return hash_equals($expectedSignature, $signature);
    }

    /**
     * Check if service is enabled
     */
    public function isEnabled(): bool
    {
        return $this->enabled && !empty($this->accessToken) && !empty($this->phoneNumberId);
    }
}
