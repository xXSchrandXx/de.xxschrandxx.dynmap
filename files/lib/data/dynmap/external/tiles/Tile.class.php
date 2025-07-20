<?php

namespace wcf\data\dynmap\external\tiles;

use wcf\data\dynmap\external\DynmapDatabaseObject;

/**
 * @property-read $MapID
 * @property-read $x
 * @property-read $y
 * @property-read $zoom
 * @property-read $HashCode
 * @property-read $LastUpdate
 * @property-read $Format [0=PNG, 1=JPEG, 2=WEBP]
 * @property-read $Image
 * @property-read $NewImage
 */
class Tile extends DynmapDatabaseObject
{
    /**
     * @inheritDoc
     */
    protected static $databaseTableName = 'Tiles';
}
