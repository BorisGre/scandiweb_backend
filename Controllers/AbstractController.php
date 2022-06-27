<?php

//use Scandiweb\ProductsAPI;

abstract class AbstractController
{
   protected $method; //GET|POST|PUT|DELETE|OTIONS|HEAD

   public $requestUri;
   public $requestParams;

   protected $action; //endpoint 
   protected $itemsPerPage;
 
   abstract public function run();
   
   abstract public function mapRequestToAction();

   abstract public function executeAction($mappedAction);
}
