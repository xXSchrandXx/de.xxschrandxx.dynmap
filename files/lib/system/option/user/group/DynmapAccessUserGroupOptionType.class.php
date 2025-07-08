<?php

namespace wcf\system\option\user\group;

use Exception;
use wcf\data\dynmap\standalonefiles\StandaloneFileList;
use wcf\data\option\Option;
use wcf\system\option\MultiSelectOptionType;
use wcf\system\option\user\group\IUserGroupOptionType;
use wcf\util\StringUtil;

class DynmapAccessUserGroupOptionType extends MultiSelectOptionType implements IUserGroupOptionType
{
    /**
     * @inheritDoc
     */
    public function getSelectOptions(Option $option)
    {
        $selectOptions = [];
        try {
            $configFileList = new StandaloneFileList();
            $configFileList->getConditionBuilder()->add('FileName = ?', ['dynmap_config.json']);
            $configFileList->readObjects();
            $configs = $configFileList->getObjects();
            foreach ($configs as $config) {
                $configArray = $config->getContent();
                $selectOptions[$config->ServerID] = 'Server ' . $config->ServerID;
                foreach($configArray['worlds'] as $world) {
                    $selectOptions[$config->ServerID . ":" . $world['name']] = '&nbsp;&nbsp;&nbsp;&nbsp;' . $world['title'] ?? $world['name'];
                    foreach ($world['maps'] as $map) {
                        $selectOptions[$config->ServerID . ":" . $world['name'] . ":" . $map['name']] =  '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;' . $map['title'] ?? $map['name'];
                    }
                }
            }
        } catch (Exception $e) {
            // do nothing
        }

        return $selectOptions;
    }

    /**
     * @inheritDoc
     */
    public function merge($defaultValue, $groupValue)
    {
        $defaultValue = empty($defaultValue) ? [] : \explode("\n", StringUtil::unifyNewlines($defaultValue));
        $groupValue = empty($groupValue) ? [] : \explode("\n", StringUtil::unifyNewlines($groupValue));

        return \implode("\n", \array_unique(\array_merge($defaultValue, $groupValue)));
    }
}
