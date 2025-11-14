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
            TextFormField::create('dynmapHost')
                ->label('wcf.acp.form.minecraftAdd.dynmapHost')
                ->addValidator(new FormFieldValidator('connection', function (TextFormField $field) {
                    /** @var IntegerFormField $dynmapPortField */
                    $dynmapPortField = $field->getDocument()->getNodeById('dynmapPort');
                    /** @var TextFormField $dynmapUserField */
                    $dynmapUserField = $field->getDocument()->getNodeById('dynmapUser');
                    /** @var TextFormField $dynmapPasswordField */
                    $dynmapPasswordField = $field->getDocument()->getNodeById('dynmapPassword');
                    /** @var TextFormField $dynmapNameField */
                    $dynmapNameField = $field->getDocument()->getNodeById('dynmapName');
                    try {
                        new MySQLDatabase($field->getSaveValue(), $dynmapUserField->getSaveValue(), $dynmapPasswordField->getSaveValue(), $dynmapNameField->getSaveValue(), $dynmapPortField->getSaveValue());
                    } catch (DatabaseException $e) {
                        $field->addValidationError(
                            new FormFieldValidationError('connect', 'wcf.acp.form.minecraftAdd.dynmapHost.error', ['msg' => $e->getMessage()])
                        );
                    }
                })),
            IntegerFormField::create('dynmapPort')
                ->label('wcf.acp.form.minecraftAdd.dynmapPort'),
            TextFormField::create('dynmapUser')
                ->label('wcf.acp.form.minecraftAdd.dynmapUser'),
            PasswordFormField::create('dynmapPassword')
                ->label('wcf.acp.form.minecraftAdd.dynmapPassword'),
            TextFormField::create('dynmapName')
                ->label('wcf.acp.form.minecraftAdd.dynmapName'),
            BooleanFormField::create('webchatEnabled')
                ->label('wcf.acp.form.minecraftAdd.webchatEnabled')
                ->description('wcf.acp.form.minecraftAdd.webchatEnabled.description'),
            IntegerFormField::create('webchatInterval')
                ->label('wcf.acp.form.minecraftAdd.webchatInterval')
                ->suffix('wcf.acp.option.suffix.seconds')
        ]);
    }
}