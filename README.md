# demo-chat

Introduction to Aerys WebSockets.

## Running

```bash
# Install dependencies.
composer install

# Run Aerys in debug mode.
# Debug mode ensures we're only using a single worker. Otherwise we might end
# up with two websocket clients connecting to two different workers.
# Later you would replace the current broadcasting with something like Redis, so
# you can have as many workers as you'd like.
vendor/bin/aerys -c aerys.php -d
```
