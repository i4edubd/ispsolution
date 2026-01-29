<?php

declare(strict_types=1);

namespace App\Http\Controllers\Panel;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreSmsPaymentRequest;
use App\Models\SmsPayment;
use App\Services\SmsBalanceService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

/**
 * SMS Payment Controller
 *
 * Handles SMS credit purchases and payment processing
 * Reference: REFERENCE_SYSTEM_QUICK_GUIDE.md - Phase 2: SMS Payment Integration
 * Reference: REFERENCE_SYSTEM_IMPLEMENTATION_TODO.md - Section 1.1
 */
class SmsPaymentController extends Controller
{
    /**
     * Create a new controller instance.
     */
    public function __construct(
        protected SmsBalanceService $smsBalanceService
    ) {}

    /**
     * Display a listing of SMS payments for the authenticated operator
     */
    public function index(Request $request): JsonResponse
    {
        $user = $request->user();

        // Only allow operators, sub-operators, and admins
        if (! $user->hasAnyRole(['admin', 'operator', 'sub-operator', 'superadmin'])) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized. Only operators, sub-operators, and admins can access SMS payments.',
            ], 403);
        }

        $payments = SmsPayment::where('operator_id', $user->id)
            ->orderBy('created_at', 'desc')
            ->paginate($request->input('per_page', 15));

        return response()->json([
            'success' => true,
            'data' => $payments,
        ]);
    }

    /**
     * Store a newly created SMS payment in storage
     */
    public function store(StoreSmsPaymentRequest $request): JsonResponse
    {
        $user = $request->user();

        // Calculate amount server-side based on quantity and pricing tiers
        $quantity = $request->integer('sms_quantity');
        $amount = $this->calculateSmsPrice($quantity);

        // Create SMS payment record
        $payment = SmsPayment::create([
            'operator_id' => $user->id,
            'amount' => $amount, // Server-calculated, not from user input
            'sms_quantity' => $quantity,
            'payment_method' => $request->input('payment_method'),
            'status' => 'pending',
        ]);

        // TODO: Initiate payment gateway transaction
        // This will be implemented when integrating with payment gateways
        // For now, we'll just create the payment record

        return response()->json([
            'success' => true,
            'message' => 'SMS payment initiated successfully',
            'data' => $payment,
        ], 201);
    }

    /**
     * Display the specified SMS payment
     */
    public function show(SmsPayment $smsPayment): JsonResponse
    {
        $user = auth()->user();

        // Only allow operators, sub-operators, and admins
        if (! $user->hasAnyRole(['admin', 'operator', 'sub-operator', 'superadmin'])) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized',
            ], 403);
        }

        // Admins and superadmins can view all payments, others can only view their own
        $isAdmin = $user->hasAnyRole(['admin', 'superadmin']);
        if (! $isAdmin && $smsPayment->operator_id !== $user->id) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized',
            ], 403);
        }

        return response()->json([
            'success' => true,
            'data' => $smsPayment,
        ]);
    }

    /**
     * Get SMS balance and history for the authenticated operator
     */
    public function balance(Request $request): JsonResponse
    {
        $user = $request->user();

        // Only allow operators, sub-operators, and admins
        if (! $user->hasAnyRole(['admin', 'operator', 'sub-operator', 'superadmin'])) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized. Only operators, sub-operators, and admins can access SMS balance.',
            ], 403);
        }

        $history = $this->smsBalanceService->getHistory($user, 20);
        $stats = $this->smsBalanceService->getUsageStats($user, 'month');

        return response()->json([
            'success' => true,
            'data' => [
                'current_balance' => $user->sms_balance ?? 0,
                'low_balance_threshold' => $user->sms_low_balance_threshold ?? 100,
                'is_low_balance' => $user->hasLowSmsBalance(),
                'history' => $history,
                'monthly_stats' => $stats,
            ],
        ]);
    }

    /**
     * Handle payment gateway webhook/callback
     *
     * This method will be called by payment gateways to update payment status
     * NOTE: This endpoint requires webhook signature verification before processing
     */
    public function webhook(Request $request): JsonResponse
    {
        // SECURITY: Reject all requests until proper verification is implemented
        // This prevents unauthorized balance credits and payment manipulation
        abort(403, 'Webhook endpoint disabled. Contact administrator for setup.');

        // TODO: CRITICAL - Implement webhook signature verification before enabling
        // Payment gateway webhooks MUST verify the request authenticity
        // This prevents unauthorized balance credits
        // Example for Bkash:
        // 1. Verify signature using gateway's public key
        // 2. Validate request IP against gateway's whitelist
        // 3. Check request timestamp to prevent replay attacks

        // TODO: After verification is implemented:
        // 1. Extract payment details from webhook payload
        // 2. Find the corresponding SmsPayment record
        // 3. Update payment status based on gateway response
        // 4. If successful, add SMS credits to operator balance using SmsBalanceService
        // 5. Send notification to operator about payment status
    }

    /**
     * Complete an SMS payment (admin/test use only)
     *
     * This endpoint allows manual completion of payments for testing
     */
    public function complete(SmsPayment $smsPayment): JsonResponse
    {
        $user = auth()->user();

        // Only superadmins can manually complete payments
        if (! $user->hasRole('superadmin')) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized. Only superadmins can manually complete payments.',
            ], 403);
        }

        // Only allow if payment is pending
        if (! $smsPayment->isPending()) {
            return response()->json([
                'success' => false,
                'message' => 'Payment is not pending',
            ], 400);
        }

        // Mark payment as completed
        $smsPayment->markCompleted();

        // Add SMS credits to operator balance
        $operator = $smsPayment->operator;
        $this->smsBalanceService->addCredits(
            $operator,
            $smsPayment->sms_quantity,
            'purchase',
            'sms_payment',
            $smsPayment->id,
            'SMS payment completed: ' . $smsPayment->transaction_id
        );

        return response()->json([
            'success' => true,
            'message' => 'Payment completed successfully',
            'data' => [
                'payment' => $smsPayment->fresh(),
                'new_balance' => $operator->fresh()->sms_balance,
            ],
        ]);
    }

    /**
     * Display SMS payment history page (Web UI)
     */
    public function webIndex(Request $request): View
    {
        $user = $request->user();

        // Get paginated payments
        $payments = SmsPayment::where('operator_id', $user->id)
            ->orderBy('created_at', 'desc')
            ->paginate(15);

        // Get balance information
        $balance = [
            'current_balance' => $user->sms_balance ?? 0,
            'low_balance_threshold' => $user->sms_low_balance_threshold ?? 100,
            'is_low_balance' => $user->hasLowSmsBalance(),
            'history' => $this->smsBalanceService->getHistory($user, 10),
            'monthly_stats' => $this->smsBalanceService->getUsageStats($user, 'month'),
        ];

        return view('panels.operator.sms-payments.index', compact('payments', 'balance'));
    }

    /**
     * Display SMS payment purchase page (Web UI)
     */
    public function webCreate(): View
    {
        $user = auth()->user();

        // Only operators, sub-operators, and admins can purchase SMS credits
        if (! $user->hasAnyRole(['admin', 'operator', 'sub-operator', 'superadmin'])) {
            abort(403, 'Unauthorized. Only operators can purchase SMS credits.');
        }

        return view('panels.operator.sms-payments.create');
    }

    /**
     * Calculate SMS price based on quantity and pricing tiers
     *
     * @param int $quantity Number of SMS credits
     *
     * @return float Calculated price in local currency
     */
    private function calculateSmsPrice(int $quantity): float
    {
        // Pricing tiers (per SMS in BDT)
        // TODO: Move these to config file or database for easier management
        if ($quantity >= 10000) {
            return $quantity * 0.40; // 20% discount
        } elseif ($quantity >= 5000) {
            return $quantity * 0.45; // 10% discount
        } else {
            return $quantity * 0.50; // Base rate
        }
    }
}
