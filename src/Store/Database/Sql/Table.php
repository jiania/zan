<?php
/*
 *    Copyright 2012-2016 Youzan, Inc.
 *
 *    Licensed under the Apache License, Version 2.0 (the "License");
 *    you may not use this file except in compliance with the License.
 *    You may obtain a copy of the License at
 *
 *        http://www.apache.org/licenses/LICENSE-2.0
 *
 *    Unless required by applicable law or agreed to in writing, software
 *    distributed under the License is distributed on an "AS IS" BASIS,
 *    WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 *    See the License for the specific language governing permissions and
 *    limitations under the License.
 */
namespace Zan\Framework\Store\Database\Sql;

use Zan\Framework\Foundation\Core\Path;
use Zan\Framework\Utilities\DesignPattern\Singleton;
use Zan\Framework\Foundation\Core\ConfigLoader;
use Zan\Framework\Store\Database\Sql\Exception\SqlTableException;

class Table
{
    use Singleton;
    private $tables = [];

    public function getDatabase($tableName)
    {
        if (!isset($this->tables[$tableName])) {
            $this->setTables();
            if (!isset($this->tables[$tableName])) {
                throw new SqlTableException('无法获取数' . $tableName . '表所在的数据库配置');
            }
        }
        return $this->tables[$tableName];
    }

    public function init()
    {
        $this->setTables();
    }

    private function setTables()
    {
        if ([] == $this->tables) {
            $tables = ConfigLoader::getInstance()->loadDistinguishBetweenFolderAndFile(Path::getTablePath());
            if (null == $tables || [] == $tables) {
                return;
            }
            foreach ($tables as $table) {
                if (null == $table || [] == $table) {
                    continue;
                }
                $parseTable = $this->parseTable($table);
                if ([] != $parseTable) {
                    $this->tables = array_merge($this->tables, $parseTable);
                }
            }
        }
        return;
    }

    private function parseTable($table)
    {
        $result = [];
        foreach ($table as $db => $tableList) {
            foreach ($tableList as $tableName) {
                $result[$tableName] = $db;
            }
        }
        return $result;
    }

}
