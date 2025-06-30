<?php

namespace wcf\util;

use wcf\system\WCF;

class DynmapUtil
{
    public static function hasAccesToWorld(string $worldName): bool
    {
        return true; // TODO
    }

    public static function hasAccesToMap(string $worldName, string $mapName): bool
    {
        return true; // TODO
    }

    public static function removeProtrectedWorlds(array $worlds): array
    {
        $newworlds = [];
        foreach($worlds as $w) {
            if (static::hasAccesToWorld($w['name'])) {
                $newworlds[] = static::removeProtectedMaps($w);
            }
        }
        return $newworlds;
    }

    public static function removeProtectedMaps(array $world): array
    {
        $newmaps = [];
        foreach($world['maps'] as $m) {
            if (static::hasAccesToMap($world['name'], $m['name'])) {
                $newmaps[] = $m;
            }
        }
        $world['maps'] = $newmaps;
        return $world;
    }

    public static function removeHiddenPlayers(array $players): array
    {
        if (WCF::getSession()->checkPermissions(['mod.minecraft.dynmap.canSeeAllPlayers'])) {
            return $players;
        }
        return [];
    }
}
