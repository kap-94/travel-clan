<?php

/**
 * Front controller
 *
 * PHP @version 7.4
 */

/**
 * Composer
 */
require dirname(__DIR__) . '/vendor/autoload.php';


/**
 * Error and Exception handling
 */
error_reporting(E_ALL);
set_error_handler('Core\Error::errorHandler');
set_exception_handler('Core\Error::exceptionHandler');


/**
 * Sessions
 */
session_start();

/**
 * Routing
 */
$router = new Core\Router();

// Add the routes
$router->add('', ['controller' => 'Home', 'action' => 'index']);
$router->add('admin', ['controller' => 'Admin', 'action' => 'index']);

$router->add('login', ['controller' => 'Login', 'action' => 'new']);
$router->add('logout', ['controller' => 'Login', 'action' => 'destroy']);
$router->add('password/reset/{token:[\da-f]+}', ['controller' => 'Password', 'action' => 'reset']); // Reg expression hexadecimal value for token
$router->add('signup/activate/{token:[\da-f]+}', ['controller' => 'Signup', 'action' => 'activate']); // Reg expression hexadecimal value for token

$router->add('articles', ['controller' => 'Articles', 'action' => 'show-all']);
$router->add('category/{category:[a-z]+}', ['controller' => 'Articles', 'action' => 'show-by-category']);
$router->add('article/{id:\d+}', ['controller' => 'Articles', 'action' => 'show']);

$router->add('{controller}/{action}');
$router->add('{controller}/{id:\d+}/{action}');

$router->dispatch($_SERVER['QUERY_STRING']);
