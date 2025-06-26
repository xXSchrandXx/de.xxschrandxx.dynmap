<?php

use wcf\event\endpoint\ControllerCollecting;
use wcf\system\endpoint\controller\xxschrandxx\dynmap\GetConfiguration;
use wcf\system\endpoint\controller\xxschrandxx\dynmap\GetUpdate;
use wcf\system\endpoint\controller\xxschrandxx\dynmap\GetMarker;
use wcf\system\endpoint\controller\xxschrandxx\dynmap\GetTile;
use wcf\system\event\EventHandler;

return static function (): void {
    EventHandler::getInstance()->register(
        ControllerCollecting::class,
        static function (ControllerCollecting $event) {
            /*
             * configuration: 'standalone/MySQL_configuration.php'
             * update: 'standalone/MySQL_update.php?world={world}&ts={timestamp}'
             * sendmessage: 'standalone/MySQL_sendmessage.php'
             * login: 'standalone/MySQL_login.php'
             * register: 'standalone/MySQL_register.php'
             * tiles: 'standalone/MySQL_tiles.php?tile='
             * markers: 'standalone/MySQL_markers.php?marker='
            */
            $event->register(new GetConfiguration());
            $event->register(new GetUpdate());
            $event->register(new GetTile());
            $event->register(new GetMarker());
        }
    );
};
