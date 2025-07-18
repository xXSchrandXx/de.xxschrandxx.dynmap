<?php

namespace wcf\data\dynmap\external;

use wcf\data\DatabaseObject;
use wcf\data\dynmap\Server;
use wcf\data\minecraft\Minecraft;

class DynmapDatabaseObject extends DatabaseObject
{
    /**
     * @inheritDoc
     */
    protected static $databaseTableIndexIsIdentity = false;

    /**
     * @var Server
     */
    protected $server;

    /**
     * @inheritDoc
     * @param $id minecraftID from Server
     */
    public function __construct(?int $id, ?array $row = null, ?self $object = null)
    {
        if ($object !== null) {
            $row = $object->data;
        }

        if ($row === null) {
            throw new \InvalidArgumentException('data cannot be null.');
        }

        $this->handleData($row);
    }

    /**
     * @inheritDoc
     */
    public static function getDatabaseTableName()
    {
        return static::$databaseTableName;
    }

    /**
     * @inheritDoc
     */
    public function getObjectID()
    {
        $this->server->getObjectID();
    }

    public function setServer(Server $server)
    {
        $this->server = $server;
    }
}
