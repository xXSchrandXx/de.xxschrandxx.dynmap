<?php

namespace wcf\page;

use wcf\data\dynmap\Server;
use wcf\data\minecraft\Minecraft;
use wcf\system\exception\IllegalLinkException;
use wcf\system\exception\NamedUserException;
use wcf\system\WCF;

class DynmapMapPage extends AbstractPage
{
    /**
     * @var Server
     */
    public $object;

    /**
     * @inheritDoc
     */
    public function readParameters()
    {
        parent::readParameters();

        if (isset($_REQUEST['id']) && \is_numeric($_REQUEST['id'])) {
            $minecraft = new Minecraft((int)$_REQUEST['id']);
            if (!$minecraft->minecraftID) {
                throw new IllegalLinkException();
            }
            $this->object = new Server($minecraft);
        }

        if (!isset($this->object)) {
            throw new IllegalLinkException();
        }

        if (!$this->object->checkSchemaVersion()) {
            throw new NamedUserException(WCF::getLanguage()->getDynamicVariable('wcf.page.dynmapmap.schemaVersion')); // TODO
        }

        if (!$this->object->hasAccesToServer($_REQUEST['id'])) {
            throw new NamedUserException(WCF::getLanguage()->getDynamicVariable('wcf.page.dynmapmap.accessServer')); // TODO
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
