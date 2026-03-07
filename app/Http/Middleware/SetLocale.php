<?php

namespace App\Http\Middleware;

use App\Models\AppSetting;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class SetLocale
{
    public function handle(Request $request, Closure $next): Response
    {
        $allowed = ['pt', 'en', 'es'];

        try {
            // Lê do banco — persiste entre sessões e reinicializações do app
            $locale = AppSetting::get('locale', config('app.locale', 'pt'));
        }
        catch (\Throwable) {
            // Banco ainda não migrado ou inacessível — usa o padrão
            $locale = config('app.locale', 'pt');
        }

        if (!in_array($locale, $allowed)) {
            $locale = 'pt';
        }

        app()->setLocale($locale);

        return $next($request);
    }
}