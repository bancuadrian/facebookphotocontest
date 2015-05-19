<?php namespace App\Http\Middleware;

use Closure;

class FacebookMiddleware {

	/**
	 * Handle an incoming request.
	 *
	 * @param  \Illuminate\Http\Request  $request
	 * @param  \Closure  $next
	 * @return mixed
	 */
	public function handle($request, Closure $next)
	{
        \Facebook\FacebookSession::setDefaultApplication(env('FACEBOOK_APP_ID'),env('FACEBOOK_APP_SECRET'));
		return $next($request);
	}

}
