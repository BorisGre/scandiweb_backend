<?php
//namespace Scandiweb;

  class Config 
  {
    public function __construct($env = 'dev'){
      $this->env = $env;
    }

    public int $itemsPerPage = 12;
    
    private array $prodDB = array("host" => "localhost",
                            "port" => 3306, 
                            "user" => "test", 
                            "password" => "J=C5Xrzd-T]7olOO", 
                            "database" => "Scandiweb",
                           );

    private array $devDB = array("host" => "localhost",
                                  "port" => 3306, 
                                  "user" => "test", 
                                  "password" => "testroot", 
                                  "database" => "Scandiweb",
                                 );                           

    public array $DB = array("host" => "localhost",
                              "port" => 3306, 
                              "user" => "test", 
                              "password" => "testroot", 
                              "database" => "Scandiweb",
   );       //$this->env == 'dev' ? $devDB : $prodDB;
  }
?>