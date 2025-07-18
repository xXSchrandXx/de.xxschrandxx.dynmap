<?php

namespace wcf\data\dynmap\external\maps;

use wcf\data\DatabaseObject;
use wcf\data\dynmap\external\DynmapDatabaseObject;

/**
 * @property-read $ID
 * @property-read $WorldID
 * @property-read $MapID
 * @property-read $Variant
 * @property-read $ServerID (always 0)
 */
class Map extends DynmapDatabaseObject
{
    /**
     * @inheritDoc
     */
    protected static $databaseTableName = 'Maps';

    /**
     * @inheritDoc
     */
    protected static $databaseTableIndexIsIdentity = true;

    /**
     * @inheritDoc
     */
    protected static $databaseTableIndexName = 'ID';

    /**
     * @inheritDoc
     */
    public function getObjectID()
    {
        DatabaseObject::getObjectID();
    }
}
