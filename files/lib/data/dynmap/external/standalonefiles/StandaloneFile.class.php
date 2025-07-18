<?php

namespace wcf\data\dynmap\external\standalonefiles;

use InvalidArgumentException;
use wcf\data\dynmap\external\DynmapDatabaseObject;
use wcf\util\JSON;

/**
 * @property-read string $FileName
 * @property-read $ServerID (always 0)
 * @property-read array|string $Content
 */
class StandaloneFile extends DynmapDatabaseObject
{
    /**
     * @inheritDoc
     */
    protected static $databaseTableName = 'StandaloneFiles';

    protected $decodedContent;

    public function getContent(): array
    {
        if (isset($this->decodedContent)) {
            return $this->decodedContent;
        }

        if (str_ends_with($this->FileName, '.json')) {
            $this->decodedContent = JSON::decode($this->Content, true);
        } else if (str_ends_with($this->FileName, '.php')) {
            preg_match_all('/\$(\w+)/', $this->Content, $matches);
            $result = [];
            foreach ($matches[1] as $varName) {
                if (preg_match('/\$' . preg_quote($varName, '/') . '\s*=\s*([^;]+)/', $this->Content, $valueMatch)) {
                    $result[$varName] = $valueMatch[1];
                }
            }
            $this->decodedContent = $result;
        } else {
            if (ENABLE_DEBUG_MODE) {
                throw new InvalidArgumentException('Not supported Content format.');
            }
            $this->decodedContent = [];
        }

        return $this->decodedContent;
    }
}
