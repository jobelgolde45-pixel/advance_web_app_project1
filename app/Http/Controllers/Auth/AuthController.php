<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Account;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Str;
use Carbon\Carbon;
use Illuminate\Auth\Events\PasswordReset;
use Illuminate\Support\Facades\DB;

class AuthController extends Controller
{
    /**
     * Register a new user
     */
    public function register(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'user_type' => 'required|in:Tenant,Owner',
            'firstname' => 'required|string|max:100',
            'middlename' => 'nullable|string|max:100',
            'lastname' => 'required|string|max:100',
            'extension_name' => 'nullable|string|max:50',
            'email' => 'required|email|unique:accounts,email',
            'mobile_number' => 'required|string|max:20|unique:accounts,mobile_number',
            'username' => 'required|string|max:50|unique:accounts,username',
            'password' => 'required|string|min:8|confirmed',
            'password_confirmation' => 'required|string|min:8',
            'province' => 'required|string|max:100',
            'municipality' => 'required|string|max:100',
            'barangay' => 'required|string|max:100',
            'zipcode' => 'required|string|max:10',
            'house_name' => 'nullable|string|max:255',
            'dti_permit' => 'required_if:user_type,Owner|file|mimes:jpeg,png,jpg,pdf|max:5120',
            'terms_agreed' => 'required|accepted',
        ], [
            'dti_permit.required_if' => 'DTI/Business Permit is required for property owners',
            'terms_agreed.required' => 'You must agree to the terms and conditions',
            'terms_agreed.accepted' => 'You must agree to the terms and conditions',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        DB::beginTransaction();
        try {
            // Generate unique user ID
            $userId = $this->generateUserId($request->user_type);
            
            // Handle DTI permit upload for owners
            $dtiPermitPath = null;
            if ($request->user_type === 'Owner' && $request->hasFile('dti_permit')) {
                $file = $request->file('dti_permit');
                $fileName = 'dti_' . $userId . '_' . time() . '.' . $file->getClientOriginalExtension();
                $dtiPermitPath = $file->storeAs('dti-permits', $fileName, 'public');
            }

            // Create user account
            $user = Account::create([
                'user_id' => $userId,
                'firstname' => $request->firstname,
                'middlename' => $request->middlename,
                'lastname' => $request->lastname,
                'extension_name' => $request->extension_name,
                'email' => $request->email,
                'mobile_number' => $request->mobile_number,
                'username' => $request->username,
                'password' => Hash::make($request->password),
                'province' => $request->province,
                'municipality' => $request->municipality,
                'barangay' => $request->barangay,
                'zipcode' => $request->zipcode,
                'house_name' => $request->house_name,
                'dti_permit' => $dtiPermitPath ? basename($dtiPermitPath) : null,
                'user_type' => $request->user_type,
                'date_registered' => Carbon::now()->toDateString(),
                'status' => 'Pending', // New registrations need admin approval
                'view' => 'No',
            ]);

            // For owners, create default amenities
            if ($request->user_type === 'Owner') {
                $this->createDefaultAmenities($userId);
            }

            // Send registration notification to admin (in real app, this would be an email or notification)
            $this->sendRegistrationNotification($user);

            DB::commit();

            // Generate token for immediate login after registration
            $token = $user->createToken('auth_token')->plainTextToken;

            return response()->json([
                'success' => true,
                'message' => 'Registration successful! Your account is pending admin approval.',
                'data' => [
                    'user' => $user,
                    'access_token' => $token,
                    'token_type' => 'Bearer',
                ],
                'note' => 'Your account will be activated after admin approval. You will receive a notification once approved.'
            ], 201);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Registration failed',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Login user
     */
    public function login(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'login' => 'required|string', // Can be email, username, or mobile number
            'password' => 'required|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        // Determine login field
        $loginField = $this->getLoginField($request->login);
        
        $credentials = [
            $loginField => $request->login,
            'password' => $request->password,
        ];

        // Attempt authentication
        if (!auth()->attempt($credentials)) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid credentials'
            ], 401);
        }

