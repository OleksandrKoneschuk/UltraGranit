<?php

namespace core;

class DataBase
{
    public $pdo;

    public function __construct($host, $name, $login, $password)
    {
        $this->pdo = new \PDO("mysql:host={$host};dbname={$name}", $login, $password, [
            \PDO::ATTR_ERRMODE => \PDO::ERRMODE_EXCEPTION,
            \PDO::ATTR_DEFAULT_FETCH_MODE => \PDO::FETCH_OBJ
        ]);
    }

    protected function where($where, $subquery = false)
    {
        if (is_array($where)) {
            $where_string = "WHERE ";
            $where_fields = array_keys($where);
            $parts = [];
            foreach ($where_fields as $field) {
                $paramKey = $subquery ? str_replace('.', '_', $field) : $field;
                $parts[] = "{$field} = :{$paramKey}";
            }
            $where_string .= implode(' AND ', $parts);
        } elseif (is_string($where)) {
            $where_string = $where;
        } else {
            $where_string = '';
        }
        return $where_string;
    }

    public function select($table, $fields = "*", $where = null, $options = [])
    {
        if (is_array($fields)) {
            $fields_string = implode(', ', $fields);
        } elseif (is_string($fields)) {
            $fields_string = $fields;
        } else {
            $fields_string = "*";
        }

        $where_string = $this->where($where);
        $order_string = "";
        if (isset($options['ORDER'])) {
            $order = $options['ORDER'];
            $order_fields = array_keys($order);
            $order_string = "ORDER BY ";
            $parts = [];
            foreach ($order_fields as $field) {
                $parts[] = "{$field} " . strtoupper($order[$field]);
            }
            $order_string .= implode(', ', $parts);
        }

        $limit_string = "";
        if (isset($options['LIMIT'])) {
            $limit = $options['LIMIT'];
            if (is_array($limit)) {
                $limit_string = "LIMIT " . intval($limit[0]) . ", " . intval($limit[1]);
            } else {
                $limit_string = "LIMIT " . intval($limit);
            }
        }

        $sql = "SELECT {$fields_string} FROM {$table} {$where_string} {$order_string} {$limit_string}";
        $sth = $this->pdo->prepare($sql);

        if (is_array($where)) {
            foreach ($where as $key => $value) {
                $sth->bindValue(":{$key}", $value);
            }
        }

        $sth->execute();
        return $sth->fetchAll() ?: [];
    }


    public function insert($table, $row_to_insert)
    {
        $fields_list = implode(", ", array_keys($row_to_insert));
        $params_array = [];
        foreach ($row_to_insert as $key => $value) {
            $params_array[] = ":{$key}";
        }
        $params_list = implode(", ", $params_array);
        $sql = "INSERT INTO {$table} ({$fields_list}) VALUES ({$params_list})";
        $sth = $this->pdo->prepare($sql);
        foreach ($row_to_insert as $key => $value) {
            $sth->bindValue(":{$key}", $value);
        }
        $sth->execute();
        return $this->pdo->lastInsertId();
    }

    public function delete($table, $where)
    {
        $where_string = $this->where($where);

        $sql = "DELETE FROM {$table} {$where_string}";
        $sth = $this->pdo->prepare($sql);
        foreach ($where as $key => $value) {
            $sth->bindValue(":{$key}", $value);
        }
        $sth->execute();
        return $sth->rowCount();
    }

    public function update($table, $row_to_update, $where)
    {
        $where_string = $this->where($where);
        $set_array = [];
        foreach ($row_to_update as $key => $value) {
            $set_array[] = "{$key} = :{$key}";
        }
        $set_string = implode(", ", $set_array);
        $sql = "UPDATE {$table} SET {$set_string} {$where_string}";
        $sth = $this->pdo->prepare($sql);
        foreach ($row_to_update as $key => $value) {
            $sth->bindValue(":{$key}", $value);
        }
        foreach ($where as $key => $value) {
            $sth->bindValue(":{$key}", $value);
        }
        $sth->execute();
        return $sth->rowCount();
    }

    public function selectWithSubquery($table, $fields, $subqueryFields, $subqueryTable, $subqueryCondition, $where = null)
    {
        $subqueryString = "(SELECT {$subqueryFields} FROM {$subqueryTable} WHERE {$subqueryCondition} LIMIT 1) AS photo";

        $fields[] = $subqueryString;
        $fields_string = implode(', ', $fields);

        $where_string = $this->where($where, true);

        $sql = "SELECT {$fields_string} FROM {$table} {$where_string}";
        $sth = $this->pdo->prepare($sql);

        if (is_array($where)) {
            foreach ($where as $key => $value) {
                $paramKey = str_replace('.', '_', $key); // Замінюємо крапки на підкреслення
                $sth->bindValue(":{$paramKey}", $value, \PDO::PARAM_INT);
            }
        }

        $sth->execute();
        return $sth->fetchAll() ?: [];
    }

    public function beginTransaction()
    {
        $this->pdo->beginTransaction();
    }

    public function commit()
    {
        $this->pdo->commit();
    }

    public function rollBack()
    {
        $this->pdo->rollBack();
    }

    public function inTransaction()
    {
        return $this->pdo->inTransaction(); // Перевіряє, чи є активна транзакція
    }

    public function lastInsertId()
    {
        return $this->pdo->lastInsertId(); // Отримує останній вставлений ID
    }


}