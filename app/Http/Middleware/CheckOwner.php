<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class CheckOwner
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = Auth::user();
        
        if ($user->user_type !== 'Owner') {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized. Owner access required.'
            ], 403);
        }

        return $next($request);
    }
}