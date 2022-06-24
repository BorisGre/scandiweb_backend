<?php
  //namespace Scandiweb;
  define('__BASE_DIR__', '.');
  define('__ENV__', 'dev');
  require_once 'LoadClasses.php';

  try {

    $loadClass = new LoadClasses();
    $loadClass->initLoad(__BASE_DIR__);

    $config = new Config(__ENV__);
    $api = new API($config, $loadClass);
    echo $api->run();
    echo "End of script";

  } catch (Exception $e) {
      echo json_encode(['error' => $e->getMessage()]);
  }
?>