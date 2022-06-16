<?php

//use Scandiweb\ProductsAPI\ProductsAPICOn;
//use Scandiweb\ProudctsAPI\Product;
require_once "DB.php";
require_once "Model/product.php";
require_once "AbsctractController.php";

class ProductController extends AbsctractController
{
   protected $method; //GET|POST|PUT|DELETE
   protected $action; //endpoint 

   protected $DB;
   
   public function __construct($config, $parsedData) {

     $this->itemsPerPage = $config->itemsPerPage;
     $this->DB = new DB($config->DB);
   }

   public function mapRequestToAction(){

       if($this->method !== 'POST' OR $this->method === 'PUT'){

           $typeOfClass = 'Product';
           $className = $this->methodParams['product']['type'].$typeOfClass;

           $ProductClass = $this->loadClasses::loadClass($className, $path);
           $productObj = new ProductClass($this->itemsPerPage, $this->DB);        

       } else {

           $productObj = new Product($this->itemsPerPage, $this->DB);  
       }

       //var_dump($productObj);
      $methodToAction = array("POST" => $productObj->addProduct, 
                              "GET" => $productObj->getProduct,
                              "PUT" => $productObj->updateProduct,
                              "DELETE" => $productObj->deleteProduct
                             );

       var_dump($methodToAction);
      return $methodToAction[$this->method];
    }

    public function executeAction($mappedAction){

    }

    public function run(){
        echo "ProductController RUN\n";
       // $this->getRequestParams();
        //$types = $this->DB->get('types');
        $types = [];
       // $mappedAction = $this->mapRequestToAction($types);

        return [];// $this->executeAction($mappedAction);
  }
}