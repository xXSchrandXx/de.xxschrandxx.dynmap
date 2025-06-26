<?php

namespace wcf\data\dynmap\markerfiles;

use wcf\data\dynmap\DynmapDatabaseObject;

/**
 * @property-read $FileName
 * @property-read $Content JSON
 */
class MarkerFile extends DynmapDatabaseObject
{
    /**
     * @inheritDoc
     */
    protected static $databaseTableName = 'MarkerFiles';

    /**
     * @inheritDoc
     */
    protected static $databaseTableIndexIsIdentity = false;
}
