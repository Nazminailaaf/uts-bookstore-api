<?php

namespace App\Http\Middleware;

use Closure;
use App\Helpers\XmlHelper;

class BasicAuthMiddleware
{
    public function handle($request, Closure $next)
    {
        $user = (string) $request->getUser();
        $pass = (string) $request->getPassword();

        $validUser = (string) env('BASIC_AUTH_USER');
        $validPass = (string) env('BASIC_AUTH_PASSWORD');

        if (!hash_equals($validUser, $user) || !hash_equals($validPass, $pass)) {
            return XmlHelper::response(null, 401, 'UNAUTHORIZED')
                ->header('WWW-Authenticate', 'Basic realm="Bookstore API"');
        }

        return $next($request);
    }
}