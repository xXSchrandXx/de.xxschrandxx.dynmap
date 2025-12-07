<?php

namespace wcf\data\dynmap;

use BadMethodCallException;
use Negotiation\Exception\InvalidArgument;
use wcf\data\DatabaseObject;
use wcf\data\DatabaseObjectDecorator;
use wcf\data\dynmap\external\faces\FaceList;
use wcf\data\dynmap\external\maps\Map;
use wcf\data\dynmap\external\maps\MapList;
use wcf\data\dynmap\external\markerfiles\MarkerFileList;
use wcf\data\dynmap\external\markericons\MarkerIconList;
use wcf\data\dynmap\external\standalonefiles\StandaloneFileList;
use wcf\data\dynmap\external\tiles\Tile;
use wcf\data\dynmap\external\tiles\TileList;
use wcf\data\media\ViewableMedia;
use wcf\data\minecraft\Minecraft;
use wcf\data\user\minecraft\MinecraftUser;
use wcf\data\user\minecraft\MinecraftUserList;
use wcf\data\user\minecraft\UserToMinecraftUser;
use wcf\data\user\User;
use wcf\data\user\UserProfile;
use wcf\system\database\exception\DatabaseException;
use wcf\system\database\MySQLDatabase;
use wcf\system\event\EventHandler;
use wcf\system\WCF;
use wcf\util\JSON;
use wcf\util\StringUtil;

/**
 * @inheritDoc
 * @package  WoltLabSuite\Core\Data\Dynmap
 *
 * @property-read ?int $image
 * @property-read string $description
 * @property-read string $dynmapHost
 * @property-read int $dynmapPort
 * @property-read string $dynmapUser
 * @property-read string $dynmapPassword
 * @property-read string $dynmapName
 * @property-read bool $webchatEnabled
 * @property-read int $webchatInterval
 */
class Server extends DatabaseObjectDecorator
{
    /**
     * supported database schema version of dynmap
     */
    public const SCHEMAVERSION = 6;

    /**
     * @inheritDoc
     */
    protected static $baseClass = Minecraft::class;

    /**
     * @var MySQLDatabase
     */
    private $dynmapObj;

    /**
     * @var array
     */
    public $config;

    /**
     * @var array
     */
    public $worlds = [];

    /**
     * @inheritDoc
     * @throws InvalidArgument if connection is not configured
     */
    public function __construct(DatabaseObject $object)
    {
        parent::__construct($object);

        if (!isset($this->dynmapHost) || empty($this->dynmapHost) ||
            !isset($this->dynmapUser) || empty($this->dynmapUser) ||
            !isset($this->dynmapPassword) || empty($this->dynmapPassword) ||
            !isset($this->dynmapName) || empty($this->dynmapName) ||
            !isset($this->dynmapPort) || empty($this->dynmapPort)
        ) {
            throw new InvalidArgument('Dynmap not supported.');
        }

        // create database connection
        $this->dynmapObj = new MySQLDatabase(
            $this->dynmapHost,
            $this->dynmapUser,
            $this->dynmapPassword,
            $this->dynmapName,
            $this->dynmapPort,
            false,
            false
        );

        $standaloneFileList = $this->getStandaloneFileList();
        $standaloneFileList->readObjects();
        $standaloneFiles = $standaloneFileList->getObjects();
        foreach ($standaloneFiles as $standaloneFile) {
            if (!str_ends_with($standaloneFile->FileName, 'json')) {
                // Skipping files like access. Permissions are handeled by WSC
                continue;
            } else if ($standaloneFile->FileName == 'dynmap_webchat.json') {
                // This only reads. Write like shown in \wcf\system\endpoint\controller\xxschrandxx\dynmap\PostSendMessage
                continue;
            } else if ($standaloneFile->FileName == 'dynmap_config.json') {
                $this->config = JSON::decode($standaloneFile->Content, true);
            } else {
                if (preg_match('/^dynmap_(.+)\.json$/', $standaloneFile->FileName, $matches)) {
                    $this->worlds[$matches[1]] = JSON::decode($standaloneFile->Content, true);
                }
            }
        }
        // Override config values for WCF usage
        $this->config['login-enabled'] = false;
        $this->config['loginrequired'] = false;
        $this->config['allowwebchat'] = $this->webchatEnabled;
        $this->config['webchat-requires-login'] = false;
        $this->config['webchat-interval'] = $this->webchatInterval;
        $this->config['joinmessage'] = WCF::getLanguage()->getDynamicVariable('wcf.endpoint.dynmap.joinmessage');
        $this->config['msg-hiddennamejoin'] = WCF::getLanguage()->getDynamicVariable('wcf.endpoint.dynmap.msg-hiddennamejoin');
        $this->config['quitmessage'] = WCF::getLanguage()->getDynamicVariable('wcf.endpoint.dynmap.quitmessage');
        $this->config['msg-hiddennamequit'] = WCF::getLanguage()->getDynamicVariable('wcf.endpoint.dynmap.msg-hiddennamequit');
        $this->config['spammessage'] = WCF::getLanguage()->getDynamicVariable('wcf.endpoint.dynmap.spammessage');
        $this->config['msg-chatnotallowed'] = WCF::getLanguage()->getDynamicVariable('wcf.endpoint.dynmap.msg-chatnotallowed');
        $this->config['msg-players'] = WCF::getLanguage()->getDynamicVariable('wcf.endpoint.dynmap.msg-players');
        $this->config['msg-maptypes'] = WCF::getLanguage()->getDynamicVariable('wcf.endpoint.dynmap.msg-maptypes');

        // Modify config or worlds with event
        EventHandler::getInstance()->fireAction($this, 'construct');
    }

