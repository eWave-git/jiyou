<?php
namespace App\Http;

use app\Utils\Common;
use \Closure;
use \Exception;
use \ReflectionFunction;
use \App\Http\Middleware\Queue as MiddlewareQueue;

class Router {

    private $url = '';

    private $prefix = '';

    private $routes = [];

    private $request;

    private $contentType = 'text/html';

    public function __construct($url) {
        $this->request = new Request($this);
        $this->url = $url;
        $this->setPrefix();
    }

    public function setContentType($contentType) {
        $this->contentType = $contentType;
    }

    private function setPrefix() {
        $parseUrl = parse_url($this->url);
        $this->prefix = $parseUrl['path'] ?? '';
    }

    private function addRoute($method, $route, $params = []) {

        foreach ($params as $key => $value) {
            if ($value instanceof Closure) {
                $params['controller'] = $value;
                unset($params[$key]);
                continue;
            }
        }

        $params['middlewares'] = $params['middlewares'] ?? [];

        $params['variables'] = [];

        $patternVariable = '/{(.*?)}/';
        if (preg_match_all($patternVariable, $route, $matches)) {
            $route = preg_replace($patternVariable, '(.*?)', $route);
            $params['variables'] = $matches[1];
        }

        $patternRoute = '/^'.str_replace('/','\/', $route).'$/';
        $this->routes[$patternRoute][$method] = $params;
    }

    public function get($route, $params = []) {
        return $this->addRoute('GET', $route, $params);
    }

    public function post($route, $params = []) {
        $this->addRoute('POST', $route, $params);
    }

    public function put($route, $params = []) {
        return $this->addRoute('PUT', $route, $params);
    }

    public function delete($route, $params = []) {
        return $this->addRoute('DELETE', $route, $params);
    }

    private function getUri() {
        $uri = $this->request->getUri();
        $xUri = strlen($this->prefix) > 1 ? explode($this->prefix, $uri) : [$uri];

        return end($xUri);
    }

    private function getRoute() {
        $uri = $this->getUri();

        $httpMethod = $this->request->getHttpMethod();

        foreach ($this->routes as $patterRoute => $methods) {
            preg_match($patterRoute, $uri, $matches);

            if (preg_match($patterRoute, $uri, $matches)) {

                if (isset($methods[$httpMethod])) {
                    unset($matches[0]);

                    $keys = $methods[$httpMethod]['variables'];
                    $methods[$httpMethod]['variables'] = array_combine($keys, $matches);
                    $methods[$httpMethod]['variables']['request'] = $this->request;

                    return $methods[$httpMethod];
                }

                throw new Exception("Error Processing Request1", 405);
            }
        }

        Common::error_loc_msg("/manager/dashboard","등록된 페이지가 없습니다.");
//        throw new Exception("Error Processing Request2", 404);
    }

    public function run() {
        try {

            $route = $this->getRoute();

            if (!isset($route['controller'])) {
                throw new Exception("Error Processing Request3", 200);
            }

            $args = [];

            $reflection = new ReflectionFunction($route['controller']);
            foreach ($reflection->getParameters() as $parameter) {

                $name = $parameter->getName();
                $args[$name] = $route['variables'][$name] ?? "";
            }


            return (new MiddlewareQueue($route['middlewares'], $route['controller'], $args))->next($this->request);
        } catch (Exception $e) {
            return new Response($e->getCode(), $this->getErrorMessage($e->getMessage()),  $this->contentType);
        }
    }

    private function getErrorMessage($message) {
        switch ($this->contentType) {
            case 'application/json':
                return ['error' => $message];
                break;
            default :
                return $message;
                break;
        }
    }

    public function getCurrentUrl() {
        return $this->url.$this->getUri();
    }

    public function redirect($route) {
        $url = $this->url.$route;

        header('location: '.$url);
        exit;
    }

    public function goback(){
        if (isset($_SERVER["HTTP_REFERER"])) {
            header("Location: " . $_SERVER["HTTP_REFERER"]);
            exit;
        }
    }

}