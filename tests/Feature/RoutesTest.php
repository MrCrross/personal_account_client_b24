<?php

use Illuminate\Support\Facades\Route;

it('у каждого маршрута существует метод контроллера', function () {
    $broken = [];
    foreach (Route::getRoutes() as $route) {
        $action = $route->getAction('uses');
        if (! is_string($action) || ! str_contains($action, '@')) {
            continue;
        }
        [$controller, $method] = explode('@', $action);
        if (! method_exists($controller, $method)) {
            $broken[] = "{$route->uri()} -> {$action}";
        }
    }

    expect($broken)->toBe([]);
});

it('каждая Inertia-страница, которую рендерит маршрут, существует', function () {
    $missing = [];
    foreach (Route::getRoutes() as $route) {
        $action = $route->getAction('uses');
        if ($action instanceof Closure) {
            $reflection = new ReflectionFunction($action);
        } elseif (is_string($action) && str_contains($action, '@') && method_exists(...explode('@', $action))) {
            $reflection = new ReflectionMethod(...explode('@', $action));
        } else {
            continue;
        }

        // Исходник обработчика маршрута — только его строки, а не весь файл
        $lines = file($reflection->getFileName());
        $source = implode('', array_slice(
            $lines,
            $reflection->getStartLine() - 1,
            $reflection->getEndLine() - $reflection->getStartLine() + 1
        ));

        preg_match_all("/Inertia::render\(\s*'([^']+)'/", $source, $matches);
        foreach ($matches[1] as $page) {
            if (! file_exists(resource_path("js/pages/{$page}.vue"))) {
                $missing[] = "{$route->uri()}: {$page}";
            }
        }
    }

    expect($missing)->toBe([]);
});
