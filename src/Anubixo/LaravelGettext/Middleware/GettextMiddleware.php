<?php

namespace Anubixo\LaravelGettext\Middleware;

use Anubixo\LaravelGettext\LaravelGettext;
use Closure;
use Illuminate\Http\Request;

class GettextMiddleware
{
    /**
    * Handle an incoming request.
    *
    * @param Request $request
    * @param Closure $next
    * @return mixed
    */
    public function handle(Request $request, Closure $next): mixed
    {
        /**
         * The package need to be initialized, the locale will
         * be available after first method call. If you have
         * async calls in your project, this filter starts the 
         * locale environment before each request.
         */
        LaravelGettext::getLocale();

        return $next($request);
    }
}
