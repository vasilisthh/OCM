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
                    'error' => $e->getMessage()
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