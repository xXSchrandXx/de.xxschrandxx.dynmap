<?php

namespace wcf\data\dynmap\standalonefiles;

use wcf\data\dynmap\DynmapDatabaseObject;
use wcf\util\JSON;

/**
 * @property-read string $FileName
 * @property-read $ServerID
 * @property-read array $Content JSON
 */
class StandaloneFile extends DynmapDatabaseObject
{
    /**
     * @inheritDoc
     */
    protected static $databaseTableName = 'StandaloneFiles';

    /**
     * @inheritDoc
     */
    protected static $databaseTableIndexIsIdentity = false;

    protected $decodedContent;

    public function getContent(): array
    {
        if (isset($this->decodedContent)) {
            return $this->decodedContent;
        }

        $this->decodedContent = JSON::decode($this->Content, true);
        return $this->decodedContent;
    }
}
