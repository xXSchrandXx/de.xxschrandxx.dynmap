<?php

namespace wcf\data\dynmap;

use wcf\data\DatabaseObject;

class DynmapDatabaseObject extends DatabaseObject
{
    /**
     * @inheritDoc
     */
    protected static $databaseTableIndexIsIdentity = false;

    /**
     * @inheritDoc
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
        throw new \BadMethodCallException('getObjectID is not supported for DDynmapDatabaseObject.');
    }
}
