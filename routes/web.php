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

$router->post('/user/register', 'UserController@register');
$router->post('/login', 'UserController@login');
$router->get('/user/get/{id}', 'UserController@getUser');
$router->get('/user/search', 'UserController@searchUsers');
$router->get('/user/info', 'UserController@info');

$router->put('/friend/set/{user_id}', 'FriendController@addFriend');
$router->put('/friend/delete/{user_id}', 'FriendController@removeFriend');

$router->post('/post/create', 'PostController@createPost');
$router->put('/post/update/{id}', 'PostController@updatePost');
$router->put('/post/delete/{id}', 'PostController@deletePost');
$router->get('/post/get/{id}', 'PostController@getPost');
$router->get('/post/feed', 'PostController@getFriendsFeed');

$router->post('/dialog/{user_id}/send', 'DialogController@sendMessage');
$router->get('/dialog/{user_id}/list', 'DialogController@listDialog');
