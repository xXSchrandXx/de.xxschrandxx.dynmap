<?php

namespace wcf\data\dynmap\external\markerfiles;

use wcf\data\dynmap\external\DynmapDatabaseObject;

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
}
