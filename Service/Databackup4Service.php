<?php

/*
 * This file is part of EC-CUBE
 *
 * Copyright(c) EC-CUBE CO.,LTD. All Rights Reserved.
 *
 * http://www.ec-cube.co.jp/
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Plugin\Databackup4\Service;

use Doctrine\DBAL\Schema\AbstractSchemaManager;
use Doctrine\DBAL\Schema\Column;
use Doctrine\DBAL\Schema\Table;
use Doctrine\ORM\EntityManagerInterface;

class Databackup4Service
{
    /**
     * @var EntityManagerInterface
     */
    protected $entityManager;

    /**
     * Databackup4Service constructor.
     *
     * @param EntityManagerInterface $entityManager
     */
    public function __construct(
        EntityManagerInterface $entityManager
    ) {
        $this->entityManager = $entityManager;
    }

    /**
     * @return array
     */
    public function listTableNames()
    {
        /** @var AbstractSchemaManager $sm */
        $sm = $this->entityManager->getConnection()->getSchemaManager();
        /** @var Table[] $tables */
        $tables = $sm->listTables();

        return array_map(function ($table) {
            /* @var Table $table */
            return $table->getName();
        }, $tables);
    }

    /**
     * @param string $tableName
     * @param array
     */
    public function listTableColumnNames($tableName)
    {
        /** @var AbstractSchemaManager $sm */
        $sm = $this->entityManager->getConnection()->getSchemaManager();
        /** @var Column[] $columns */
        $columns = $sm->listTableColumns($tableName);

        return array_map(function ($column) {
            /* @var Column $column */
            return $column->getName();
        }, $columns);
    }

    public function findAll($tableName)
    {
        $conn = $this->entityManager->getConnection();
        $sql = 'SELECT * FROM '.$tableName;
        $stmt = $conn->query($sql);

        return $stmt->fetchAll();
    }

    /**
     * @param string $tableName
     * @param string $dumpDir
     *
     * @return bool
     */
    public function dumpCSV($tableName, $dumpDir)
    {
        $fp = fopen($dumpDir.'/'.$tableName.'.csv', 'w');
        if ($fp !== false) {
            $columns = $this->listTableColumnNames($tableName);
            fputcsv($fp, $columns);

            $conn = $this->entityManager->getConnection();
            $sql = 'SELECT '.implode(',', $columns).' FROM '.$tableName;
            $stmt = $conn->query($sql);
            while ($row = $stmt->fetch(\PDO::FETCH_NUM)) {
                fputcsv($fp, $row);
            }
            fclose($fp);

            return true;
        }

        throw new \LogicException();
    }
}
