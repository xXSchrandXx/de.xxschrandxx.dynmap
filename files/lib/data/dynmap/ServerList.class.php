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
            dynmapHost IS NOT NULL AND dynmapHost != ? AND
            dynmapPort IS NOT NULL AND dynmapPort != ? AND
            dynmapUser IS NOT NULL AND dynmapUser != ? AND
            dynmapPassword IS NOT NULL AND dynmapPassword != ? AND
            dynmapName IS NOT NULL AND dynmapName != ?', ['', '', '', '', '']);
    }
}
