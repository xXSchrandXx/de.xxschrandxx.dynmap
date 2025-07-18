<?php
namespace wcf\system\event\listener;

use wcf\acp\form\MinecraftAddForm;
use wcf\system\form\builder\field\BooleanFormField;
use wcf\system\form\builder\field\IntegerFormField;
use wcf\system\form\builder\field\PasswordFormField;
use wcf\system\form\builder\field\TextFormField;

class MinecraftAddDynmapEventListener implements IParameterizedEventListener {
    /**
     * @var MinecraftEditForm $eventObj
     */
    public function execute($eventObj, $className, $eventName, array &$parameters) {
        $this->$eventName($eventObj);
    }

    /**
     * @var MinecraftAddForm $eventObj
     */
    protected function createForm($eventObj) {
        $formContainer = $eventObj->form->getNodeById('data');
        $formContainer->appendChildren([
            TextFormField::create('dbHost')
                ->label('wcf.acp.form.minecraftAdd.dbHost'),
            IntegerFormField::create('dbPort')
                ->label('wcf.acp.form.minecraftAdd.dbPort'),
            TextFormField::create('dbUser')
                ->label('wcf.acp.form.minecraftAdd.dbUser'),
            PasswordFormField::create('dbPassword')
                ->label('wcf.acp.form.minecraftAdd.dbPassword'),
            TextFormField::create('dbName')
                ->label('wcf.acp.form.minecraftAdd.dbName'),
            BooleanFormField::create('webchatEnabled')
                ->label('wcf.acp.form.minecraftAdd.webchatEnabled')
                ->description('wcf.acp.form.minecraftAdd.webchatEnabled.description'),
            IntegerFormField::create('webchatInterval')
                ->label('wcf.acp.form.minecraftAdd.webchatInterval')
        ]);
    }
}