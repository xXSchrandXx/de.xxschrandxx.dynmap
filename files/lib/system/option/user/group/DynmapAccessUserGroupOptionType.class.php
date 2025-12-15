<?php

namespace wcf\system\option\user\group;

use Exception;
use wcf\data\dynmap\Server;
use wcf\data\minecraft\MinecraftList;
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
        $minecraftList = new MinecraftList();
        $minecraftList->readObjects();
        $minecrafts = $minecraftList->getObjects();
        $selectOptions = [];
        /** @var \wcf\data\minecraft\Minecraft $minecraft */
        foreach ($minecrafts as $minecraft) {
            try {
                $server = new Server($minecraft);
                $selectOptions[$minecraft->getObjectID()] = $minecraft->getTitle();
                foreach($server->getWorlds() as $world) {
                    $selectOptions[$minecraft->getObjectID() . ":" . $world['name']] = '&nbsp;&nbsp;&nbsp;&nbsp;' . htmlspecialchars($world['title'] ?? $world['name']);
                    foreach ($world['maps'] as $map) {
                        $selectOptions[$minecraft->getObjectID() . ":" . $world['name'] . ":" . $map['name']] =  '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;' . htmlspecialchars($map['title'] ?? $map['name']);
                    }
                }
            } catch (Exception $e) {
                // do nothing
            }
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
