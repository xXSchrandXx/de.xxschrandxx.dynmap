<?php

namespace wcf\page;

use Laminas\Diactoros\Response\RedirectResponse;
use wcf\data\dynmap\DynmapDB;
use wcf\system\request\LinkHandler;

class DynmapPage extends AbstractPage
{
    /**
     * @inheritDoc
     */
    public $neededModules = ['DYNMAP_GENERAL_HOST', 'DYNMAP_GENERAL_PORT', 'DYNMAP_GENERAL_USER', 'DYNMAP_GENERAL_PASSWORD', 'DYNMAP_GENERAL_NAME'];

    /**
     * @inheritDoc
     */
    public function readParameters()
    {
        parent::readParameters();

        $serverIDs = DynmapDB::getInstance()->getServerIDs();
        if (count($serverIDs) <= 1) {
            // display server map
            return new RedirectResponse(LinkHandler::getInstance()->getControllerLink(DynmapMapPage::class, ['id' => $serverIDs[0]]));
        }
    }
}
