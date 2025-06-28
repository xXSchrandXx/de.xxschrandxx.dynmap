<?php

namespace wcf\data\dynmap\standalonefiles;

use wcf\data\dynmap\DynmapDatabaseObjectEditor;

class StandaloneFileEditor extends DynmapDatabaseObjectEditor
{
    /**
     * @inheritDoc
     */
    protected static $baseClass = StandaloneFile::class;
}
