<?php

namespace wcf\data\dynmap\tiles;

use wcf\data\dynmap\DynmapDatabaseObject;
use wcf\data\dynmap\DynmapDB;
use wcf\data\dynmap\maps\Map;

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

    /**
     * @inheritDoc
     */
    protected static $databaseTableIndexIsIdentity = false;

    public static function getTile(string $worldID, string $prefix, string $variant, int $x, int $y, int $zoom): ?Tile
    {
        $sql = "SELECT t.NewImage,t.Image,t.Format,t.HashCode,t.LastUpdate
                FROM " . Map::getDatabaseTableName() . " m 
                JOIN " . self::getDatabaseTableName() . " t 
                ON m.ID=t.MapID
                WHERE m.WorldID = ? 
                AND m.MapID = ? 
                AND m.Variant = ? 
                AND t.x = ? 
                AND t.y = ? 
                AND t.zoom = ?";
        $statement = DynmapDB::getInstance()->getDB()->prepareStatement($sql);
        $statement->execute([$worldID, $prefix, $variant, $x, $y, $zoom]);
        $row = $statement->fetchArray();
        // enforce data type 'array'
        if ($row === false) {
            $row = [];
        }
        return new static(null, $row);
    }
}
