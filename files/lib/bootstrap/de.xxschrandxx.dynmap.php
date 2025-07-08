<?php

use wcf\event\endpoint\ControllerCollecting;
use wcf\system\endpoint\controller\xxschrandxx\dynmap\GetConfiguration;
use wcf\system\endpoint\controller\xxschrandxx\dynmap\GetUpdate;
use wcf\system\endpoint\controller\xxschrandxx\dynmap\GetMarker;
use wcf\system\endpoint\controller\xxschrandxx\dynmap\GetTile;
use wcf\system\endpoint\controller\xxschrandxx\dynmap\PostSendMessage;
use wcf\system\event\EventHandler;

return static function (): void {
    EventHandler::getInstance()->register(
        ControllerCollecting::class,
        static function (ControllerCollecting $event) {
            /*
             * login: disabled
             * register: disabled
            */
            $event->register(new GetConfiguration());
            $event->register(new GetUpdate());
            $event->register(new GetTile());
            $event->register(new GetMarker());
            $event->register(new PostSendMessage());
        }
    );
};
