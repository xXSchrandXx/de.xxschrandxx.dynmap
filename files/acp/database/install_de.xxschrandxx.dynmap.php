<?php

use wcf\system\database\table\column\DefaultTrueBooleanDatabaseTableColumn;
use wcf\system\database\table\column\IntDatabaseTableColumn;
use wcf\system\database\table\column\NotNullInt10DatabaseTableColumn;
use wcf\system\database\table\column\VarcharDatabaseTableColumn;
use wcf\system\database\table\PartialDatabaseTable;

return [
    PartialDatabaseTable::create('wcf1_minecraft')
        ->columns([
            VarcharDatabaseTableColumn::create('icon')
                ->length(255),
            VarcharDatabaseTableColumn::create('description')
                ->length(255),
            VarcharDatabaseTableColumn::create('dbHost')
                ->length(255),
            IntDatabaseTableColumn::create('dbPort'),
            VarcharDatabaseTableColumn::create('dbUser')
                ->length(255),
            VarcharDatabaseTableColumn::create('dbPassword')
                ->length(255),
            VarcharDatabaseTableColumn::create('dbName')
                ->length(255),
            DefaultTrueBooleanDatabaseTableColumn::create('webchatEnabled'),
            NotNullInt10DatabaseTableColumn::create('webchatInterval')
                ->defaultValue(5)
        ])
];
