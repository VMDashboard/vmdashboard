<?php

require('header.php');
require('navigation.php');
$uuid = "aaa9693e-79dd-4f14-9240-98b30c16b5b2"
$domName = $lv->domain_get_name_by_uuid($uuid);
$dom = $lv->get_domain_object($domName);
$ret = domain_get_memory_stats($domName);
var_dump($ret);

require('footer.php');

 ?>
