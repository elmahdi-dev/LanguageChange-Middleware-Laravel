<?php

namespace App\Http\Middleware;
use Closure;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Cookie;

class LanguageChange
{
    public function handle($request, Closure $next)
    {
        // Get the language from the request, can be null.
        $language = substr($request->getRequestUri(), 1, 2);

        // if there is no language, use the one saved in the cookie or default one.
        if (empty($language)) {
            $language = Cookie::get('language') ?? config('app.locale');
            return redirect($language . '/');
        }

        App::setLocale($language);
        Cookie::queue('language', $language, 45000);

        //  this is a boolean passed with GET to activate language change
        $switch = $request->query('switch');
        // The uri is used to redirect to the same page after switching languages
        $uri = substr($request->getRequestUri(), 4);
        if($switch) {
            $clean_uri = explode('?', $uri);
            return redirect($language . '/' . $clean_uri[0]);
        }

        return $next($request);
    }
}
