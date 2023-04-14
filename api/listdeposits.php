<?php

require_once('../config/config.php');

if((!isset($_GET['myid'])||empty(trim($_GET['myid'])))){

    jsontell (["err" => "List deposits failed", "tell" => "Invalid myid"]);
}

if(!isset($_GET['offset'])||!is_numeric(trim($_GET['offset']))){

    jsontell (["err" => "List deposits failed", "tell" => "Invalid offset"]);
}

if(!isset($_GET['limit'])||!is_numeric(trim($_GET['limit']))){

    jsontell (["err" => "List deposits failed", "tell" => "Invalid limit"]);
}

$offset = ((int)$_GET['offset']);

$limit = ((int)$_GET['limit']);

$querylistdeposits="SELECT * FROM deposit WHERE myid = '{$_GET['myid']}' LIMIT {$limit} OFFSET {$offset}";

$resultlistdeposits = runquery($querylistdeposits,true);

jsontell (["suc" => "List deposits success", "tell" => $resultlistdeposits]);

