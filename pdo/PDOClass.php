<?php

require_once dirname(__FILE__) .'/../config/config.php';

class PDOClass
{
    private $host = DB_HOST;
    private $user = DB_USER;
    private $pass = DB_PASS;
    private $dbname = DB_NAME;

    private $dbh;
    private $error;
    private $stmt;

    public function __construct()
    {
        $dsn = "mysql:host=$this->host;dbname=$this->dbname";
        $options = array(
            PDO::ATTR_PERSISTENT => true,
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::MYSQL_ATTR_INIT_COMMAND => 'SET NAMES utf8',
        );
        $this->dbh = new PDO($dsn, $this->user, $this->pass, $options);
        $this->error = false;
    }

    public function query($query)
    {
        $this->stmt = $this->dbh->prepare($query);

        return $this; //allows chaining
    }

    public function bind($param, $value, $type = null)
    {
        if (is_null($type)) {
            switch (true) {
                case is_int($value):
                    $type = PDO::PARAM_INT;
                    break;
                case is_bool($value):
                    $type = PDO::PARAM_BOOL;
                    break;
                case is_null($value):
                    $type = PDO::PARAM_NULL;
                    break;
                default:
                    $type = PDO::PARAM_STR;
            }
        }
        $this->stmt->bindValue($param, $value, $type);

        return $this; //allows chaining
    }

    public function execute()
    {
        try {
            $this->stmt->execute();
        } catch (PDOException $e) {
            $this->error = $e->getMessage();
        }

        return $this->stmt->errorCode() === '00000';
    }

    public function fetchAll($type = 'Object')
    {
        $this->execute();
        if ($type == 'Array') {
            return $this->stmt->fetchAll(PDO::FETCH_ASSOC);
        } else {
            return $this->stmt->fetchAll(PDO::FETCH_OBJ);
        }
    }

    public function selectAll($table, $columns = false, $where = false, $extra = false)
    {
        $this->buildSelectStatement($table, $columns, $where, $extra);
        if ($where) {
            $this->bindWhereParameters($where);
        }

        return $this->fetchAll();
    }

    public function selectRow($table, $columns = false, $where = false, $extra = false)
    {
        $this->buildSelectStatement($table, $columns, $where, $extra);
        if ($where) {
            $this->bindWhereParameters($where);
        }

        return $this->fetchRow();
    }

    public function selectOne($table, $column = false, $where = false, $extra = false)
    {
        if (!is_array($column) || count($column) != 1) {
            $this->error = 'Error `selectOne` can only pass a single column name';
        }

        $this->buildSelectStatement($table, $column, $where, $extra);
        if ($where) {
            $this->bindWhereParameters($where);
        }
        $returnObject = $this->fetchRow();

        return $returnObject->$column;

    }

    public function insert($table, $columns)
    {
        $columnString = $this->buildColumnString($columns);
        $binderString = $this->buildBindString($columns);
        $query = "INSERT INTO $table ($columnString) VALUES ($binderString);";
        $this->stmt = $this->dbh->prepare($query);
        foreach ($columns as $key => $value) {
            $this->bind(":column_$key", $value);
        }

        return $this->execute();
    }

    public function update($table, $columns, $where = false, $limit = false)
    {

        $query = "UPDATE $table SET ";
        $query .= $this->buildColumnBindString($columns);
        $query .= $this->buildWhereString($where);
        if ($limit) {
            $limitInt = (int)$limit;
            $query .= " LIMIT $limitInt";
        }
        $this->stmt = $this->dbh->prepare($query);
        foreach ($columns as $key => $value) {
            $this->bind(":column_$key", $value);
        }
        $this->bindWhereParameters($where);

        return $this->execute();
    }


    public function delete($table, $where = false, $limit = false)
    {
        $query = "DELETE FROM $table";
        $query .= $this->buildWhereString($where);
        if ($limit) {
            $limitInt = (int)$limit;
            $query .= " LIMIT $limitInt";
        }
        $this->stmt = $this->dbh->prepare($query);
        $this->bindWhereParameters($where);

        return $this->execute();
    }

    public function rowCount()
    {
        return $this->stmt->rowCount();
    }

    public function lastInsertId()
    {
        return $this->dbh->lastInsertId();
    }

    public function debugDumpParams()
    {
        return $this->stmt->debugDumpParams();
    }

    public function getError()
    {
        return $this->error;
    }

    public function getQuery()
    {
        return $this->stmt->queryString;
    }

    private function buildSelectStatement($table, $columns, $where, $extra)
    {
        if (!$columns) {
            $columns = ' * ';
        }
        $query = "SELECT $columns FROM $table ";
        if ($where) {
            $query .= $this->buildWhereString($where);
        }
        if ($extra) {
            $query .= " $extra ";
        }
        $this->stmt = $this->dbh->prepare($query);
    }

    private function buildWhereString($where)
    {
        $whereString = '';
        if ($where) {
            $whereString .= " WHERE ";
            if (is_array($where)) {
                $clauses = array();
                foreach ($where as $key => $value) {
                    $clauses[] = "$key = :where_$key";
                }
                $whereString .= implode(' AND ', $clauses);
            } else {
                $whereString .= preg_replace("/where/i", "", $where);
            }
        }

        return $whereString;
    }

    private function bindWhereParameters($where)
    {
        if ($where) {
            if (is_array($where)) {
                foreach ($where as $key => $value) {
                    $this->bind(":where_$key", $value);
                }
            }
        }
    }

    private function buildColumnBindString($columns)
    {
        if ($columns && is_array($columns)) {
            $binders = array();
            foreach ($columns as $key => $value) {
                $binders[] = "$key = :column_$key";
            }

            return (string)implode(', ', $binders);
        }
    }

    private function buildColumnString($columns)
    {
        $tmp = array();
        foreach ($columns as $key => $value) {
            $tmp[] = $key;
        }

        return (string)implode(', ', $tmp);
    }

    private function buildBindString($columns)
    {
        $tmp = array();
        foreach ($columns as $key => $value) {
            $tmp[] = ':column_'.$key;
        }

        return (string)implode(', ', $tmp);
    }

}