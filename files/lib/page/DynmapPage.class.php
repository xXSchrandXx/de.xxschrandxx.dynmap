<?php

namespace wcf\page;

use Laminas\Diactoros\Response\RedirectResponse;
use wcf\data\dynmap\ServerList;
use wcf\system\request\LinkHandler;

class DynmapPage extends MultipleLinkPage
{
    /**
     * @inheritDoc
     */
    public $objectListClassName = ServerList::class;

    /**
     * @inheritDoc
     */
    public function readData()
    {
        parent::readData();

        if ($this->items == 1) {
            // display server map
            // using mid (mapid) instead of id, cause it can be 0 and 0 gets removed
            return new RedirectResponse(LinkHandler::getInstance()->getControllerLink(DynmapMapPage::class, ['id' => $this->objectList->getSingleObject()->getObjectID()]));
        }
    }
}
