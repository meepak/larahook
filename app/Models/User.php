<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use PragmaRX\Google2FA\Google2FA;

class User extends Authenticatable
{
    protected $fillable = ['username', 'email', 'two_fa_secret'];

    public static function registerUser($email)
    {
        $username = explode('@', $email)[0];

        // Generate a 2FA secret key
        $google2fa = app(Google2FA::class);
        $twoFASecret = $google2fa->generateSecretKey();

        $user = static::firstOrCreate([
            'email' => $email
        ], [
            'username' => $username,
            'two_fa_secret' => $twoFASecret,
        ]);

        return $user;
    }
}
