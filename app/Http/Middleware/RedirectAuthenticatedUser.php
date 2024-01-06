<?php

namespace App\Http\Middleware;

use App\Helpers\JWTHelper;
use Closure;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;
use Symfony\Component\HttpFoundation\Response;

class RedirectAuthenticatedUser
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

            if (JWTHelper::verifyToken($token) !== null) throw new Exception("Can't access while logged in.");

            return $next($request);

        } catch (Exception $exception) {
            if ($request->header('token'))

                return response()->json([
                    'status' => 'fail',
                    'message' => $exception->getMessage()
                ]);

            return Redirect::route('dashboard.view');
        }
    }
}
