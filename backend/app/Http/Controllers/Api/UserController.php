<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use App\Services\UserService;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Log;

class UserController extends Controller
{
    protected $userService;

    public function __construct(UserService $userService)
    {
        $this->userService = $userService;
    }

    /**
     * Get all users with optional search
     * 
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function index(Request $request)
    {
        $query = User::query();
        
        if ($request->has('search') && !empty($request->search)) {
            $searchTerm = $request->search;
            $query->where(function($q) use ($searchTerm) {
                $q->where('name', 'like', "%{$searchTerm}%")
                  ->orWhere('email', 'like', "%{$searchTerm}%")
                  ->orWhere('username', 'like', "%{$searchTerm}%");
            });
        }
        
        $users = $query->orderBy('created_at', 'desc')->get();
        
        return response()->json([
            'status' => 'success',
            'data' => $users
        ]);
    }

    /**
     * Get a user by ID
     * 
     * @param string $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function show($id)
    {
        $user = User::find($id);
        
        if (!$user) {
            return response()->json([
                'status' => 'error',
                'message' => 'User not found'
            ], 404);
        }
        
        return response()->json([
            'status' => 'success',
            'data' => $user
        ]);
    }

    /**
     * Create a new user
     * 
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:100',
            'email' => 'required|email|max:100|unique:users,email',
            'username' => 'required|string|max:50|unique:users,username',
            'phone' => 'nullable|string|max:20',
            'website' => 'nullable|string|max:100'
        ]);
        
        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }
        
        $user = User::create($request->only([
            'name', 'email', 'username', 'phone', 'website'
        ]));
        
        return response()->json([
            'status' => 'success',
            'message' => 'User created successfully',
            'data' => $user
        ], 201);
    }

    /**
     * Update a user
     * 
     * @param \Illuminate\Http\Request $request
     * @param string $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(Request $request, $id)
    {
        $user = User::find($id);
        
        if (!$user) {
            return response()->json([
                'status' => 'error',
                'message' => 'User not found'
            ], 404);
        }
        
        $validator = Validator::make($request->all(), [
            'name' => 'sometimes|required|string|max:100',
            'email' => 'sometimes|required|email|max:100|unique:users,email,'.$user->id,
            'username' => 'sometimes|required|string|max:50|unique:users,username,'.$user->id,
            'phone' => 'nullable|string|max:20',
            'website' => 'nullable|string|max:100'
        ]);
        
        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }
        
        $user->update($request->only([
            'name', 'email', 'username', 'phone', 'website'
        ]));
        
        return response()->json([
            'status' => 'success',
            'message' => 'User updated successfully',
            'data' => $user
        ]);
    }

    /**
     * Delete a user
     * 
     * @param string $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy($id)
    {
        Log::info('Attempting to delete user', ['id' => $id]);
        
        $user = User::find($id);
        
        if (!$user) {
            Log::warning('User not found for deletion', ['id' => $id]);
            return response()->json([
                'status' => 'error',
                'message' => 'User not found'
            ], 404);
        }
        
        try {
            $userName = $user->name;
            $userId = $user->id;
            
            $deleted = $user->delete();
            
            Log::info('User deletion result', [
                'id' => $userId,
                'name' => $userName,
                'deleted' => $deleted
            ]);
            
            if (!$deleted) {
                throw new \Exception('Failed to delete user');
            }
            
            return response()->json([
                'status' => 'success',
                'message' => 'User deleted successfully',
                'id' => $userId
            ]);
        } catch (\Exception $e) {
            Log::error('Error deleting user', [
                'id' => $id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to delete user: ' . $e->getMessage()
            ], 500);
        }
    }
    
    /**
     * Fetch users from external API and store them
     * 
     * @return \Illuminate\Http\JsonResponse
     */
    public function fetch()
    {
        try {
            $result = $this->userService->fetchUsersFromApi();
            
            if (!$result['success']) {
                return response()->json([
                    'status' => 'error',
                    'message' => $result['error']
                ], 500);
            }
            
            $storedResult = $this->userService->storeFetchedUsers($result['data']);
            
            return response()->json([
                'status' => 'success',
                'message' => 'Users fetched and stored successfully',
                'data' => [
                    'fetched_count' => count($result['data']),
                    'created' => $storedResult['created'],
                    'updated' => $storedResult['updated'],
                    'failed' => $storedResult['failed']
                ]
            ]);
        } catch (\Exception $e) {
            \Log::error('Error in fetch method: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString()
            ]);
            
            return response()->json([
                'status' => 'error',
                'message' => 'An error occurred while processing your request: ' . $e->getMessage()
            ], 500);
        }
    }
} 