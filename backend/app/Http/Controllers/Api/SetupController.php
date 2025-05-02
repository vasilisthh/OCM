<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\Schema;

class SetupController extends Controller
{
    /**
     * Initialize the database with required tables
     * 
     * @return \Illuminate\Http\JsonResponse
     */
    public function initialize()
    {
        try {
            // Check if users table exists, if not create it
            if (!Schema::hasTable('users')) {
                Schema::create('users', function ($table) {
                    $table->id();
                    $table->string('name');
                    $table->string('email')->unique();
                    $table->string('username')->unique();
                    $table->string('phone')->nullable();
                    $table->string('website')->nullable();
                    $table->timestamps();
                });
            }
            
            // Check if the table is empty
            $count = User::count();
            
            if ($count === 0) {
                // Add sample data
                $sampleUsers = [
                    [
                        'name' => 'John Doe',
                        'email' => 'john@example.com',
                        'username' => 'johndoe',
                        'phone' => '555-123-4567',
                        'website' => 'johndoe.com',
                        'created_at' => Carbon::now(),
                        'updated_at' => Carbon::now(),
                    ],
                    [
                        'name' => 'Jane Smith',
                        'email' => 'jane@example.com',
                        'username' => 'janesmith',
                        'phone' => '555-987-6543',
                        'website' => 'janesmith.com',
                        'created_at' => Carbon::now(),
                        'updated_at' => Carbon::now(),
                    ],
                ];
                
                foreach ($sampleUsers as $userData) {
                    User::create($userData);
                }
            }
            
            return response()->json([
                'status' => 'success',
                'message' => 'Database initialized successfully',
                'data' => [
                    'table_created' => true,
                    'sample_data_added' => ($count === 0),
                    'user_count' => User::count()
                ]
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to initialize database',
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ], 500);
        }
    }
} 