<?php

namespace wcf\data\dynmap\maps;

use wcf\data\dynmap\DynmapDatabaseObject;

/**
 * @property-read $ID
 * @property-read $WorldID
 * @property-read $MapID
 * @property-read $Variant
 * @property-read $ServerID
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
}
