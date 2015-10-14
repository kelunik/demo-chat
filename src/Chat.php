<?php

namespace Kelunik\Demo;

use Aerys\Request;
use Aerys\Response;
use Aerys\Websocket;
use Aerys\Websocket\Endpoint;

class Chat implements Websocket {
    /** @var Endpoint */
    private $endpoint;
    private $connections;
    private $ips;

    public function onStart(Websocket\Endpoint $endpoint) {
        $this->endpoint = $endpoint;
        $this->connections = [];
        $this->ips = [];
    }

    public function onHandshake(Request $request, Response $response) {
        $origin = $request->getHeader("origin");

        if ($origin !== "http://localhost:1337") {
            $response->setStatus(403);
            $response->send("<h1>origin not allowed</h1>");

            return null;
        }

        return $request->getConnectionInfo()["client_addr"];
    }

    public function onOpen(int $clientId, $handshakeData) {
        $this->connections[$clientId] = $handshakeData;
        $this->ips[$handshakeData][$clientId] = true;
    }

    public function onData(int $clientId, Websocket\Message $msg) {
        $body = yield $msg; // buffer all data
        $ip = $this->connections[$clientId];

        if (preg_match("~@(\\d+\\.\\d+\\.\\d+\\.\\d+)\\b~", $body, $match)) {
            list($all, $receiver) = $match;

            $payload = $ip . " (private): " . substr($body, strlen($all));
            $clients = array_keys($this->ips[$receiver] ?? []);

            if (!empty($clients)) {
                $this->endpoint->broadcast($payload, $clients);
            }
        } else {
            $payload = $ip . ": " . $body;
            $this->endpoint->broadcast($payload);
        }
    }

    public function onClose(int $clientId, int $code, string $reason) {
        $ip = $this->connections[$clientId];

        unset($this->connections[$clientId]);
        unset($this->ips[$ip][$clientId]);

        if (empty($this->ips[$ip])) {
            unset($this->ips[$ip]);
        }
    }

    public function onStop() {
        $this->connections = [];
        $this->ips = [];
    }
}