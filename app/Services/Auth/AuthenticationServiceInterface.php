<?php

namespace App\Services\Auth;

interface AuthenticationServiceInterface
{
    public function authenticate(array $credentials);
    public function logout();
}
