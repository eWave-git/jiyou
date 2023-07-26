<?php

namespace App\Http\Middleware;

use \App\Session\Manager\Login as SessionManagerLogin;

class RequireManagerLogin {

    public function handle($request, $next) {
        if (!SessionManagerLogin::isLogged()) {
            $request->getRouter()->redirect('/manager/login');
        }

        return $next($request);
    }
}