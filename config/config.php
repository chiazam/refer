<?php

define("HOST", "localhost");

define("USERNAME", "root");

define("PASSWORD", "");

define("DATABASE", "refer");

define("SPLIT",['my' => 70, "refer" => 30 ]);

define("BONUS", 20);

function jsontell (array $tell){

    header('Content-type: application/json');

    die(json_encode($tell));
}

$conn = mysqli_connect(HOST, USERNAME, PASSWORD, DATABASE);

if(!$conn) {

    jsontell (["err"=>"Database connection failed", "tell"=>"Cross check database credentials..."]);
}

function runquery(string $query, bool $assoc = false){

    global $conn;

    $result = mysqli_query($conn, $query);

    if ($result) {

       if($assoc == true){

            return fetchassoc($result);
       }else{

            return true;
       }
    }else{

    echo  (mysqli_error($conn)/* ."... on file ".__FILE__." one line ".__LINE__ */);

        return false;
    }
}

function fetchassoc(mysqli_result $result){

    $numrows = mysqli_num_rows($result);

    $rows = [];

    if ($numrows  > 0) {
        
        while($row = mysqli_fetch_assoc($result)) {

            array_push($rows, $row);
        }

        return ['num' => $numrows, 'rows' => $rows];
    } else {
        
        return ['num'=>$numrows];

    }

}