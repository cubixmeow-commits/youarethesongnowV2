<?php

declare(strict_types=1);

namespace Yatsn\Http;

final class Router
{
    /** @var list<array{methods: list<string>, pattern: string, handler: callable}> */
    private array $routes = [];

    public function get(string $pattern, callable $handler): void
    {
        $this->add(['GET'], $pattern, $handler);
    }

    public function post(string $pattern, callable $handler): void
    {
        $this->add(['POST'], $pattern, $handler);
    }

    public function patch(string $pattern, callable $handler): void
    {
        $this->add(['PATCH'], $pattern, $handler);
    }

    public function put(string $pattern, callable $handler): void
    {
        $this->add(['PUT'], $pattern, $handler);
    }

    public function delete(string $pattern, callable $handler): void
    {
        $this->add(['DELETE'], $pattern, $handler);
    }

    /** @param list<string> $methods */
    public function add(array $methods, string $pattern, callable $handler): void
    {
        $this->routes[] = [
            'methods' => array_map('strtoupper', $methods),
            'pattern' => $pattern,
            'handler' => $handler,
        ];
    }

    public function dispatch(Request $request): mixed
    {
        foreach ($this->routes as $route) {
            if (!in_array($request->method, $route['methods'], true) && !($request->method === 'HEAD' && in_array('GET', $route['methods'], true))) {
                continue;
            }
            $params = $this->match($route['pattern'], $request->path);
            if ($params === null) {
                continue;
            }
            return ($route['handler'])($request, $params);
        }

        return null;
    }

    /** @return array<string, string>|null */
    private function match(string $pattern, string $path): ?array
    {
        $regex = preg_replace('#\{([a-zA-Z_][a-zA-Z0-9_]*)\}#', '(?P<$1>[^/]+)', $pattern);
        $regex = '#^' . $regex . '$#';
        if (!preg_match($regex, $path, $matches)) {
            return null;
        }
        $params = [];
        foreach ($matches as $key => $value) {
            if (!is_int($key)) {
                $params[$key] = $value;
            }
        }
        return $params;
    }
}
