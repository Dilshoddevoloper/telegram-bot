<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Log;

class OnlyMessage
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
      if($request && isset($request['message'])) {
          return $next($request);
      }
      abort(200);
    }

}
