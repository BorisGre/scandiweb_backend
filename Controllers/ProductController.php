<?php

class ProductController extends AbstractController
{
    protected $method; //GET|POST|PUT|DELETE
    private $productType = "";
   
    public function __construct($config, $data) {
      
      $this->config = $config;
      $this->itemsPerPage = $config->itemsPerPage;
      $this->reqArgs = $data;
      $this->method = $data['method'];
      $this->endpointArgs = $this->reqArgs['data'];
    }

    public function parseGETUri(){

      $page = 0; 
      $pattern = "/\?page=[0-9]{1,5}/";
      preg_match($pattern, $this->reqArgs['data'], $matches);

      if(empty($matches) === false AND $matches[0] === $this->reqArgs['data']){

        $splited = explode("=", $this->reqArgs['data']);
        $splitedLastElementIndex = count($splited)-1;
        
        if(isset($splited[$splitedLastElementIndex]) AND $splited[$splitedLastElementIndex]){

           $page = $splited[$splitedLastElementIndex];
        }
     }

     return $page;
    }

    public function prepareArgs(){

      if($this->method === "GET"){

         $this->endpointArgs = $this->reqArgs['data'] === "" ? 0 : intval($this->parseGETUri());
      }  

      if(in_array($this->method, ["POST", "PUT"])){

        $this->productType = isset($this->endpointArgs['type']) ? $this->endpointArgs['type'] : $this->productType;
        $this->productType = ucfirst(strtolower($this->productType));
       // $this->endpointArgs['type'] = strtoupper($this->productType);

        if(strlen($this->productType) === 0){

          throw new Exception("productType error".$this->productType);
        }
      }
      
      if($this->method === "PUT"){

        $lastRouteIndex = count($this->reqArgs['route'])-1;
        $this->endpointArgs['sku'] = $this->reqArgs['route'][$lastRouteIndex];
      }
    }

    /* Lazy loading of Product Models */
    public function mapRequestToAction(){

      $path = "./Model/";
      $typeOfClass = 'Product';
      $className = $this->productType;

      $defaultModelClass = "Main";
      $classNameArray = ['Abstract'];
      $productClass = null;
      
      array_push($classNameArray, $defaultModelClass);

      if(isset($className) === true AND strlen($className) > 0){
            
            array_push($classNameArray, $className);
      } 

      foreach($classNameArray as $className){ 
              
                $productClass = LoadClasses::loadClass($className.$typeOfClass, $path); 
      }

      //var_dump($this->productType, $classNameArray);
 
      $productObj = new $productClass($this->config);    

      $methodToAction = [  "POST" => fn() => $productObj->addProduct($this->endpointArgs),   //newProduct
                            "GET" => fn() => $productObj->getProduct($this->endpointArgs),   //page
                            "PUT" => fn() => $productObj->updateProduct($this->endpointArgs),//updatedProduct
                         "DELETE" => fn() => $productObj->deleteProduct($this->endpointArgs) //sku
                        ];
                         
      return $methodToAction[$this->method];
    }

    public function executeAction($mappedAction){

        return $mappedAction();
    }

    public function run(){

      //echo "ProductController RUN\n";
      $this->prepareArgs();

      $mappedAction = $this->mapRequestToAction();
      return $this->executeAction($mappedAction);
    }
}