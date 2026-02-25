<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Route as RouteFacade;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

Artisan::command('generate:openapi', function () {
    $this->comment('Generating OpenAPI spec...');

    $routes = collect(RouteFacade::getRoutes())->filter(function ($route) {
        // Only include API routes
        return str_starts_with($route->uri(), 'api/');
    });

    $paths = [];

    foreach ($routes as $route) {
        $uri = $route->uri();

        // normalize to OpenAPI path (keep Laravel path params as {param})
        $path = '/'.ltrim($uri, '/');

        $methods = array_filter($route->methods(), fn($m) => $m !== 'HEAD');

        foreach ($methods as $method) {
            $methodLower = strtolower($method);

            $operation = [
                'summary' => $route->getActionName() ?: $route->uri(),
                'responses' => [
                    '200' => ['description' => 'Successful response'],
                ],
            ];

            // parameters from URI
            preg_match_all('/\{([^}]+)\}/', $uri, $matches);
            if (! empty($matches[1])) {
                $operation['parameters'] = [];
                foreach ($matches[1] as $param) {
                    $operation['parameters'][] = [
                        'name' => $param,
                        'in' => 'path',
                        'required' => true,
                        'schema' => ['type' => 'string'],
                    ];
                }
            }

            // security if route uses sanctum
            $action = $route->getAction();
            $middleware = $action['middleware'] ?? [];
            if (is_string($middleware)) {
                $middleware = [$middleware];
            }

            foreach ($middleware as $m) {
                if (str_contains($m, 'auth:sanctum') || str_contains($m, 'auth')) {
                    $operation['security'] = [['sanctum' => []]];
                    break;
                }
            }

            $paths[$path][ $methodLower ] = $operation;
        }
    }

    $openapi = [
        'openapi' => '3.0.0',
        'info' => [
            'title' => config('app.name', 'Tonercity API'),
            'version' => 'v1',
        ],
        'servers' => [
            ['url' => config('app.url', '/')],
        ],
        'paths' => $paths,
        'components' => [
            'securitySchemes' => [
                'sanctum' => [
                    'type' => 'http',
                    'scheme' => 'bearer',
                ],
            ],
        ],
    ];

    $outputDir = public_path('docs');
    if (! file_exists($outputDir)) {
        mkdir($outputDir, 0755, true);
    }

    file_put_contents($outputDir.DIRECTORY_SEPARATOR.'openapi.json', json_encode($openapi, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES));

    // Redoc HTML viewer
    $html = <<<'HTML'
<!doctype html>
<html>
  <head>
    <meta charset="utf-8" />
    <title>Tonercity API Docs</title>
    <meta name="viewport" content="width=device-width, initial-scale=1" />
  </head>
  <body>
    <redoc spec-url="openapi.json"></redoc>
    <script src="https://cdn.redoc.ly/redoc/latest/bundles/redoc.standalone.js"></script>
  </body>
</html>
HTML;

    file_put_contents($outputDir.DIRECTORY_SEPARATOR.'index.html', $html);

    $this->comment('OpenAPI generated to: public/docs/openapi.json');
    $this->comment('Docs viewer: public/docs/index.html');

})->purpose('Generate a minimal OpenAPI spec from registered API routes');
