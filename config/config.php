<?php

define("HOST", "localhost");

define("USERNAME", "root");

define("PASSWORD", "");

define("DATABASE", "refer");

define("SPLIT",['my' => 95, "refer" => 5]);

define("FIRSTSPLIT",['my' => 93, "refer" => 7 ]);

define("BONUS", 0);

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

function adddeposit(string $myid, int $amount){

$querygetreferid="SELECT referid FROM refer WHERE myid = '{$myid}'";

$resultgetreferid = runquery($querygetreferid,true);

if($resultgetreferid['num'] > 0){

    $done = true;

    $referid = $resultgetreferid['rows'][0]['referid'];

    $mysplit = (($amount * FIRSTSPLIT['my']) / 100);

    $refersplit = (($amount * FIRSTSPLIT['refer']) / 100);
    
    $querygetfirstdeposit="SELECT * FROM deposit WHERE myid = '{$myid}' AND referid = '{$referid}' LIMIT 1";

    $resultgetfirstdeposit = runquery($querygetfirstdeposit,true);
    
    if($resultgetfirstdeposit['num'] > 0){
        
        $mysplit = (($amount * SPLIT['my']) / 100);

        $refersplit = (($amount * SPLIT['refer']) / 100);
        
    }

    $queryinsertdeposit="INSERT INTO deposit (myid, referid, mysplit, refersplit, amount) VALUES ('{$myid}', '{$referid}', '{$mysplit}', '{$refersplit}', '{$amount}')";

    $resultinsertdeposit = runquery($queryinsertdeposit);

    if(!$resultinsertdeposit){

        $done = false;

    }else{

        $querygetreferamount="SELECT amount FROM total WHERE referid = '{$referid}'";

        $resultgetreferamount = runquery($querygetreferamount,true);

        if($resultgetreferamount['num'] > 0){

            $newamount = ($resultgetreferamount['rows'][0]['amount'] + $refersplit);

            $queryupdatereferamount="UPDATE total SET amount = '{$newamount}' WHERE referid = '{$referid}'";
    
            $resultupdatereferamount = runquery($queryupdatereferamount);

            if(!$resultupdatereferamount){

                $done = false;
            }

        }else{

            $queryinsertreferamount="INSERT INTO total (referid, amount) VALUES ('{$referid}', '{$refersplit}')";

            $resultinsertreferamount = runquery($queryinsertreferamount);

            if(!$resultinsertreferamount){

                $done = false;
            }
        }
    }

    if($done == false){

        $querydeletedeposit="DELETE FROM deposit WHERE myid = '{$myid}' AND referid = '{$referid}' AND  amount = '{$amount}'";

        runquery($querydeletedeposit);
        
        return false;
    }else{

        return true;
    }
}else{

    return false;
}
    
}

function addrefer(string $myid, string $referid){

$queryaddrefer="INSERT INTO refer (myid, referid) VALUES ('{$myid}', '{$referid}')";

$resultaddrefer = runquery($queryaddrefer);

if($resultaddrefer){

    $bonusdone = true;

    $querygetamount="SELECT amount FROM total WHERE referid = '{$referid}'";

    $resultgetamount = runquery($querygetamount,true);

    if($resultgetamount['num'] > 0){

        $newamount = ($resultgetamount['rows'][0]['amount'] + BONUS);

        $queryupdateamount="UPDATE total SET amount = '{$newamount}' WHERE referid = '{$referid}'";
    
        $resultupdateamount = runquery($queryupdateamount);

        if(!$resultupdateamount){

            $bonusdone = false;
        }

    }else{

        $queryinsertamount="INSERT INTO total (referid, amount) VALUES ('{$referid}', '".BONUS."')";
    
        $resultinsertamount = runquery($queryinsertamount);

        if(!$resultinsertamount){

            $bonusdone = false;
        }
    }

    if($bonusdone == false){

        $querydeleterefer="DELETE FROM refer WHERE myid = '{$myid}'";

        runquery($querydeleterefer);

        return false;
    }else{

    return true;
    }
}else{

    return false;
}
    
}

function getreferidamount(string $referid){
    
$querygetreferidamount="SELECT amount FROM total WHERE referid = '{$referid}'";

$resultgetreferidamount = runquery($querygetreferidamount,true);

if($resultgetreferidamount['num'] > 0){
    
    return $resultgetreferidamount['rows'][0]['amount'];

}else{

    return false;

}
    
}

function withdrawamount(string $referid,int $amount){
    
    $readyamount = getreferidamount($referid);

    if ($readyamount == false||$readyamount < $amount){

        return false;

    }

    $newamount = ($readyamount - $amount);

    $queryupdatereferamount="UPDATE total SET amount = '{$newamount}' WHERE referid = '{$referid}'";
    
    $resultupdatereferamount = runquery($queryupdatereferamount);

    if(!$resultupdatereferamount){

        return false;
    }else{

        return true;

    }

}

function listdeposits(string $myid, int $limit, int $offset){

$querylistdeposits="SELECT * FROM deposit WHERE myid = '{$myid}' LIMIT {$limit} OFFSET {$offset}";

$resultlistdeposits = runquery($querylistdeposits,true);

return $resultlistdeposits;
    
}

function listreferidsplits(string $referid, int $limit, int $offset){
    
$queryreferidsplits="SELECT * amount FROM deposit WHERE referid = '{$referid}' LIMIT {$limit} OFFSET {$offset}";

$resultreferidsplits = runquery($queryreferidsplits,true);

return $resultreferidsplits;
    
}

