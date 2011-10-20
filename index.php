<?php
//Boot strap

$root = dirname(__FILE__);

include_once($root . '/System/System.php');

System::run(array(
    "system_config_path" => $root . '/System/core/etc/config.xml'
));