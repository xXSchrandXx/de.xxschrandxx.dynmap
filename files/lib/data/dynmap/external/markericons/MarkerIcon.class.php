<?php

namespace wcf\data\dynmap\external\markericons;

use wcf\data\dynmap\external\DynmapDatabaseObject;

/**
 * @property-read $IconName
 * @property-read $Image
 */
class MarkerIcon extends DynmapDatabaseObject
{
    /**
     * @inheritDoc
     */
    protected static $databaseTableName = 'MarkerIcons';
}
