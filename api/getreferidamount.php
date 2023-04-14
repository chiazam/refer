<?php

require_once('../config/config.php');

if(!isset($_GET['referid'])||empty(trim($_GET['referid']))){

    jsontell (["err" => "Get referid amount failed", "tell" => "Invalid referid"]);
}

$querygetreferidamount="SELECT amount FROM total WHERE referid = '{$_GET['referid']}'";

$resultgetreferidamount = runquery($querygetreferidamount,true);

if($resultgetreferidamount['num'] > 0){

    jsontell (["suc" => "Get referid amount success", "tell" => $resultgetreferidamount['rows'][0]]);

}else{

    jsontell (["err" => "Get referid amount failed", "tell" => "Refer not found"]);

}