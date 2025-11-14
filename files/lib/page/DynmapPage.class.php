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
            return new RedirectResponse(LinkHandler::getInstance()->getControllerLink(DynmapMapPage::class, ['id' => $this->objectList->getSingleObject()->getObjectID()]));
        }
    }
}
