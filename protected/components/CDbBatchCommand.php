<?php
namespace application\components;

use CDbCommand;
use CDbConnection;

class CDbBatchCommand extends CDbCommand
{
    public function __construct(CDbConnection $connection, $query = null)
    {
        parent::__construct($connection, $query);
    }

    public function insert($table, $columns)
    {
        $params = array();
        $names = array();

        if (empty($columns)) return;
        if (reset($columns) === FALSE) return;
        $names = array_keys((array)reset($columns));
        foreach ($names as $k => $v) {
            $names[$k] = $this->getConnection()->quoteColumnName($v);
        }

        foreach ($columns as $column) {

            $column = (array)$column;
            foreach ($column as $name => $value) {
                $name = $this->getConnection()->quoteColumnName($name);
                $key = array_search($name, $names);
                $params[$key] = "'" . addslashes($value) . "'";

            }

            $values[] = '(' . implode(', ', $params) . ')';
        }

        $values = implode(',', $values);
        $sql = 'INSERT INTO ' . $this->getConnection()->quoteTableName($table)
            . ' (' . implode(', ', $names) . ') VALUES ' . $values;

        return $this->setText($sql)->execute();

    }

    public function replace($table, $columns)
    {
        $params = array();
        $names = array();

        if (empty($columns)) return;
        if (reset($columns) === FALSE) return;
        $names = array_keys((array)reset($columns));
        foreach ($names as $k => $v) {
            $names[$k] = $this->getConnection()->quoteColumnName($v);
        }

        foreach ($columns as $column) {

            $column = (array)$column;
            foreach ($column as $name => $value) {
                $name = $this->getConnection()->quoteColumnName($name);
                $key = array_search($name, $names);
                $params[$key] = "'" . addslashes($value) . "'";

            }

            $values[] = '(' . implode(', ', $params) . ')';
        }

        $values = implode(',', $values);
        $sql = 'REPLACE INTO ' . $this->getConnection()->quoteTableName($table)
            . ' (' . implode(', ', $names) . ') VALUES ' . $values;

        return $this->setText($sql)->execute();

    }

}