<?php
namespace App\Core;

class Router
{
    private array $routes = [];
    private array $middlewares = [];

    public function get(string $path, string|callable $handler, array $middleware = []): void
    {
        $this->routes[] = ['GET', $path, $handler, $middleware];
    }

    public function post(string $path, string|callable $handler, array $middleware = []): void
    {
        $this->routes[] = ['POST', $path, $handler, $middleware];
    }

    public function dispatch(string $method, string $uri): void
    {
        // Remove query string
        $uri = strtok($uri, '?');
        // Remove base path
        $base = rtrim(dirname($_SERVER['SCRIPT_NAME']), '/');
        $uri = '/' . ltrim(substr($uri, strlen($base)), '/');

        foreach ($this->routes as [$routeMethod, $path, $handler, $middlewares]) {
            $pattern = preg_replace('/\{(\w+)\}/', '(?P<$1>[^/]+)', $path);
            $pattern = "@^{$pattern}$@D";

            if ($routeMethod !== $method) continue;
            if (!preg_match($pattern, $uri, $matches)) continue;

            // Extract named params
            $params = array_filter($matches, fn($k) => !is_int($k), ARRAY_FILTER_USE_KEY);

            // Run middlewares
            foreach ($middlewares as $mw) {
                if (!$this->runMiddleware($mw, $params)) return;
            }

            // Run handler
            if (is_callable($handler)) {
                call_user_func_array($handler, $params);
                return;
            }

            [$controllerClass, $methodName] = explode('@', $handler);
            $fullClass = "App\\Controllers\\{$controllerClass}";
            $controller = new $fullClass();
            call_user_func_array([$controller, $methodName], $params);
            return;
        }

        // 404
        http_response_code(404);
        require ROOT . '/views/errors/404.php';
    }

    private function runMiddleware(string $mw, array $params): bool
    {
        $fullClass = "App\\Middleware\\{$mw}";
        if (class_exists($fullClass)) {
            $instance = new $fullClass();
            return $instance->handle($params);
        }
        return true;
    }
}