    /**
     * @return ?int
     */
    public function getImageID(): ?int
    {
        return $this->image;
    }

    /**
     * @return ?ViewableMedia
     */
    public function getImage(): ?ViewableMedia
    {
        return ViewableMedia::getMedia($this->image);
    }

    /**
     * @return string description
     */
    public function getDescription(): string
    {
        return $this->description;
    }

    /**
     * Checks weather schemaVersion is supported
     * @return bool
     */
    public function checkSchemaVersion(): bool
    {
        $supported = false;
        try {
            $statement = $this->getDB()->prepareStatement('SELECT level FROM SchemaVersion');
            $statement->execute();
            if ($statement->fetchSingleColumn() == self::SCHEMAVERSION) {
                $supported = true;
            }
        } catch (DatabaseException $e) {
        }
        return $supported;
    }

    /**
     * Returns servers MySQLDatabase
     * @return MySQLDatabase
     */
    public function getDB(): MySQLDatabase
    {
        return $this->dynmapObj;
    }

    /**
     * Returns all worlds
     * @return array
     */
    public function getWorlds(): array
    {
        return $this->getConfigValue('worlds');
    }

    /**
     * Return given world data
     * @param string $worldName
     * @return array
     */
    public function getWorld(string $worldName): ?array
    {
        foreach ($this->getWorlds() as $world) {
            if ($world['name'] == $worldName) {
                return $world;
            }
        }
        return null;
    }

    /**
     * Returns all worlddatas
     * @return array
     */
    public function getWorldDatas(): array
    {
        return $this->worldDatas;
    }

    /**
     * Return given world data
     * @param string $worldName
     * @return array
     */
    public function getWorldData(string $worldName): ?array
    {
        return $this->worlds[$worldName] ?? null;
    }

    /**
     * Return the whole config
     * @return array
     */
    public function getConfig(): ?array
    {
        return $this->config;
    }

    /**
     * Get the a config value
     * @return mixed
     */
    public function getConfigValue($key)
    {
        return $this->config[$key] ?? null;
    }

    /**
     * Checks weather active Session can use webchat
     * @return bool
     */
    public function canUseWebchat(): bool
    {
        if (!$this->webchatEnabled) {
            return false;
        }
        return WCF::getSession()->getPermission("user.minecraft.dynmap.canUseWebchat");
    }

    /**
     * Checks weather active Session has access to this server
     * @return bool 
     */
    public function hasAccesToServer(): bool
    {
        $access = \explode("\n", StringUtil::unifyNewlines(WCF::getSession()->getPermission('user.minecraft.dynmap.noAccess')));

        return !in_array($this->minecraftID, $access);
    }

    /**
     * Checks weather active Session has access to given world
     * @param string $wordName name of the world
     * @return bool
     */
    public function hasAccesToWorld(string $worldName): bool
    {
        $access = \explode("\n", StringUtil::unifyNewlines(WCF::getSession()->getPermission('user.minecraft.dynmap.noAccess')));

        if (!self::hasAccesToServer($this->minecraftID)) {
            return false;
        }

        return !in_array($this->minecraftID . ":" . $worldName, $access);
    }

    /**
     * Checks weather active Session has access to given map in given world
     * @param string $wordName name of the world
     * @param string $mapName name of the map
     * @return bool
     */
    public function hasAccesToMap(string $worldName, string $mapName): bool
    {
        $access = \explode("\n", StringUtil::unifyNewlines(WCF::getSession()->getPermission('user.minecraft.dynmap.noAccess')));

        if (!self::hasAccesToWorld($worldName)) {
            return false;
        }

        return !in_array($this->minecraftID . ":" . $worldName . ":" . $mapName, $access);
    }

    /**
     * Get servers worlds without protected world and maps
     * @return array
     */
    public function getAccessableWorlds(): array
    {
        $newworlds = [];
        foreach($this->getConfigValue('worlds') as $world) {
            if ($this->hasAccesToWorld($world['name'])) {
                $newworlds[] = $this->getAccessableMaps($world['name'], $world['maps']);
            }
        }
        return $newworlds;
    }

