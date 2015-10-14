<?php

use Aerys\Host;
use Aerys\Router;
use Kelunik\Demo\Chat;
use function Aerys\root;
use function Aerys\websocket;

$router = (new Router())
    ->route("GET", "ws", websocket(new Chat));

$root = root(__DIR__ . "/public");

(new Host)
    ->name("localhost")
    ->expose("*", 1337)
    ->use($router)
    ->use($root);