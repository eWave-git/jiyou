<?php
date_default_timezone_set('Asia/Seoul');

require __DIR__.'/includes/app.php';

use \App\Http\Router;

$obRouter = new Router(URL);


include __DIR__.'/routes/admin.php';

include __DIR__.'/routes/manager.php';



$obRouter->run()->sendResponse();

exit;
