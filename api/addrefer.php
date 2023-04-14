<?php

require_once('../config/config.php');

if(!isset($_POST['myid'])||empty(trim($_POST['myid']))){

    jsontell (["err" => "Add refer failed", "tell" => "Invalid myid"]);
}

if(!isset($_POST['referid'])||empty(trim($_POST['referid']))){

    jsontell (["err" => "Add refer failed", "tell" => "Invalid referid"]);
}

$queryaddrefer="INSERT INTO refer (myid, referid) VALUES ('{$_POST['myid']}', '{$_POST['referid']}')";

$resultaddrefer = runquery($queryaddrefer);

if($resultaddrefer){

    $bonusdone = true;

    $querygetamount="SELECT amount FROM total WHERE referid = '{$_POST['referid']}'";

    $resultgetamount = runquery($querygetamount,true);

    if($resultgetamount['num'] > 0){

        $newamount = ($resultgetamount['rows'][0]['amount'] + BONUS);

        $queryupdateamount="UPDATE total SET amount = '{$newamount}' WHERE referid = '{$_POST['referid']}'";
    
        $resultupdateamount = runquery($queryupdateamount);

        if(!$resultupdateamount){

            $bonusdone = false;
        }

    }else{

        $queryinsertamount="INSERT INTO total (referid, amount) VALUES ('{$_POST['referid']}', '".BONUS."')";
    
        $resultinsertamount = runquery($queryinsertamount);

        if(!$resultinsertamount){

            $bonusdone = false;
        }
    }

    if($bonusdone == false){

        $querydeleterefer="DELETE FROM refer WHERE myid = '{$_POST['myid']}'";

        runquery($querydeleterefer);

        jsontell (["suc" => "Add refer failed", "tell" => "Try again"]);
    }else{

    jsontell (["suc" => "Add refer success"]);
    }
}else{

    jsontell (["suc" => "Add refer failed", "tell" => "Try again"]);
}

