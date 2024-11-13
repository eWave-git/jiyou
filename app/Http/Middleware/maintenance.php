<?php
namespace App\Http\Middleware;

class maintenance {
    public function handle ($request, $next) {
        if (getenv('MAINTENANCE') == 'true') {
            throw new \Exception("Maintenance page. Try it later.");
        }
        return $next($request);
    }
}