<?php

namespace App\Enums;

class EmailTokenVerification
{
    public const TOKEN_REQUIRED = 'Token verification is required';
    public const VALID_TOKEN = 'Token is valid';
    public const TOKEN_NOT_FOUND = 'Token not found';
    public const TOKEN_EXPIRED = 'Token has expired';
    public const INVALID_TOKEN = 'Invalid token';

}
