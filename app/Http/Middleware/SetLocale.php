<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class SetLocale
{
    public function handle(Request $request, Closure $next): Response
    {
        $allowed = ['pt', 'en', 'es'];
        $locale  = session('locale', config('app.locale', 'pt'));

        if (! in_array($locale, $allowed)) {
            $locale = 'pt';
        }

        app()->setLocale($locale);

        return $next($request);
    }
}
