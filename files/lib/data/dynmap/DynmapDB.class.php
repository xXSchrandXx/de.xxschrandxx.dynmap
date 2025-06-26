<?php

namespace wcf\data\dynmap;

use wcf\system\database\exception\DatabaseException;
use wcf\system\database\MySQLDatabase;
use wcf\system\database\statement\PreparedStatement;
use wcf\system\SingletonFactory;

class DynmapDB extends SingletonFactory
{
    /**
     * @var MySQLDatabase
     */
    private $dbObj;

    /**
     * @inheritDoc
     */
    protected function init()
    {
        // get configuration
        $dbHost = DYNMAP_GENERAL_HOST;
        $dbPort = DYNMAP_GENERAL_PORT;
        $dbUser = DYNMAP_GENERAL_USER;
        $dbPassword = DYNMAP_GENERAL_PASSWORD;
        $dbName = DYNMAP_GENERAL_NAME;
        $defaultDriverOptions = [];

        // create database connection
        $this->dbObj = new MySQLDatabase(
            $dbHost,
            $dbUser,
            $dbPassword,
            $dbName,
            $dbPort,
            false,
            false,
            $defaultDriverOptions
        );
    }

    /**
     * Returns the database object.
     * @return MySQLDatabase
     */
    public function getDB()
    {
        return $this->dbObj;
    }

    /**
     * Executes a SQL statement with the given parameters.
     * @param string $sql The SQL statement to execute.
     * @param array $params The parameters to bind to the SQL statement.
     * @param int $limit The limit for the SQL statement.
     * @param int $offset The offset for the SQL statement.
     * @return PreparedStatement The executed statement object.
     */
    public function execute(string $sql, array $params = [], $limit = 0, $offset = 0): PreparedStatement
    {
        foreach ($params as $param) {
            $sql = preg_replace('/\?/', $param, $sql, 1);
        }
        $statement = $this->getDB()->prepareStatement($sql, $limit, $offset);
        $statement->execute();
        return $statement;
    }

    public function getServerIDs(): array
    {
        try {
            $statement = $this->getDB()->prepareStatement('SELECT DISTINCT ServerID FROM StandaloneFiles');
            $statement->execute();
            return $statement->fetchAll(\PDO::FETCH_COLUMN);
        } catch (DatabaseException $e) {
            return [];
        }
    }

    public function getSchemaVersion(): int
    {
        try {
            $statement = $this->getDB()->prepareStatement('SELECT level FROM SchemaVersion');
            $statement->execute();
            return $statement->fetchSingleColumn();
        } catch (DatabaseException $e) {
            // If the table does not exist, return 0 as the schema version
            return 0;
        }
    }
}
