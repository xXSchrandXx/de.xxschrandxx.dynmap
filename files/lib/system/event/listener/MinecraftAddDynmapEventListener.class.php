<?php
namespace wcf\system\event\listener;

use wcf\acp\form\MinecraftAddForm;
use wcf\system\database\exception\DatabaseException;
use wcf\system\database\MySQLDatabase;
use wcf\system\form\builder\field\BooleanFormField;
use wcf\system\form\builder\field\ColorFormField;
use wcf\system\form\builder\field\DescriptionFormField;
use wcf\system\form\builder\field\FileProcessorFormField;
use wcf\system\form\builder\field\IconFormField;
use wcf\system\form\builder\field\IntegerFormField;
use wcf\system\form\builder\field\PasswordFormField;
use wcf\system\form\builder\field\TextFormField;
use wcf\system\form\builder\field\validation\FormFieldValidationError;
use wcf\system\form\builder\field\validation\FormFieldValidator;

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
            IconFormField::create('icon')
                ->label('wcf.acp.form.minecraftAdd.icon'),
            DescriptionFormField::create()
                ->label('wcf.acp.form.minecraftAdd.description'),
            TextFormField::create('dbHost')
                ->label('wcf.acp.form.minecraftAdd.dbHost')
                ->addValidator(new FormFieldValidator('connection', function (TextFormField $field) {
                    /** @var IntegerFormField $dbPortField */
                    $dbPortField = $field->getDocument()->getNodeById('dbPort');
                    /** @var TextFormField $dbUserField */
                    $dbUserField = $field->getDocument()->getNodeById('dbUser');
                    /** @var TextFormField $dbPasswordField */
                    $dbPasswordField = $field->getDocument()->getNodeById('dbPassword');
                    /** @var TextFormField $dbNameField */
                    $dbNameField = $field->getDocument()->getNodeById('dbName');
                    try {
                        new MySQLDatabase($field->getSaveValue(), $dbUserField->getSaveValue(), $dbPasswordField->getSaveValue(), $dbNameField->getSaveValue(), $dbPortField->getSaveValue());
                    } catch (DatabaseException $e) {
                        $field->addValidationError(
                            new FormFieldValidationError('connect', 'wcf.acp.form.minecraftAdd.dbHost.error', ['msg' => $e->getMessage()])
                        );
                    }
                })),
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
                ->suffix('wcf.acp.option.suffix.seconds')
        ]);
    }
}