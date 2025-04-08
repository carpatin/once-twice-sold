# Set up the project

Git clone the project.

Go and copy from `websocket/.env.example` to `websocket/.env`

Change the `websocket/websocket.php` so that the started container does not exit immediately
with error for missing libs. For that place a `sleep(100000)` on the second line.

Start clean the docker containers:

```
> bin/console clean-start
```

This brings up three containers:

- `websocket`:
    - a container running the `websocket/websocket.php` entry point
    - exposes `localhost:9502`
- `caddy`:
    - a container running Caddy for delivering the static HTML pages
    - these pages are in `webapp/public`
    - exposes them under `localhost:80`
    - has support for executing PHP files (through `webapp`, not used at the moment)
- `webapp`:
    - a container running PHP-FPM
    - added for future developments

Run the composer install:

```
> bin/console websocket-composer install
```

Change the `websocket/websocket.php` back, so remove the `sleep(100000)` on the second line.

Restart the containers:

```
> bin/console stop && bin/console start
```

The websocket server should be running properly at this point.

# Experiment with the auction application

In one browser tab navigate to `localhost/seller.html`

In a second browser tab navigate to `localhost/bidder.html`

In a third browser tab navigate to `localhost/bidder.html`

Start an auction on the Seller page.

Go to Bidder pages and notice the auction appeared.

Place bids alternately on the two Bidder pages.

At the end of the period set for the auction notice the alerts displayed on each of the tabs.

# Enjoy