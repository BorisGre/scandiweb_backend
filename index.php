<?php
  require_once 'ProductsController.php';
  //namespace API;
  //use API\Config;
  require_once 'autoload.php';
  require_once 'config.php';

  try {
    
    $config = new Config();
    $api = new ProductsAPIController($config);
    var_dump($api->run());
     echo "AAA";
     //echo $api->run();
  } catch (Exception $e) {
      echo json_encode(Array('error' => $e->getMessage()));
  }
?>