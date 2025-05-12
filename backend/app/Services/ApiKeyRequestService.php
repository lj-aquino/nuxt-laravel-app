<?php

namespace App\Services;

use Exception;
use Spatie\Crypto\Rsa\PublicKey;
use Illuminate\Support\Facades\Log;
use DateTime;
use DateTimeZone;

class ApiKeyRequestService
{
    public function generateEncryptedApiKey()
    {
        try {
            // Check if API key exists and is not placeholder
            $api_key = config('app.api_key');
            Log::info('AMIS API - Current API key first 8 chars: ' . substr($api_key, 0, 8));
            
            if (!$api_key || $api_key === '<TO_BE_PROVIDED_BY_AMIS_TEAM>') {
                throw new Exception('API key is not configured or still has placeholder value! Current value: ' . $api_key);
            }
            
            // Get current time in Philippine timezone
            $manila_time = new DateTime('now', new DateTimeZone('Asia/Manila'));
            $current_time = $manila_time->getTimestamp();
            
            // Log both system time and Manila time for comparison
            Log::info('AMIS API - System timestamp: ' . time() . ' (' . date('Y-m-d H:i:s') . ')');
            Log::info('AMIS API - Manila timestamp: ' . $current_time . ' (' . $manila_time->format('Y-m-d H:i:s T') . ')');
            
            // Get the public key
            $public_key_path = storage_path('keys/public_key.pem');
            Log::info('AMIS API - Looking for public key at: ' . $public_key_path);
            
            if (!file_exists($public_key_path)) {
                throw new Exception('Public key file not found at: ' . $public_key_path);
            }
            
            $public_key = PublicKey::fromFile($public_key_path);
            
            if (!$public_key) {
                throw new Exception('Failed to load public key!');
            }
            
            // Create the string to encrypt
            $data_to_encrypt = $api_key . '|' . $current_time;
            Log::info('AMIS API - Encrypting data with timestamp: ' . $current_time);
            
            // Encrypt the concatenated value
            $encrypted_data = $public_key->encrypt($data_to_encrypt);
            
            // Return the base64-encoded data
            $encrypted_key = base64_encode($encrypted_data);
            Log::info('AMIS API - Generated encrypted key (length: ' . strlen($encrypted_key) . ')');
            
            return $encrypted_key;
        } catch (Exception $e) {
            Log::error('AMIS API - Error generating encrypted key: ' . $e->getMessage());
            throw $e;
        }
    }
}