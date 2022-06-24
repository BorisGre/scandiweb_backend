<?php

class ProductController extends AbstractController
{
   protected $method; //GET|POST|PUT|DELETE
   protected $action; //endpoint 
   
   public function __construct($config, $parsedData) {
    
     $this->config = $config;
     $this->itemsPerPage = $config->itemsPerPage;
     $this->method = $parsedData['method'];
     $this->productType = $parsedData['productType'];
   }

   /* Lazy loading of Product Models */
   public function mapRequestToAction(){

      $path = "./Model/";
      $typeOfClass = 'Product';
      $className = $this->productType;          
      $classNameArray = ['Abstract', 'Main'];
      $productClass = null;
      
      if(isset($className) === true AND strlen($className) > 0){
            
            array_push($classNameArray, $className);
      } 
      
      foreach($classNameArray as $className){ 
              
                $productClass = LoadClasses::loadClass($className.$typeOfClass, $path);    
      }
 
      $productObj = new $productClass($this->config);        
      
      $methodToAction = array("POST" => fn() => $productObj->addProduct(),   //newProduct
                               "GET" => fn() => $productObj->getProduct(),   //page
                               "PUT" => fn() => $productObj->updateProduct(),//updatedProduct
                            "DELETE" => fn() => $productObj->deleteProduct() //skuArray
                        );
      
      return $methodToAction[$this->method];
    }

    public function executeAction($mappedAction){
        echo  "mapped ACTION";
        return $mappedAction();
    }

    public function run(){
        echo "ProductController RUN\n";
      
        $types = [];
        $mappedAction = $this->mapRequestToAction();

        echo "mapped action\n";

        //var_dump($this->executeAction($mappedAction));

        return $this->executeAction($mappedAction);
  }
}