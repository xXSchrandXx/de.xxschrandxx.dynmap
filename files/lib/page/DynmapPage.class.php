<?php

namespace wcf\page;

use Laminas\Diactoros\Response\RedirectResponse;
use wcf\data\minecraft\MinecraftList;
use wcf\system\request\LinkHandler;

class DynmapPage extends SortablePage
{
    /**
     * @inheritDoc
     */
    public $objectListClassName = MinecraftList::class;

    /**
     * @inheritDoc
     */
    public $defaultSortField = 'minecraftID';

    public $serverIDs = [];

    public $servers = [];

    /**
     * @inheritDoc
     */
    protected function initObjectList()
    {
        parent::initObjectList();

        $this->objectList->getConditionBuilder()->add('
            dbHost IS NOT NULL AND dbHost != ? AND
            dbPort IS NOT NULL AND dbPort != ? AND
            dbUser IS NOT NULL AND dbUser != ? AND
            dbPassword IS NOT NULL AND dbPassword != ? AND
            dbName IS NOT NULL AND dbName != ?', ['', '', '', '', '']);
    }

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
