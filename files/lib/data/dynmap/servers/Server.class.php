<?php

namespace wcf\data\dynmap\servers;

use InvalidArgumentException;
use wcf\data\dynmap\maps\MapList;
use wcf\data\dynmap\standalonefiles\StandaloneFileList;
use wcf\util\JSON;

class Server
{
    /**
     * @var int
     */
    public $id;

    /**
     * @var array
     */
    protected $config;

    /**
     * @var array
     */
    protected $worlds = [];

    /**
     * @var Map[]
     */
    protected $maps;

    public function __construct(int $id)
    {
        $this->id = $id;

        $standaloneFileList = new StandaloneFileList();
        $standaloneFileList->getConditionBuilder()->add('ServerID = ?', [$this->id]);
        if ($standaloneFileList->countObjects() === 0) {
            throw new InvalidArgumentException('id');
        }
        $standaloneFileList->readObjects();
        $standaloneFiles = $standaloneFileList->getObjects();
        foreach ($standaloneFiles as $standaloneFile) {
            if ($standaloneFile->FileName == 'dynmap_access.php') {
                continue;
            } else if ($standaloneFile->FileName == 'dynmap_config.json') {
                $this->config = JSON::decode($standaloneFile->Content, true);
            } else {
                if (preg_match('/^dynmap_(.+)\.json$/', $standaloneFile->FileName, $matches)) {
                    $this->worlds[$matches[1]] = JSON::decode($standaloneFile->Content, true);
                }
            }
        }

        $mapList = new MapList();
        $mapList->getConditionBuilder()->add('ServerID = ?', [$this->id]);
        $mapList->readObjects();
        $this->maps = $mapList->getObjects();
    }

    public function getWorlds(): array
    {
        return $this->worlds;
    }

    public function getWorld(string $name): ?array
    {
        if (isset($this->worlds[$name])) {
            return $this->worlds[$name];
        }
        return null;
    }

    public function __get($name)
    {
        return $this->config[$name] ?? null;
    }
}
