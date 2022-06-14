<?php
//namespace API\Config;

  class Config 
  {

    public int $itemsPerPage = 12;
    public array $DB = array("host" => "localhost",
                             "port" => 3306, 
                             "user" => "test", 
                             "password" => "testroot", 
                             "database" => "Scandiweb",
                            );
  }
  ?>