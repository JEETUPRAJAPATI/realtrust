<?php

namespace App\Http\Middleware;

use Laravel\Sanctum\PersonalAccessToken;

use Carbon\Carbon;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\Response;

class CheckTokenExpiration
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $token = $request->bearerToken();
        Log::info('CheckTokenExpiration Middleware - Token: ' . $token); // Add logging

        if ($token) {
            $accessToken = PersonalAccessToken::findToken($token);
            Log::info('CheckTokenExpiration Middleware - Access Token: ' . json_encode($accessToken)); // Add logging

            if ($accessToken) {
                $tokenExpiration = config('sanctum.expiration');
                Log::info('CheckTokenExpiration Middleware - Token Expiration: ' . $tokenExpiration); // Add logging

                if ($tokenExpiration) {
                    $expirationDate = $accessToken->created_at->addMinutes($tokenExpiration);
                    Log::info('CheckTokenExpiration Middleware - Expiration Date: ' . $expirationDate); // Add logging

                    if (Carbon::now()->greaterThan($expirationDate)) {
                        Log::info('CheckTokenExpiration Middleware - Token Expired'); // Add logging
                        return response()->json(['message' => 'Token expired'], 401);
                    }
                }
            }
        }
        return $next($request);
    }
}
