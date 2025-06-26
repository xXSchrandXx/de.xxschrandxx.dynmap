<?php

namespace wcf\data\dynmap\markericons;

use wcf\data\dynmap\DynmapDatabaseObject;

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

    /**
     * @inheritDoc
     */
    protected static $databaseTableIndexIsIdentity = false;
}
