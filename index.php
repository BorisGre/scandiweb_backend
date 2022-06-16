<?php
  //namespace Scandiweb;
  define('__BASE_DIR__', '.');
  
  require_once 'LoadClasses.php';

  try {

    $loadClass = new LoadClasses();
    $loadClass->initLoad(__BASE_DIR__);

   // var_dump(get_declared_classes()); 
   // var_dump(LoadClasses);
    $config = new Config();
    $api = new API($config, $loadClass);
    var_dump($api->run());
   
    ///a = new LoadClasses;
   
     echo "AAA";
     //echo $api->run();
  } catch (Exception $e) {
      echo json_encode(Array('error' => $e->getMessage()));
  }
?>