<?php

namespace wcf\data\dynmap;

use wcf\data\DatabaseObjectList;
use wcf\data\minecraft\Minecraft;

/**
 * Returns only configured Dynmap MinecraftServers
 * @method Server getSingleObject()
 * @method Server[] getObjects()
 */
class ServerList extends DatabaseObjectList
{
    public $objectClassName = Minecraft::class;
    public $decoratorClassName = Server::class;

    public function __construct()
    {
        parent::__construct();
        
        $this->conditionBuilder->add('
            dbHost IS NOT NULL AND dbHost != ? AND
            dbPort IS NOT NULL AND dbPort != ? AND
            dbUser IS NOT NULL AND dbUser != ? AND
            dbPassword IS NOT NULL AND dbPassword != ? AND
            dbName IS NOT NULL AND dbName != ?', ['', '', '', '', '']);
    }
}
