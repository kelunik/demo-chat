<?php

use Aerys\Host;
use Aerys\Router;
use Kelunik\Demo\Chat;
use function Aerys\root;
use function Aerys\websocket;

// route /ws to the websocket endpoint
// you can add more routes to this router
$router = (new Router())
    ->route("GET", "ws", websocket(new Chat));

// add document root
$root = root(__DIR__ . "/public");

// create virtual host localhost:1337
// requests will first be routed, if no route matches, the server tries to find a file in the document root
// you can add more responders or even multiple document roots to a single host
(new Host)
    ->name("localhost")
    ->expose("*", 1337)
    ->use($router)
    ->use($root);