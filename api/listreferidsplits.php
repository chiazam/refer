<?php

require_once('../config/config.php');

if((!isset($_GET['referid'])||empty(trim($_GET['referid'])))){

    jsontell (["err" => "List refer id splits failed", "tell" => "Invalid referid"]);
}

if(!isset($_GET['offset'])||!is_numeric(trim($_GET['offset']))){

    jsontell (["err" => "List refer id splits failed", "tell" => "Invalid offset"]);
}

if(!isset($_GET['limit'])||!is_numeric(trim($_GET['limit']))){

    jsontell (["err" => "List refer id splits failed", "tell" => "Invalid limit"]);
}

$offset = ((int)$_GET['offset']);

$limit = ((int)$_GET['limit']);

$queryreferidsplits="SELECT * amount FROM deposit WHERE referid = '{$_GET['referid']}' LIMIT {$limit} OFFSET {$offset}";

$resultreferidsplits = runquery($queryreferidsplits,true);

jsontell (["suc" => "List refer id splits success", "tell" => $resultreferidsplits]);

