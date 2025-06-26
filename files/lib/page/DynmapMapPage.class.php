<?php

namespace wcf\page;

use wcf\data\dynmap\maps\MapList;
use wcf\data\dynmap\servers\Server;
use wcf\system\exception\IllegalLinkException;
use wcf\system\WCF;

class DynmapMapPage extends AbstractPage
{
    /**
     * @inheritDoc
     */
    public $neededModules = ['DYNMAP_GENERAL_HOST', 'DYNMAP_GENERAL_PORT', 'DYNMAP_GENERAL_USER', 'DYNMAP_GENERAL_PASSWORD', 'DYNMAP_GENERAL_NAME'];

    /**
     * @var Server
     */
    public $object;

    /**
     * @var Map[]
     */
    public $maps = [];

    /**
     * @inheritDoc
     */
    public function readParameters()
    {
        parent::readParameters();

        if (isset($_REQUEST['id']) && \is_numeric($_REQUEST['id'])) {
            $this->object = new Server((int)$_REQUEST['id']);
        }

        if (!isset($this->object)) {
            throw new IllegalLinkException();
        }
    }

    public function assignVariables()
    {
        parent::assignVariables();

        WCF::getTPL()->assign([
            'object' => $this->object
        ]);
    }
}