    /**
     * Get servers world without protected maps
     * @return array
     * @throws BadMethodCallException when world does not exist
     */
    public function getAccessableMaps(string $worldName): array
    {
        $world =  $this->getWorld($worldName);
        if ($world === null) {
            throw new BadMethodCallException('World does not exist.');
        }
        $newmaps = [];
        foreach($world['maps'] as $map) {
            if ($this->hasAccesToMap($world['name'], $map['name'])) {
                $newmaps[] = $map;
            }
        }
        $world['maps'] = $newmaps;
        return $world;
    }

    /**
     * Get visable players
     * @return array
     * @throws BadMethodCallException when world does not exist
     */
    public function getVisablePlayers(string $worldName): array
    {
        $world =  $this->getWorldData($worldName);
        if ($world === null || !array_key_exists('players', $world)) {
            throw new BadMethodCallException('World does not exist.');
        }
        $players = $world['players'];
        if (WCF::getSession()->getPermission('mod.minecraft.dynmap.canSeeAllPlayers')) {
            return $players;
        }

        if (empty($players)) {
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

        if (empty($linked)) {
            return $players;
        }

        $hidden = [];
        $ignoredUserIds = [];
        $currentUser = WCF::getSession()->getUser();
        if (isset($currentUser)) {
            $currentUserProfile = new UserProfile($currentUser);
            $ignoredUserIds = $currentUserProfile->getIgnoredUsers();
        }
        /** @var \wcf\data\user\minecraft\MinecraftUser $minecraftUser */
        foreach ($linked as $minecraftUser) {
            $userProfile = new UserProfile(new User($minecraftUser->userID));
            // check weather someone is not accessible
            if (!$userProfile->isAccessible('canViewDynmap', $userProfile->userID)) {
                if (!in_array($minecraftUser->minecraftName, $hidden)) {
                    $hidden[] = $minecraftUser->minecraftName;
                    continue;
                }
            }
            if (isset($currentUser)) {
                // check weather the current user is getting ignored by someone else
                if (in_array($currentUser->userID, $userProfile->getIgnoredByUsers())) {
                    if (!in_array($minecraftUser->minecraftName, $hidden)) {
                        $hidden[] = $minecraftUser->minecraftName;
                        continue;
                    }
                }
                // check weather the current user ignores someone
                if (in_array($minecraftUser->userID, $ignoredUserIds)) {
                    if (!in_array($minecraftUser->minecraftName, $hidden)) {
                        $hidden[] = $minecraftUser->minecraftName;
                        continue;
                    }
                }
            }
        }
        $filtered = array_filter($players, function($player) use ($hidden) {
            return !in_array($player['account'], $hidden, true);
        });

        return $filtered;
    }

    /**
     * Get predefined List for this server
     * @return FaceList
     */
    public function getFaceList(): FaceList
    {
        return new FaceList($this);
    }
    
    /**
     * Get predefined List for this server
     * @return MapList
     */
    public function getMapList(): MapList
    {
        return new MapList($this);
    }

    /**
     * Get predefined List for this server
     * @return MarkerFileList
     */
    public function getMarkerFileList(): MarkerFileList
    {
        return new MarkerFileList($this);
    }

    /**
     * Get predefined List for this server
     * @return MarkerIconList
     */
    public function getMarkerIconList(): MarkerIconList
    {
        return new MarkerIconList($this);
    }

    /**
     * Get predefined List for this server
     * @return StandaloneFileList
     */
    public function getStandaloneFileList(): StandaloneFileList
    {
        return new StandaloneFileList($this);
    }
    /**
     * Get predefined List for this server
     * @return TileList
     */
    public function getTileList(): TileList
    {
        return new TileList($this);
    }

    public function getTile(string $worldName, string $prefix, string $variant, int $x, int $y, int $zoom): ?Tile
    {
        $tileList = $this->getTileList();
        $tileList->sqlSelects = Map::getDatabaseTableAlias() . '.WorldID,' . 
                                Map::getDatabaseTableAlias() . '.MapID,' .
                                Map::getDatabaseTableAlias() . '.Variant';
        $tileList->sqlJoins = 'JOIN ' . Map::getDatabaseTableName() . ' ' . Map::getDatabaseTableAlias() . ' ' . 
                              'ON ' . Map::getDatabaseTableAlias() . '.ID = ' . Tile::getDatabaseTableAlias() . '.MapID';
        $tileList->getConditionBuilder()->add(Map::getDatabaseTableAlias() . '.WorldID = ? AND ' .
                                              Map::getDatabaseTableAlias() . '.MapID = ? AND ' .
                                              Map::getDatabaseTableAlias() . '.Variant = ? AND ' .
                                              'x = ? AND ' .
                                              'y = ? AND ' .
                                              'zoom = ?',
                                               [$worldName, $prefix, $variant,
                                                $x, $y, $zoom]);
        $tileList->readObjects();
        return $tileList->getSingleObject();
    }
}
