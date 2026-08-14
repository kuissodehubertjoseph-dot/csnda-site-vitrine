<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

$app = Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        //
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        //
    })->create();

// Déploiement manuel InfinityFree : le Gestionnaire de fichiers n'autorise
// les uploads que dans htdocs/, donc laravel_app/ est un sous-dossier de
// htdocs/ (htdocs/laravel_app/) au lieu d'être à côté. Si ce dossier
// s'appelle "laravel_app" et que son parent contient index.php (= htdocs),
// on sert les assets depuis ce parent au lieu de laravel_app/public.
$deployedPublicPath = dirname($app->basePath());
if (basename($app->basePath()) === 'laravel_app' && file_exists($deployedPublicPath.'/index.php')) {
    $app->usePublicPath($deployedPublicPath);
}

return $app;
