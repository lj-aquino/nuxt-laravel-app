<?php

namespace App\Services;

use Exception;
use Spatie\Crypto\Rsa\PublicKey;

class ApiKeyRequestService
{
    public function generateEncryptedApiKey()
    {
        // Using Spatie's PublicKey model, get the public key
        $public_key = PublicKey::fromFile(storage_path('keys/public_key.pem'));
        
        // Get the current time
        $current_time = time();
        
        // Get the API key
        $api_key = env('ENROLLMENT_API_KEY');
        
        if (!$public_key) {
            throw new Exception('Public key is missing!');
        }
        
        if (!$api_key) {
            throw new Exception('API key is missing!');
        }
        
        // Encrypt the concatenated value (api key + | + current time) using RSA
        $encrypted_data = $public_key->encrypt($api_key . '|' . $current_time);
        
        // Return the base64-encoded data (final API key)
        return base64_encode($encrypted_data);
    }
}