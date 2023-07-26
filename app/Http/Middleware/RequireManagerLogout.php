<?php

namespace App\Http\Middleware;

use \App\Session\Manager\Login as SessionManagerLogin;

class RequireManagerLogout {

    public function handle($request, $next) {
        if (SessionManagerLogin::isLogged()) {
            $request->getRouter()->redirect('/manager');
        }

        return $next($request);
    }
}