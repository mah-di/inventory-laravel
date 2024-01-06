<?php

namespace App\Http\Middleware;

use App\Helpers\JWTHelper;
use App\Models\User;
use Closure;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use Symfony\Component\HttpFoundation\Response;

class TokenAuthenticate
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        try {
            $token = $request->header('token') ?? $request->cookie('token');

            if ($request->routeIs('password.reset') or $request->routeIs('password.reset.view')) $payload = JWTHelper::verifyPasswordResetToken($token);

            else $payload = JWTHelper::verifyToken($token);

            if ($payload == null) throw new Exception("Unauthorized Request.");

            $request->headers->set('id', $payload->userID);
            $request->headers->set('email', $payload->email);
            $request->headers->set('verifiedAt', $payload->verifiedAt);

            return $next($request);

        } catch (Exception $exception) {
            if ($request->header('token'))

                return response()->json([
                    'status' => 'fail',
                    'message' => $exception->getMessage()
                ], 401);

            return Redirect::route('login.view');
        }
    }
}
