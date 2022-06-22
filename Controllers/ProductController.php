<?php

class ProductController extends AbstractController
{
   protected $method; //GET|POST|PUT|DELETE
   protected $action; //endpoint 

   protected $DB;
   
   public function __construct($config, $classLoader, $parsedData, $DB) {
    
     $this->classLoader = $classLoader;
     $this->itemsPerPage = $config->itemsPerPage;
     $this->DB = ""; //new $DB($config->DB);
     $this->method = $parsedData['method'];
     $this->productType = $parsedData['productType'];
   }

   public function mapRequestToAction(){

      $path = "./Model";
      $typeOfClass = 'Product';
      $className = $this->productType;          
      $classNameArray = ['Abstract', 'Main'];
      $productClass = null;
      
      if(isset($className) === true){
            
            array_push($classNameArray, $className);
      } 
      
      foreach($classNameArray as $className){ 
      
                $productClass = $this->classLoader::loadClass($className.$typeOfClass, $path);    
                //var_dump($controllerClass, $className.$typeOfClass);
      }
      
      $productObj = new ProductClass($this->itemsPerPage, $this->DB);        
      
      $methodToAction = array("POST" => $productObj->addProduct, 
                               "GET" => $productObj->getProduct,
                               "PUT" => $productObj->updateProduct,
                            "DELETE" => $productObj->deleteProduct
                        );
      
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