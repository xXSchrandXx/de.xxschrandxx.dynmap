<?php

namespace wcf\data\dynmap;

use wcf\data\DatabaseObjectEditor;
use wcf\system\database\util\PreparedStatementConditionBuilder;

class DynmapDatabaseObjectEditor extends DatabaseObjectEditor
{
    /**
     * @inheritDoc
     * @return null
     */
    public static function create(array $parameters = [])
    {
        $keys = $values = '';
        $statementParameters = [];
        foreach ($parameters as $key => $value) {
            if (!empty($keys)) {
                $keys .= ',';
                $values .= ',';
            }

            $keys .= $key;
            $values .= '?';
            $statementParameters[] = $value;
        }

        // save object
        $sql = "INSERT INTO " . static::getDatabaseTableName() . "
                            (" . $keys . ")
                VALUES      (" . $values . ")";
        $statement = DynmapDB::getInstance()->getDB()->prepareStatement($sql);
        $statement->execute($statementParameters);
        
        return null;
    }

    /**
     * @inheritDoc
     */
    public function update(array $parameters = [], PreparedStatementConditionBuilder $conditionBuilder = new PreparedStatementConditionBuilder())
    {
        if (empty($parameters)) {
            return;
        }
        if (empty($conditionBuilder->getParameters())) {
            throw new \InvalidArgumentException('No conditions provided for update operation.');
        }

        $updateSQL = '';
        $statementParameters = [];
        foreach ($parameters as $key => $value) {
            if (!empty($updateSQL)) {
                $updateSQL .= ', ';
            }
            $updateSQL .= $key . ' = ?';
            $statementParameters[] = $value;
        }
        $statementParameters = array_merge($statementParameters, $conditionBuilder->getParameters());

        $sql = "UPDATE  " . static::getDatabaseTableName() . "
                SET     " . $updateSQL . "
                " . $conditionBuilder;
        $statement = DynmapDB::getInstance()->getDB()->prepareStatement($sql);
        $statement->execute($statementParameters);
    }

    /**
     * @inheritDoc
     */
    public function updateCounters(array $counters = [], PreparedStatementConditionBuilder $conditionBuilder = new PreparedStatementConditionBuilder())
    {
        if (empty($counters)) {
            return;
        }
        if (empty($conditionBuilder->getParameters())) {
            throw new \InvalidArgumentException('No conditions provided for update operation.');
        }

        $updateSQL = '';
        $statementParameters = [];
        foreach ($counters as $key => $value) {
            if (!empty($updateSQL)) {
                $updateSQL .= ', ';
            }
            $updateSQL .= $key . ' = ' . $key . ' + ?';
            $statementParameters[] = $value;
        }
        $statementParameters = array_merge($statementParameters, $conditionBuilder->getParameters());

        $sql = "UPDATE  " . static::getDatabaseTableName() . "
                SET     " . $updateSQL . "
                " . $conditionBuilder;
        $statement = DynmapDB::getInstance()->getDB()->prepareStatement($sql);
        $statement->execute($statementParameters);
    }

    /**
     * @inheritDoc
     */
    public function delete(PreparedStatementConditionBuilder $conditionBuilder = new PreparedStatementConditionBuilder())
    {
        $this->deleteAll([], $conditionBuilder);
    }

    
    /**
     * @inheritDoc
     */
    public static function deleteAll(array $objectIDs = [], PreparedStatementConditionBuilder $conditionBuilder = new PreparedStatementConditionBuilder())
    {
        if (!empty($objectIDs)) {
            throw new \InvalidArgumentException('Object IDs are not supported for deleteAll operation in DynmapDatabaseObjectEditor.');
        }
        if (empty($conditionBuilder->getParameters())) {
            throw new \InvalidArgumentException('No conditions provided for delete operation.');
        }
        $sql = "DELETE FROM " . static::getDatabaseTableName() . "
                " . $conditionBuilder;
        $statement = DynmapDB::getInstance()->getDB()->prepareStatement($sql);
        $statement->execute($conditionBuilder->getParameters());
        return $statement->getAffectedRows();
    }
}
