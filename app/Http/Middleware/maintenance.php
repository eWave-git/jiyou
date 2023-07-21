<?php
namespace App\Http\Middleware;

class maintenance {
    public function handle ($request, $next) {
        if (getenv('MAINTENANCE') == 'true') {
            throw new \Exception("Página em manutenção. Tente mais tarde");
        }
        return $next($request);
    }
}