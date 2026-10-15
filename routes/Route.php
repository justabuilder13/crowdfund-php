<?php

namespace App\Routes;

use App\Providers\View;

// Petit routeur: garde la liste des routes, trouve celle qui correspond
// à l'URL et à la méthode HTTP, puis appelle Contrôleur@méthode.
class Route
{
    private static array $routes = [];

    // Ajoute une route GET.
    public static function get(string $url, string $controller): void
    {
        self::$routes[] = ['url' => $url, 'controller' => $controller, 'method' => 'GET'];
    }

    // Ajoute une route POST.
    public static function post(string $url, string $controller): void
    {
        self::$routes[] = ['url' => $url, 'controller' => $controller, 'method' => 'POST'];
    }

    // Trouve la route qui correspond et appelle l'action du contrôleur.
    public static function dispatch(): void
    {
        $urlSegments = explode('?', $_SERVER['REQUEST_URI'], 2);
        $urlPath = rtrim($urlSegments[0], '/') ?: '/';
        $method = $_SERVER['REQUEST_METHOD'];

        // Les paramètres de l'URL (ex: ?id=3) deviennent un tableau.
        $queryParams = [];
        if (isset($urlSegments[1])) {
            parse_str($urlSegments[1], $queryParams);
        }

        foreach (self::$routes as $route) {
            $routePath = rtrim(BASE . $route['url'], '/') ?: '/';

            if ($routePath === $urlPath && $route['method'] === $method) {
                [$controllerName, $methodName] = explode('@', $route['controller']);
                $controllerClass = 'App\\Controllers\\' . $controllerName;
                $controller = new $controllerClass();

                if ($method === 'GET') {
                    $controller->$methodName($queryParams);
                } else {
                    $controller->$methodName($_POST, $queryParams);
                }
                return;
            }
        }

        // Aucune route trouvée.
        http_response_code(404);
        View::render('error', ['msg' => '404 - Page not found']);
    }
}
