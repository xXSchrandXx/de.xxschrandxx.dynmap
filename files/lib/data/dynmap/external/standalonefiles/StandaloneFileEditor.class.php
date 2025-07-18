<?php

namespace wcf\data\dynmap\external\standalonefiles;

use wcf\data\dynmap\external\DynmapDatabaseObjectEditor;

class StandaloneFileEditor extends DynmapDatabaseObjectEditor
{
    /**
     * @inheritDoc
     */
    protected static $baseClass = StandaloneFile::class;
}
