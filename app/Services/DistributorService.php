<?php

namespace App\Services;

class DistributorService
{
    /**
     * Supported country codes with their international dialing prefixes
     *
     * @var array<string, string>
     */
    protected $countryCodes = [
        'BD' => '+880',
        'IN' => '+91',
        'PK' => '+92',
        'NP' => '+977',
        'LK' => '+94',
        'MM' => '+95',
    ];
    
    /**
     * Normalize a mobile number to international format
     *
     * @param string $mobile The mobile number to normalize
     * @return string The normalized mobile number with country code
     */
    public function normalizeMobile(string $mobile): string
    {
        // Remove spaces and dashes
        $mobile = preg_replace('/[\s\-]/', '', $mobile);
        
        // If starts with 0, replace with country code
        if (substr($mobile, 0, 1) === '0') {
            $mobile = '+880' . substr($mobile, 1); // Default to BD
        }
        
        // If doesn't start with +, add default country code
        if (substr($mobile, 0, 1) !== '+') {
            $mobile = '+880' . $mobile;
        }
        
        return $mobile;
    }
    
    /**
     * Validate a mobile number format
     *
     * @param string $mobile The mobile number to validate
     * @return bool True if valid, false otherwise
     */
    public function validateMobile(string $mobile): bool
    {
        // Check if mobile starts with valid country code
        foreach ($this->countryCodes as $code) {
            if (substr($mobile, 0, strlen($code)) === $code) {
                // Check length (typically 10-15 digits after country code)
                $number = substr($mobile, strlen($code));
                return strlen($number) >= 10 && strlen($number) <= 15 && ctype_digit($number);
            }
        }
        
        return false;
    }
    
    /**
     * Format a mobile number with the appropriate country code
     *
     * @param string $mobile The mobile number to format
     * @param string $countryCode The country code (default: BD)
     * @return string The formatted mobile number
     */
    public function formatMobile(string $mobile, string $countryCode = 'BD'): string
    {
        $prefix = $this->countryCodes[$countryCode] ?? '+880';
        
        // If mobile doesn't start with +, normalize it
        if (substr($mobile, 0, 1) !== '+') {
            return $this->normalizeMobile($mobile);
        }
        
        return $mobile;
    }
    
    /**
     * Get the country code from a mobile number
     *
     * @param string $mobile The mobile number
     * @return string|null The country code or null if not found
     */
    public function getCountryCode(string $mobile): ?string
    {
        foreach ($this->countryCodes as $country => $code) {
            if (substr($mobile, 0, strlen($code)) === $code) {
                return $country;
            }
        }
        
        return null;
    }
}
