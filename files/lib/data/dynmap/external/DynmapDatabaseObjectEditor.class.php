<?php

namespace wcf\data\dynmap\external;

use wcf\data\DatabaseObjectEditor;
use wcf\data\dynmap\Server;
use wcf\system\database\util\PreparedStatementConditionBuilder;

class DynmapDatabaseObjectEditor extends DatabaseObjectEditor
{
    /**
     * @var Server
     */
    protected $server;

    /**
     * @inheritDoc
     */
    public function __construct(DynmapDatabaseObject $object, Server $server)
    {
        parent::__construct($object);

        $this->server = $server;
    }

    /**
     * @inheritDoc
     * @return null
     */
    public static function create(array $parameters = [])
    {
        $server = null;
        $keys = $values = '';
        $statementParameters = [];
        foreach ($parameters as $key => $value) {
            if ($value instanceof Server) {
                $server = $value;
                continue;
            }
            if (!empty($keys)) {
                $keys .= ',';
                $values .= ',';
            }

            $keys .= $key;
            $values .= '?';
            $statementParameters[] = $value;
        }
        if ($server === null) {
            throw new \BadMethodCallException('Server not set.');
        }

        // save object
        $sql = "INSERT INTO " . static::getDatabaseTableName() . "
                            (" . $keys . ")
                VALUES      (" . $values . ")";
        $statement = $server->getDB()->prepareStatement($sql);
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
        $statement = $this->server->getDB()->prepareStatement($sql);
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
        $statement = $this->server->getDB()->prepareStatement($sql);
        $statement->execute($statementParameters);
    }

    /**
     * @inheritDoc
     */
    public function delete(PreparedStatementConditionBuilder $conditionBuilder = new PreparedStatementConditionBuilder())
    {
        throw new \BadMethodCallException('delete is not supported for DynmapDatabaseObjectEditor.');
    }

    
    /**
     * @inheritDoc
     */
    public static function deleteAll(array $objectIDs = [], PreparedStatementConditionBuilder $conditionBuilder = new PreparedStatementConditionBuilder())
    {
        throw new \BadMethodCallException('deleteAll is not supported for DynmapDatabaseObjectEditor.');
    }
}
