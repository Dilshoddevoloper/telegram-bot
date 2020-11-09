<?php

namespace App\Http\Middleware;

use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken as Middleware;

class VerifyCsrfToken extends Middleware
{
    /**
     * The URIs that should be excluded from CSRF verification.
     *
     * @var array
     */
    protected $except = [
        '1452191230:AAEesg6F0TpkzTs7YRmVRqDFsl3ZpzZY4a4',
        'telegram'
    ];
}
