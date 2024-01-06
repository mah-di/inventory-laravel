<?php

namespace App\Http\Middleware;

use Closure;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;
use Symfony\Component\HttpFoundation\Response;

class VerifiedEmail
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        try {
            $verifiedAt = $request->header('verifiedAt');

            if ($verifiedAt === null) throw new Exception("Please verify your email to gain access.");

            return $next($request);

        } catch (Exception $exception) {
            return Redirect::route('verify.email.view');
        }
    }
}
