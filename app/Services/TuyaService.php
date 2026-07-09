<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Cache;
use Exception;

class TuyaService
{
    protected $clientId;
    protected $clientSecret;
    protected $baseUrl;

    public function __construct()
    {
        $this->clientId = config('services.tuya.client_id', env('TUYA_CLIENT_ID'));
        $this->clientSecret = config('services.tuya.client_secret', env('TUYA_CLIENT_SECRET'));
        
        // Use EU datacenter by default, but allow override
        $this->baseUrl = config('services.tuya.base_url', env('TUYA_BASE_URL', 'https://openapi.tuyaeu.com'));
    }

    /**
     * Unlock a smart lock using password-free door-operate.
     * 
     * @param string $deviceId The Tuya device ID
     * @return bool True if successful
     */
    public function remoteUnlock(string $deviceId): bool
    {
        // 1. Get Access Token
        $token = $this->getAccessToken();
        if (!$token) {
            throw new Exception('Failed to obtain Tuya access token.');
        }

        // 2. Request Password Ticket
        $ticketId = $this->getPasswordTicket($deviceId, $token);
        if (!$ticketId) {
            throw new Exception('Failed to obtain Tuya password ticket.');
        }

        // 3. Send Unlock Command
        return $this->sendUnlockCommand($deviceId, $token, $ticketId);
    }

    /**
     * Retrieves an access token, utilizing cache.
     */
    protected function getAccessToken(): ?string
    {
        // Cache the token to avoid hitting rate limits (usually valid for 2 hours)
        return Cache::remember('tuya_access_token', 7000, function () {
            $path = '/v1.0/token?grant_type=1';
            
            $headers = $this->buildHeaders($path, 'GET', '', null);
            
            $response = Http::withHeaders($headers)
                ->get($this->baseUrl . $path);

            if ($response->successful() && $response->json('success')) {
                return $response->json('result.access_token');
            }

            throw new Exception('Tuya Token Error: ' . $response->body());
        });
    }

    /**
     * Request a temporary password ticket.
     */
    protected function getPasswordTicket(string $deviceId, string $token): ?string
    {
        $path = "/v1.0/smart-lock/devices/{$deviceId}/password-ticket";
        
        $headers = $this->buildHeaders($path, 'POST', '', $token);
        
        $response = Http::withHeaders($headers)
            ->post($this->baseUrl . $path);

        if ($response->successful() && $response->json('success')) {
            return $response->json('result.ticket_id');
        }
        
        throw new Exception('Tuya Ticket Error: ' . $response->body());
    }

    /**
     * Execute the password-free door-operate command.
     */
    protected function sendUnlockCommand(string $deviceId, string $token, string $ticketId): bool
    {
        $path = "/v1.0/smart-lock/devices/{$deviceId}/password-free/door-operate";
        
        $bodyParams = [
            'ticket_id' => $ticketId,
            'open' => true,
        ];
        
        $bodyString = json_encode($bodyParams);
        
        $headers = $this->buildHeaders($path, 'POST', $bodyString, $token);
        
        $response = Http::withHeaders($headers)
            ->post($this->baseUrl . $path, $bodyParams);

        if ($response->successful() && $response->json('success')) {
            return true;
        }

        throw new Exception('Tuya Unlock Error: ' . $response->body());
    }

    /**
     * Builds the required Tuya headers including the HMAC-SHA256 signature.
     */
    protected function buildHeaders(string $path, string $method, string $body, ?string $token): array
    {
        $t = (string)(time() * 1000);
        $nonce = ''; // Can be random string for extra security, leaving empty for simplicity

        // Calculate Content-SHA256
        $contentSha256 = hash('sha256', $body);
        
        // StringToSign = HTTPMethod + \n + Content-SHA256 + \n + Headers(empty here) + \n + URL
        $stringToSign = $method . "\n" . $contentSha256 . "\n\n" . $path;

        // Message = clientId + accessToken(optional) + t + nonce + StringToSign
        $message = $this->clientId . ($token ?? '') . $t . $nonce . $stringToSign;

        $sign = strtoupper(hash_hmac('sha256', $message, $this->clientSecret));

        $headers = [
            'client_id' => $this->clientId,
            'sign' => $sign,
            'sign_method' => 'HMAC-SHA256',
            't' => $t,
        ];

        if ($token) {
            $headers['access_token'] = $token;
        }

        return $headers;
    }
}
