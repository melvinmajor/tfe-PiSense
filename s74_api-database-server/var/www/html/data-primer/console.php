<?php
include'vendor/autoload.php';
if(!file_exists('config.php')) {
  echo "Rename config-example.php into config.php and edit\n";
  exit(1);
}
include'config.php';
$db = atk4\data\Persistence::connect($c[0],$c[1],$c[2]);

eval(\Psy\sh());
