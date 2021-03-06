<?php

//------------------------------------
$sql = "SELECT * FROM `users`";
$db->query($sql);
$result = $db->fetchAll();     // Multiple rows
//$result  = $db->fetchOne();   // Single row
//$result  = $db->fetchAll('Array');  // Multiple rows, returned as a multi-dimensional array
//$result  = $db->fetchOne('Array');  // Single row, returned as an array

//-----------------------------------------------------
$sql = "SELECT * FROM `users` WHERE firstname = :firstname";
$db->query($sql);
$db->bind(':firstname', 'John');
$result = $db->fetchAll();
//or
//$result = $db->query($sql)->bind(':firstname', 'John')->fetchAll();

// QUERY RESULTS
// All results can be tested and outputted using $result
if ($result) {
    echo $db->rowCount().' records affected';
    //foreach($results as $result){
    //    echo $result->{$column};
    //}
} else {
    echo $db->getError();
}

// SELECT
// Select all columns and return multiple rows
$table = 'users';
$result = $db->selectAll($table);

// Select specific columns and return multiple rows
$table = 'users';
$columns = 'firstname, surname';
$result = $db->selectAll($table, $columns);

// Select specific columns and return multiple rows using a 'where' string
$table = 'users';
$columns = 'firstname';
$where = "surname LIKE 'D%'";
$result = $db->selectAll($table, $columns, $where);

// Select all columns and return multiple rows using a 'where' array
$table = 'users';
$where = array('surname' => 'Doe');
$result = $db->selectAll($table, false, $where);

// Select one row using a 'where' string
$table = 'users';
$where = array('firstname' => 'John', 'surname' => 'Doe');
$result = $db->selectRow($table, false, $where);
// Select one row using a 'where' array
//-------------------------------------
$table = 'users';
$columns = 'id';
$where = array('firstname' => 'John', 'surname' => 'Doe');
$result = $db->selectRow($table, $columns, $where);

// Select one value using a 'where' array
$table = 'users';
$columns = 'id';
$where = array('firstname' => 'John', 'surname' => 'Doe');
$result = $db->selectOne($table, $columns, $where);

// Select multiple rows and order the results
$table = 'users';
$extra = 'ORDER BY surname ASC';
$result = $db->selectAll($table, false, false, $extra);

// SELECT RESULTS
// All results can be tested and outputted using $result
if ($result) {
    echo $db->rowCount().' records affected';
    //foreach($results as $result){
    //    echo $result->{$column};
    //}
} else {
    echo $db->getError();
}

// INSERT RECORDS
// Insert a record using 'bind' params
$table = 'users';
$columns = array('firstname' => 'Fred', 'surname' => 'Bloggs');
$result = $db->insert($table, $columns);

// INSERT STATUS
// Success can be tested using $result
if ($result) {
    echo $db->rowCount().' records affected';
} else {
    echo $db->getError();
}

// UPDATE RECORDS
// Update (all) records using 'bind' params 
$table = 'users';
$columns = array('firstname' => 'Fred', 'surname' => 'Bloggs');
$result = $db->update($table, $columns);

// Update records using 'bind' params and 'where' string
$table = 'users';
$columns = array('firstname' => 'Fred 2', 'surname' => 'Bloggs 2');
$where = "firstname = 'Fred' AND surname = 'Bloggs'";  //'WHERE' is not needed, or spaces
$result = $db->update($table, $columns, $where);

// Update specific records using 'bind' params and 'where' 
$table = 'users';
$columns = array('firstname' => 'Fred 2', 'surname' => 'Bloggs 2');
$where = array('firstname' => 'Fred', 'surname' => 'Bloggs');
$result = $db->update($table, $columns, $where);

// UPDATE STATUS
// Success can be tested using $result
if ($result) {
    echo $db->rowCount().' records affected';
} else {
    echo $db->getError();
}

// DELETE RECORDS
// Delete records using a 'where' string
$table = 'users';
$where = "surname = 'Doe'";
$result = $db->delete($table, $where);

// Delete records using a 'where' array
//-------------------------------------
$table = 'users';
$where = array('surname' => 'Doe');
$result = $db->delete($table, $where);

// DELETE STATUS
// Success can be tested using $result
if ($result) {
    echo $db->rowCount().' records affected';
} else {
    echo $db->getError();
}