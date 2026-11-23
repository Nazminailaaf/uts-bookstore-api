<?php

/** @var \Laravel\Lumen\Routing\Router $router */

// ---------- PUBLIC (tanpa autentikasi) : hanya membaca data ----------
$router->get('/authors', 'AuthorsController@index');
$router->get('/authors/{id}', 'AuthorsController@show');
$router->get('/books', 'BooksController@index');   // ?author_id=1 untuk filter
$router->get('/books/{id}', 'BooksController@show');

// ---------- PROTECTED (Basic Auth) : INSERT, UPDATE, DELETE ----------
$router->group(['middleware' => 'basicauth'], function () use ($router) {
    $router->post('/authors', 'AuthorsController@store');
    $router->put('/authors/{id}', 'AuthorsController@update');
    $router->delete('/authors/{id}', 'AuthorsController@destroy');

    $router->post('/books', 'BooksController@store');
    $router->put('/books/{id}', 'BooksController@update');
    $router->delete('/books/{id}', 'BooksController@destroy');
});
