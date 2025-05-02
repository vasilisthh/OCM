<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class UserService
{
    protected $apiUrl = 'https://jsonplaceholder.typicode.com/users';
    
    /**
     * Fetch users from external API
     * 
     * @return array
     */
    public function fetchUsersFromApi()
    {
        try {
            $response = Http::get($this->apiUrl);
            
            if ($response->successful()) {
                Log::info('Successfully fetched users from API', [
                    'count' => count($response->json())
                ]);
                
                return [
                    'success' => true,
                    'data' => $response->json()
                ];
            } else {
                Log::error('Failed to fetch users from API', [
                    'status' => $response->status(),
                    'response' => $response->body()
                ]);
                
                return [
                    'success' => false,
                    'error' => 'Failed to fetch users from API: ' . $response->status()
                ];
            }
        } catch (\Exception $e) {
            Log::error('Exception while fetching users from API', [
                'message' => $e->getMessage()
            ]);
            
            return [
                'success' => false,
                'error' => 'Exception while fetching users: ' . $e->getMessage()
            ];
        }
    }
    
    /**
     * Store fetched users in database
     * 
     * @param array $users
     * @return array
     */
    public function storeFetchedUsers($users)
    {
        $created = 0;
        $updated = 0;
        $failed = 0;
        
        foreach ($users as $userData) {
            try {
                Log::info('Processing user', [
                    'id' => $userData['id'] ?? 'unknown',
                    'email' => $userData['email'] ?? 'none'
                ]);
                
                $existingUser = User::where('id', $userData['id'])->first();
                
                if ($existingUser) {
                    $existingUser->update([
                        'name' => $userData['name'],
                        'email' => $userData['email'],
                        'username' => $userData['username'],
                        'phone' => $userData['phone'] ?? null,
                        'website' => $userData['website'] ?? null
                    ]);
                    $updated++;
                } else {
                    // Try to prevent duplicate email/username errors
                    $duplicateEmail = User::where('email', $userData['email'])->first();
                    $duplicateUsername = User::where('username', $userData['username'])->first();
                    
                    if ($duplicateEmail) {
                        Log::warning('Duplicate email found', ['email' => $userData['email']]);
                        // Modify email to avoid conflict
                        $userData['email'] = 'duplicate_' . $userData['email'];
                    }
                    
                    if ($duplicateUsername) {
                        Log::warning('Duplicate username found', ['username' => $userData['username']]);
                        // Modify username to avoid conflict
                        $userData['username'] = 'dup_' . $userData['username'];
                    }
                    
                    User::create([
                        'id' => $userData['id'],
                        'name' => $userData['name'],
                        'email' => $userData['email'],
                        'username' => $userData['username'],
                        'phone' => $userData['phone'] ?? null,
                        'website' => $userData['website'] ?? null
                    ]);
                    $created++;
                }
            } catch (\Exception $e) {
                Log::error('Failed to store user', [
                    'user' => $userData['id'] ?? 'unknown',
                    'error' => $e->getMessage(),
                    'trace' => $e->getTraceAsString()
                ]);
                $failed++;
            }
        }
        
        Log::info('User storage complete', [
            'created' => $created,
            'updated' => $updated,
            'failed' => $failed
        ]);
        
        return [
            'created' => $created,
            'updated' => $updated,
            'failed' => $failed
        ];
    }
} 