        $user = Account::where($loginField, $request->login)->first();

        // Check if account is approved
        if ($user->status !== 'Approved') {
            auth()->logout();
            
            $statusMessage = $user->status === 'Pending' 
                ? 'Your account is pending admin approval.'
                : ($user->status === 'Rejected' 
                    ? 'Your account has been rejected. Please contact support.'
                    : 'Your account is ' . strtolower($user->status));
            
            return response()->json([
                'success' => false,
                'message' => $statusMessage,
                'status' => $user->status
            ], 403);
        }

        // Update last seen/view
        $user->updateLastSeen();

        // Generate token
        $token = $user->createToken('auth_token')->plainTextToken;

        // Log login activity (in real app, you'd have an activity log table)
        // ActivityLog::create([
        //     'user_id' => $user->user_id,
        //     'action' => 'login',
        //     'ip_address' => $request->ip(),
        //     'user_agent' => $request->userAgent(),
        // ]);

        return response()->json([
            'success' => true,
            'message' => 'Login successful',
            'data' => [
                'user' => $user->load('profileInfo'),
                'access_token' => $token,
                'token_type' => 'Bearer',
            ]
        ]);
    }

    /**
     * Logout user
     */
    public function logout(Request $request)
    {
        $user = $request->user();
        
        // Log logout activity
        // ActivityLog::create([
        //     'user_id' => $user->user_id,
        //     'action' => 'logout',
        //     'ip_address' => $request->ip(),
        // ]);
        
        // Revoke all tokens
        $user->tokens()->delete();
        
        // Alternative: Revoke only current token
        // $request->user()->currentAccessToken()->delete();
        
        return response()->json([
            'success' => true,
            'message' => 'Logged out successfully'
        ]);
    }

    /**
     * Get authenticated user profile
     */
    public function profile(Request $request)
    {
        $user = $request->user();
        
        // Load relationships
        $user->load(['profileInfo']);
        
        // Add computed statistics based on user type
        $userData = $user->toArray();
        
        if ($user->isOwner()) {
            $userData['owner_statistics'] = $user->owner_statistics;
            $userData['accommodations'] = $user->accommodations_with_availability;
        } elseif ($user->isTenant()) {
            $userData['tenant_statistics'] = $user->tenant_statistics;
        }
        
        $userData['unread_notifications_count'] = $user->unread_notifications_count;
        
        return response()->json([
            'success' => true,
            'data' => $userData
        ]);
    }

    /**
     * Send password reset link
     */
    public function forgotPassword(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email|exists:accounts,email',
        ], [
            'email.exists' => 'We could not find a user with that email address.'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        // We will send the password reset link to this user. Once we have attempted
        // to send the link, we will examine the response then see the message we
        // need to show to the user. Finally, we'll send out a proper response.
        $status = Password::sendResetLink(
            $request->only('email')
        );

        if ($status === Password::RESET_LINK_SENT) {
            return response()->json([
                'success' => true,
                'message' => 'Password reset link has been sent to your email.'
            ]);
        } else {
            return response()->json([
                'success' => false,
                'message' => 'Unable to send password reset link. Please try again later.'
            ], 500);
        }
    }

    /**
     * Reset password
     */
    public function resetPassword(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'token' => 'required',
            'email' => 'required|email|exists:accounts,email',
            'password' => 'required|string|min:8|confirmed',
            'password_confirmation' => 'required|string|min:8',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        // Here we will attempt to reset the user's password. If it is successful we
        // will update the password on an actual user model and persist it to the
        // database. Otherwise we will parse the error and return the response.
        $status = Password::reset(
            $request->only('email', 'password', 'password_confirmation', 'token'),
            function ($user, $password) {
                $user->forceFill([
                    'password' => Hash::make($password)
                ])->save();

                event(new PasswordReset($user));
            }
        );

        if ($status === Password::PASSWORD_RESET) {
            return response()->json([
                'success' => true,
                'message' => 'Password has been reset successfully.'
            ]);
        } else {
            return response()->json([
                'success' => false,
                'message' => 'Invalid or expired reset token.'
            ], 400);
        }
    }

    /**
     * Change password (authenticated)
     */
    public function changePassword(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'current_password' => 'required|string',
            'new_password' => 'required|string|min:8|confirmed',
            'new_password_confirmation' => 'required|string|min:8',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        $user = $request->user();

        // Check current password
        if (!Hash::check($request->current_password, $user->password)) {
            return response()->json([
                'success' => false,
                'message' => 'Current password is incorrect'
            ], 400);
        }

        // Check if new password is same as old password
        if (Hash::check($request->new_password, $user->password)) {
            return response()->json([
                'success' => false,
                'message' => 'New password cannot be same as current password'
            ], 400);
        }

        // Update password
        $user->password = Hash::make($request->new_password);
        $user->save();

        // Log password change activity
        // ActivityLog::create([
        //     'user_id' => $user->user_id,
        //     'action' => 'password_changed',
        //     'ip_address' => $request->ip(),
        // ]);

        // Send notification email (in real app)
        // Mail::to($user->email)->send(new PasswordChanged($user));

        // Revoke all tokens (optional - for security)
        // $user->tokens()->delete();

        return response()->json([
            'success' => true,
            'message' => 'Password changed successfully'
        ]);
    }

    /**
     * Verify email (for future implementation)
     */
    public function verifyEmail(Request $request)
    {
        // This would verify email via token
        // For now, return placeholder
        return response()->json([
            'success' => true,
            'message' => 'Email verification endpoint',
            'note' => 'Email verification will be implemented in future version'
        ]);
    }

    /**
     * Resend verification email (for future implementation)
     */
    public function resendVerificationEmail(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email|exists:accounts,email',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        // This would resend verification email
        return response()->json([
            'success' => true,
            'message' => 'Verification email resent (placeholder)',
            'note' => 'Email verification will be implemented in future version'
        ]);
    }

    /**
     * Check if email/username/mobile is available
     */
    public function checkAvailability(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'field' => 'required|in:email,username,mobile_number',
            'value' => 'required|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        $field = $request->field;
        $value = $request->value;
        
        // Additional validation based on field
        $rules = [];
        switch ($field) {
            case 'email':
                $rules['value'] = 'email';
                break;
            case 'mobile_number':
                $rules['value'] = 'regex:/^[0-9]{10,15}$/';
                break;
            case 'username':
                $rules['value'] = 'regex:/^[a-zA-Z0-9_@.-]+$/';
                break;
        }
        
        $fieldValidator = Validator::make(['value' => $value], $rules);
        if ($fieldValidator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $fieldValidator->errors()
            ], 422);
        }

        $exists = Account::where($field, $value)->exists();

        return response()->json([
            'success' => true,
            'available' => !$exists,
            'message' => $exists 
                ? ucfirst(str_replace('_', ' ', $field)) . ' is already taken'
                : ucfirst(str_replace('_', ' ', $field)) . ' is available'
        ]);
    }

    /**
     * Refresh token
     */
    public function refreshToken(Request $request)
    {
        $user = $request->user();
        
        // Revoke current token
        $request->user()->currentAccessToken()->delete();
        
        // Create new token
        $token = $user->createToken('auth_token')->plainTextToken;
        
        return response()->json([
            'success' => true,
            'data' => [
                'access_token' => $token,
                'token_type' => 'Bearer',
            ]
        ]);
    }

    /**
     * Get login field type (email, username, or mobile)
     */
    private function getLoginField($login)
    {
        if (filter_var($login, FILTER_VALIDATE_EMAIL)) {
            return 'email';
        }
        
        if (preg_match('/^[0-9]{10,15}$/', $login)) {
            return 'mobile_number';
        }
        
        return 'username';
    }

    /**
     * Generate unique user ID
     */
    private function generateUserId($userType)
    {
        $prefix = strtolower($userType) . '_';
        $timestamp = date('YmdHis');
        $random = mt_rand(1000, 9999);
        
        return $prefix . $timestamp . $random;
    }

    /**
     * Create default amenities for new owners
     */
    private function createDefaultAmenities($ownerId)
    {
        $defaultAmenities = [
            ['name' => 'Wifi', 'icon' => 'wifi'],
            ['name' => 'Airconditioner', 'icon' => 'snowflake'],
            ['name' => 'TV', 'icon' => 'tv'],
            ['name' => 'Comfort Room', 'icon' => 'bath'],
            ['name' => 'Kitchen', 'icon' => 'utensils'],
            ['name' => 'Laundry Area', 'icon' => 'tshirt'],
            ['name' => 'Parking Space', 'icon' => 'car'],
            ['name' => '24/7 Security', 'icon' => 'shield-alt'],
        ];

        foreach ($defaultAmenities as $amenity) {
            $amenityId = 'amenity_' . strtolower(str_replace(' ', '_', $amenity['name'])) . '_' . uniqid();
            
            \App\Models\Amenity::create([
                'amenities_id' => $amenityId,
                'amenities_name' => $amenity['name'],
                'user_id' => $ownerId
            ]);
        }
    }

    /**
     * Send registration notification
     */
    private function sendRegistrationNotification($user)
    {
        // In real app, this would:
        // 1. Send email to user confirming registration
        // 2. Send notification to admin for approval
        // 3. Log registration activity
        
        // Placeholder for email sending
        // Mail::to($user->email)->send(new RegistrationConfirmation($user));
        
        // Placeholder for admin notification
        // $admins = Account::where('user_type', 'Admin')->get();
        // foreach ($admins as $admin) {
        //     Notification::create([
        //         'notification_id' => uniqid(),
        //         'notification_message' => "New {$user->user_type} registration: {$user->full_name}",
        //         'receiver' => $admin->user_id,
        //         'sender' => 'system',
        //         'seen' => 'unsee',
        //         'date_sent' => now(),
        //     ]);
        // }
        
        // Log registration
        // ActivityLog::create([
        //     'user_id' => $user->user_id,
        //     'action' => 'registered',
        //     'details' => json_encode(['user_type' => $user->user_type]),
        //     'ip_address' => request()->ip(),
        // ]);
    }

    /**
     * Verify mobile number via OTP (for future implementation)
     */
    public function sendMobileOtp(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'mobile_number' => 'required|regex:/^[0-9]{10,15}$/|exists:accounts,mobile_number',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        // Generate OTP (in real app, use SMS service)
        $otp = rand(100000, 999999);
        $expiresAt = Carbon::now()->addMinutes(10);
        
        // Store OTP in cache
        cache()->put('mobile_otp_' . $request->mobile_number, [
            'otp' => $otp,
            'expires_at' => $expiresAt,
        ], $expiresAt);

        // In real app, send SMS here
        // SMS::send($request->mobile_number, "Your OTP is: {$otp}. Valid for 10 minutes.");

        return response()->json([
            'success' => true,
            'message' => 'OTP sent to mobile number',
            'note' => 'In production, OTP would be sent via SMS. Demo OTP: ' . $otp,
            'expires_in' => 10, // minutes
        ]);
    }

    /**
     * Verify mobile OTP
     */
    public function verifyMobileOtp(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'mobile_number' => 'required|regex:/^[0-9]{10,15}$/|exists:accounts,mobile_number',
            'otp' => 'required|string|size:6',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        $cacheKey = 'mobile_otp_' . $request->mobile_number;
        $otpData = cache()->get($cacheKey);

        if (!$otpData) {
            return response()->json([
                'success' => false,
                'message' => 'OTP expired or not found'
            ], 400);
        }

        if ($otpData['otp'] != $request->otp) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid OTP'
            ], 400);
        }

        // OTP verified successfully
        cache()->forget($cacheKey);
        
        // Mark mobile as verified (you'd need a verified_mobile field in accounts table)
        // Account::where('mobile_number', $request->mobile_number)
        //     ->update(['mobile_verified_at' => now()]);

        return response()->json([
            'success' => true,
            'message' => 'Mobile number verified successfully'
        ]);
    }

    /**
     * Admin login (special endpoint for admins)
     */
    public function adminLogin(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'login' => 'required|string',
            'password' => 'required|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        $loginField = $this->getLoginField($request->login);
        
        $credentials = [
            $loginField => $request->login,
            'password' => $request->password,
        ];

        if (!auth()->attempt($credentials)) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid credentials'
            ], 401);
        }

        $user = Account::where($loginField, $request->login)->first();

        // Check if user is admin
        if (!$user->isAdmin()) {
            auth()->logout();
            return response()->json([
                'success' => false,
                'message' => 'Access denied. Admin only.'
            ], 403);
        }

        // Check if admin account is approved
        if ($user->status !== 'Approved') {
            auth()->logout();
            return response()->json([
                'success' => false,
                'message' => 'Admin account is ' . strtolower($user->status)
            ], 403);
        }

        // Update last seen
        $user->updateLastSeen();

        // Generate token
        $token = $user->createToken('admin_token')->plainTextToken;

        return response()->json([
            'success' => true,
            'message' => 'Admin login successful',
            'data' => [
                'user' => $user->load('profileInfo'),
                'access_token' => $token,
                'token_type' => 'Bearer',
                'permissions' => $this->getAdminPermissions($user),
            ]
        ]);
    }

    /**
     * Get admin permissions
     */
    private function getAdminPermissions($user)
    {
        // Define permissions based on user role
        // In a real app, you'd have a roles/permissions system
        $permissions = [
            'view_users' => true,
            'manage_users' => true,
            'view_accommodations' => true,
            'manage_accommodations' => true,
            'view_reservations' => true,
            'manage_reservations' => true,
            'view_reports' => true,
            'system_settings' => true,
        ];

        return $permissions;
    }

    /**
     * Login as different user (Admin only - for testing/support)
     */
    public function loginAsUser(Request $request)
    {
        $admin = $request->user();
        
        if (!$admin->isAdmin()) {
            return response()->json([
                'success' => false,
                'message' => 'Admin access required'
            ], 403);
        }

        $validator = Validator::make($request->all(), [
            'user_id' => 'required|exists:accounts,user_id',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        $targetUser = Account::find($request->user_id);

        // Check if target user is approved
        if ($targetUser->status !== 'Approved') {
            return response()->json([
                'success' => false,
                'message' => 'Target user account is ' . strtolower($targetUser->status)
            ], 400);
        }

        // Log admin login-as action
        // ActivityLog::create([
        //     'user_id' => $admin->user_id,
        //     'action' => 'login_as',
        //     'details' => json_encode(['target_user_id' => $targetUser->user_id]),
        //     'ip_address' => $request->ip(),
        // ]);

        // Generate token for target user
        $token = $targetUser->createToken('impersonation_token')->plainTextToken;

        return response()->json([
            'success' => true,
            'message' => 'Logged in as user',
            'data' => [
                'user' => $targetUser,
                'access_token' => $token,
                'token_type' => 'Bearer',
                'impersonated_by' => $admin->user_id,
                'note' => 'This is an impersonation session. Log out to return to admin account.'
            ]
        ]);
    }

    /**
     * Get current session info
     */
    public function sessionInfo(Request $request)
    {
        $user = $request->user();
        $token = $request->bearerToken();
        
        $sessionInfo = [
            'user_id' => $user->user_id,
            'user_type' => $user->user_type,
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),
            'token_created_at' => optional($request->user()->currentAccessToken())->created_at,
            'abilities' => optional($request->user()->currentAccessToken())->abilities ?? [],
        ];

        return response()->json([
            'success' => true,
            'data' => $sessionInfo
        ]);
    }
}