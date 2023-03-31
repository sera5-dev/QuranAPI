<?php

/** @var \Laravel\Lumen\Routing\Router $router */

/*
|--------------------------------------------------------------------------
| Application Routes
|--------------------------------------------------------------------------
|
| Here is where you can register all of the routes for an application.
| It is a breeze. Simply tell Lumen the URIs it should respond to
| and give it the Closure to call when that URI is requested.
|
*/

$router->get('/', function () use ($router) {
    return $router->app->version();
});

$router->group(['prefix' => "v1"], function () use ($router) {
    $randomOrNumber = "\brandom\b|\bfirst\b|\blast\b|[0-9]+";

    $router->get('/', "QuranV1Controller@index");
    $router->get('pages_count', "QuranV1Controller@pages_count");
    $router->get('test', "QuranV1Controller@test");
    $router->get('surah', "QuranV1Controller@surah_list");
    $router->get("page/{page:$randomOrNumber}", "QuranV1Controller@page");
    $router->get("surah/{surah:$randomOrNumber}", "QuranV1Controller@surah");
    $router->get("surah/{surah:$randomOrNumber}/{verse:$randomOrNumber}", "QuranV1Controller@verse");
});
