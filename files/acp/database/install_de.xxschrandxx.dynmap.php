<?php

use wcf\system\database\table\column\BlobDatabaseTableColumn;
use wcf\system\database\table\column\DefaultTrueBooleanDatabaseTableColumn;
use wcf\system\database\table\column\IntDatabaseTableColumn;
use wcf\system\database\table\column\NotNullInt10DatabaseTableColumn;
use wcf\system\database\table\column\VarcharDatabaseTableColumn;
use wcf\system\database\table\PartialDatabaseTable;

return [
    PartialDatabaseTable::create('wcf1_minecraft')
        ->columns([
            IntDatabaseTableColumn::create('image'),
            VarcharDatabaseTableColumn::create('description')
                ->length(255),
            VarcharDatabaseTableColumn::create('dynmapHost')
                ->length(255),
            IntDatabaseTableColumn::create('dynmapPort'),
            VarcharDatabaseTableColumn::create('dynmapUser')
                ->length(255),
            VarcharDatabaseTableColumn::create('dynmapPassword')
                ->length(255),
            VarcharDatabaseTableColumn::create('dynmapName')
                ->length(255),
            DefaultTrueBooleanDatabaseTableColumn::create('webchatEnabled'),
            NotNullInt10DatabaseTableColumn::create('webchatInterval')
                ->defaultValue(5)
        ])
];
