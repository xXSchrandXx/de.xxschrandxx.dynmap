<?php

namespace wcf\util;

use wcf\data\user\minecraft\MinecraftUser;
use wcf\data\user\minecraft\MinecraftUserList;
use wcf\data\user\minecraft\UserToMinecraftUser;
use wcf\data\user\User;
use wcf\data\user\UserProfile;
use wcf\system\WCF;

class DynmapUtil
{
    public static function hasAccesToServer(int $serverID): bool
    {
        $access = \explode("\n", StringUtil::unifyNewlines(WCF::getSession()->getPermission('user.minecraft.dynmap.noAccess')));

        return !in_array($serverID, $access);
    }

    public static function hasAccesToWorld(int $serverID, string $worldName): bool
    {
        $access = \explode("\n", StringUtil::unifyNewlines(WCF::getSession()->getPermission('user.minecraft.dynmap.noAccess')));

        if (!self::hasAccesToServer($serverID)) {
            return false;
        }

        return !in_array($serverID . ":" . $worldName, $access);
    }

    public static function hasAccesToMap(int $serverID, string $worldName, string $mapName): bool
    {
        $access = \explode("\n", StringUtil::unifyNewlines(WCF::getSession()->getPermission('user.minecraft.dynmap.noAccess')));

        if (!self::hasAccesToWorld($serverID, $worldName)) {
            return false;
        }

        return !in_array($serverID . ":" . $worldName . ":" . $mapName, $access);
    }

    public static function removeProtrectedWorlds(int $serverID, array $worlds): array
    {
        $newworlds = [];
        foreach($worlds as $world) {
            if (static::hasAccesToWorld($serverID, $world['name'])) {
                $newworlds[] = static::removeProtectedMaps($serverID, $world, $world['maps']);
            }
        }
        return $newworlds;
    }

    public static function removeProtectedMaps(int $serverID, array $world, array $maps): array
    {
        $newmaps = [];
        foreach($maps as $map) {
            if (static::hasAccesToMap($serverID, $world['name'], $map['name'])) {
                $newmaps[] = $map;
            }
        }
        $world['maps'] = $newmaps;
        return $world;
    }

    public static function removeHiddenPlayers(array $players): array
    {
        if (WCF::getSession()->getPermission('mod.minecraft.dynmap.canSeeAllPlayers')) {
            return $players;
        }

        $accounts = [];
        foreach ($players as $player) {
            $accounts[] = $player['account'];
        }
        $linkedList = new MinecraftUserList();
        $linkedList->getConditionBuilder()->add('minecraftName IN (?)', [$accounts]);
        $linkedList->sqlSelects = UserToMinecraftUser::getDatabaseTableAlias() . ".userID";
        $linkedList->sqlJoins = "RIGHT JOIN " . UserToMinecraftUser::getDatabaseTableName() . " " . UserToMinecraftUser::getDatabaseTableAlias() . "
                                 ON " . MinecraftUser::getDatabaseTableAlias() . "." . MinecraftUser::getDatabaseTableIndexName() . "=" . UserToMinecraftUser::getDatabaseTableAlias() . "." . UserToMinecraftUser::getDatabaseTableIndexName();
        $linkedList->readObjects();
        $linked = $linkedList->getObjects();

        $hidden = [];
        /** @var \wcf\data\user\minecraft\MinecraftUser $minecraftUser */
        foreach ($linked as $minecraftUser) {
            $userProfile = new UserProfile(new User($minecraftUser->userID));
            if (!$userProfile->isAccessible('canViewDynmap', WCF::getUser()->userID)) {
                $hidden[] = $minecraftUser->minecraftName;
            }
        }
        $filtered = array_filter($players, function($player) use ($hidden) {
            return !in_array($player['account'], $hidden, true);
        });

        return $filtered;
    }

    public static function canUseWebchat(): bool
    {
        if (!DYNMAP_GENERAL_WEBCHAT_ENABLED) {
            return false;
        }
        return WCF::getSession()->getPermission("user.minecraft.dynmap.canUseWebchat");
    }
}
