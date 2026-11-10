<?php

/**
 * Routeur pour le serveur intégré de PHP, destiné au partage par tunnel
 * (connexion montante lente) : compresse les pages et les fichiers texte en
 * gzip et met les fichiers statiques en cache dans le navigateur.
 *
 * Lancement (depuis la racine du projet) :
 *   php -S 127.0.0.1:8001 -t public deploiement/serveur-tunnel.php
 */
$publicPath = dirname(__DIR__).'/public';

$uri = urldecode(parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH) ?? '');
$gzip = str_contains($_SERVER['HTTP_ACCEPT_ENCODING'] ?? '', 'gzip');

$fichier = $uri !== '/' ? realpath($publicPath.$uri) : false;

if ($fichier && str_starts_with($fichier, realpath($publicPath)) && is_file($fichier) && ! str_ends_with($fichier, '.php')) {
    $extension = strtolower(pathinfo($fichier, PATHINFO_EXTENSION));
    $types = [
        'css' => 'text/css; charset=utf-8',
        'js' => 'text/javascript; charset=utf-8',
        'mjs' => 'text/javascript; charset=utf-8',
        'json' => 'application/json',
        'svg' => 'image/svg+xml',
        'png' => 'image/png',
        'jpg' => 'image/jpeg',
        'jpeg' => 'image/jpeg',
        'webp' => 'image/webp',
        'gif' => 'image/gif',
        'ico' => 'image/x-icon',
        'woff' => 'font/woff',
        'woff2' => 'font/woff2',
        'txt' => 'text/plain; charset=utf-8',
    ];
    $compressible = in_array($extension, ['css', 'js', 'mjs', 'json', 'svg', 'txt'], true);

    $etag = '"'.md5($fichier.filemtime($fichier).filesize($fichier)).'"';
    header('ETag: '.$etag);
    header('Content-Type: '.($types[$extension] ?? mime_content_type($fichier) ?: 'application/octet-stream'));
    header('Cache-Control: '.(str_starts_with($uri, '/build/') ? 'public, max-age=31536000, immutable' : 'public, max-age=86400'));
    header('Vary: Accept-Encoding');

    if (($_SERVER['HTTP_IF_NONE_MATCH'] ?? '') === $etag) {
        http_response_code(304);

        return true;
    }

    if ($gzip && $compressible) {
        $contenu = gzencode(file_get_contents($fichier), 6);
        header('Content-Encoding: gzip');
        header('Content-Length: '.strlen($contenu));
        echo $contenu;
    } else {
        header('Content-Length: '.filesize($fichier));
        readfile($fichier);
    }

    return true;
}

file_put_contents('php://stdout', '['.date('H:i:s').'] '.$_SERVER['REQUEST_METHOD'].' '.$uri."\n");

if ($gzip) {
    ob_start('ob_gzhandler');
}

require_once $publicPath.'/index.php';
