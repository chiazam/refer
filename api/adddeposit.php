<?php

require_once('../config/config.php');

if(!isset($_POST['myid'])||empty(trim($_POST['myid']))){

    jsontell (["err" => "Add deposit failed", "tell" => "Invalid myid"]);
}

if(!isset($_POST['amount'])||!is_numeric(trim($_POST['amount']))){

    jsontell (["err" => "Add deposit failed", "tell" => "Invalid amount"]);
}

$querygetreferid="SELECT referid FROM refer WHERE myid = '{$_POST['myid']}'";

$resultgetreferid = runquery($querygetreferid,true);

if($resultgetreferid['num'] > 0){

    $done = true;

    $referid = $resultgetreferid['rows'][0]['referid'];

    $amount = ((int)$_POST['amount']);

    $mysplit = (($amount * SPLIT['my']) / 100);

    $refersplit = (($amount * SPLIT['refer']) / 100);

    $queryinsertdeposit="INSERT INTO deposit (myid, referid, mysplit, refersplit, amount) VALUES ('{$_POST['myid']}', '{$referid}', '{$mysplit}', '{$refersplit}', '{$amount}')";

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

        $querydeletedeposit="DELETE FROM deposit WHERE myid = '{$_POST['myid']}' AND referid = '{$referid}' AND  amount = '{$amount}'";

        runquery($querydeletedeposit);

        jsontell (["suc" => "Add deposit failed", "tell" => "Try again"]);
    }else{

    jsontell (["suc" => "Add deposit success"]);
    }
}else{

    jsontell (["err" => "Add deposit failed", "tell" => "Refer not found"]);
}