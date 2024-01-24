<?php

namespace App\Helpers;
use App\Models\User;
use Exception;
use Firebase\JWT\JWT;
use Firebase\JWT\Key;

class JWTHelper
{

    public static function generateToken(User $user, string $type = 'auth.token'): string
    {
        $userID = $user->id;
        $ownerID = $user->owner_id ?? $userID;
        $email = $user->email;
        $verifiedAt = $user->verified_at;
        $roles = $user->roles->pluck('slug')->all();

        $key = env('JWT_SECRET');

        $expTime = ($type === 'password.reset.token') ? time()+60*10 : time()+60*60*24*180;

        $payload = [
            'iss' => 'OSTAD_POS',
            'iat' => time(),
            'exp' => $expTime,
            'email' => $email,
            'userID' => $userID,
            'ownerID' => $ownerID,
            'roles' => $roles,
            'verifiedAt' => $verifiedAt,
            'type' => $type
        ];

        return JWT::encode($payload, $key, 'HS256');
    }

    public static function verifyToken(?string $token): ?object
    {
        try
        {
            if ($token == null) throw new Exception("No Auth Token Provided");

            $key = env('JWT_SECRET');

            $payload = JWT::decode($token, new Key($key, 'HS256'));

            if ($payload->type !== 'auth.token') throw new Exception("Unauthorized request.", 401);

            return $payload;

        } catch (Exception $exception)
        {
            return null;
        }
    }

    public static function verifyPasswordResetToken(?string $token): ?object
    {
        try
        {
            if ($token == null) throw new Exception("No Auth Token Provided");

            $key = env('JWT_SECRET');

            $payload = JWT::decode($token, new Key($key, 'HS256'));

            if ($payload->type !== 'password.reset.token') throw new Exception("Unauthorized request.", 401);

            return $payload;

        } catch (Exception $exception)
        {
            return null;
        }
    }

}
