<?php

namespace wcf\data\dynmap;

use wcf\data\DatabaseObjectList;

/**
 * @property DynmapDatabaseObject[] $objects
 * @method DynmapDatabaseObject getSingleObject()
 * @method DynmapDatabaseObject[] getObjects()
 */
class DynmapDatabaseObjectList extends DatabaseObjectList
{
    /**
     * @inheritDoc
     */
    public function countObjects()
    {
        $sql = "SELECT  COUNT(*)
                FROM    " . $this->getDatabaseTableName() . " " . $this->getDatabaseTableAlias() . "
                " . $this->sqlConditionJoins . "
                " . $this->getConditionBuilder();
        $statement = DynmapDB::getInstance()->getDB()->prepareStatement($sql);
        $statement->execute($this->getConditionBuilder()->getParameters());

        return $statement->fetchSingleColumn();
    }

    /**
     * @inheritDoc
     */
    public function readObjectIDs()
    {
        throw new \BadMethodCallException('readObjectIDs is not supported for DDynmapDatabaseObjectList.');
    }

    /**
     * @inheritDoc
     */

    /**
     * Reads the objects from database.
     */
    public function readObjects()
    {
        $sql = "SELECT  " . (!empty($this->sqlSelects) ? $this->sqlSelects . ($this->useQualifiedShorthand ? ',' : '') : '') . "
                        " . ($this->useQualifiedShorthand ? $this->getDatabaseTableAlias() . '.*' : '') . "
                FROM    " . $this->getDatabaseTableName() . " " . $this->getDatabaseTableAlias() . "
                " . $this->sqlJoins . "
                " . $this->getConditionBuilder() . "
                " . (!empty($this->sqlOrderBy) ? "ORDER BY " . $this->sqlOrderBy : '');
        $statement = DynmapDB::getInstance()->getDB()->prepareStatement($sql, $this->sqlLimit, $this->sqlOffset);
        $statement->execute($this->getConditionBuilder()->getParameters());
        $this->objects = $statement->fetchObjects(($this->objectClassName ?: $this->className));

        // decorate objects
        if (!empty($this->decoratorClassName)) {
            foreach ($this->objects as &$object) {
                $object = new $this->decoratorClassName($object);
            }
            unset($object);
        }

        // use table index as array index
        $objects = $this->indexToObject = [];
        $i = 0;
        foreach ($this->objects as $object) {
            $objectID = $i++;
            $objects[$objectID] = $object;

            $this->indexToObject[] = $objectID;
        }
        $this->objectIDs = $this->indexToObject;
        $this->objects = $objects;
    }
}
