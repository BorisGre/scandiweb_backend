<?php

//use Scandiweb\ProductsAPI;

abstract class AbsctractAPIController
{
   protected $method; //GET|POST|PUT|DELETE

   public $requestUri;
   public $requestParams;

   protected $action; //endpoint 
   protected $itemsPerPage;
 
   abstract public function run();
   
   abstract public function mapRequestToAction();

   abstract public function executeAction($mappedAction);

   abstract public function getStatusMessage($statusCode);

   abstract protected function prepareResponse($data);

   abstract public function response($statusCode, $data);
}
