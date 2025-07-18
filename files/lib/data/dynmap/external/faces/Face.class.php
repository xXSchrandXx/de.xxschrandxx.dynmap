<?php

namespace wcf\data\dynmap\external\faces;

use wcf\data\dynmap\external\DynmapDatabaseObject;

/**
 * @property-read $PlayerName
 * @property-read $TypeID [0="Face 8x8", 1="Face 16x16", 2="Face 32x32, 3="Body"]
 * @property-read $Image
 */
class Face extends DynmapDatabaseObject
{
    /**
     * @inheritDoc
     */
    protected static $databaseTableName = 'Faces';
}
