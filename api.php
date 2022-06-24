<?php
//namespace Scandiweb;

class API
{
   protected $method; //GET|POST|PUT|DELETE

   public $requestUri;
   public $requestParams;

   protected $action; //endpoint 

   private $statusCode = 200;

   private $currentController;
   private $endpoint;
   private $config;
   private $parsedData = ["method" => 'GET', "productType" => null];
   private $classLoader;

   
   public function __construct($config){

        $this->config = $config;
   }

   public function parse(){

        echo "START Parse\n";

        $this->requestUri = explode('/', trim($_SERVER['REQUEST_URI'],'/'));
        $this->requestParams = $_REQUEST;
         // QUERY_STRING
         //
        $allMethods = ["GET", "POST", "PUT", "DELETE"];
        $allwodeMethods = $allMethods;
        $this->method = $_SERVER['REQUEST_METHOD'];
        $endpoints = ["/", "addproduct"];

        $methodToData = ["GET" => $_GET, "POST" => $_POST, "PUT" => $_POST, "DELETE" => $_POST];


        echo "METHOD ".$this->method."\n";
        var_dump($_GET, $_REQUEST, $_SERVER, $_POST);

        if($this->method === "GET"){
            $this->parsedData = $_GET;
        }
        $this->parsedData = ["method" => $this->method, "productType" => ""];/**/
        //$this->parsedData = $methodToData[$this->method];

        //$getData = $_GET


        /*if(in_array($this->method, $allwodeMethods) === false){

            $this->statusCode = 405;
        }

        if(array_key_exists('HTTP_X_HTTP_METHOD', $_SERVER) === false OR in_array($this->method, $allMethods) === false){ 
            
            $this->statusCode = 404;
            throw new Exception("Unexpected Method on parse");
        }   

        if($this->method === "GET"){

            $this->methodParams = $this->requestUri[0];
        }

        if($this->method !== "GET" AND in_array($this->method, $methods)){

            $this->methodParams = $_POST['params'];
        }   */
        $this->endpoint = "";
        //return ;
    }

   public function run(){

        // return  
        $this->parse();
         echo "AFTER PARSER.\n";
          $mappedController = $this->mapEndpointToController();
          $rawResult = [];
     
          if(isset($mappedController) === false){

             $this->statusCode = 404;
             $this->prepareResponse($this->statusCode, $rawResult);
             return $this->response($rawResult);   
          }                            

          try {

            $rawResult = $this->runController($mappedController);   
            $preparedResponse = $this->prepareResponse($this->statusCode, $rawResult);
            return $this->response($preparedResponse);    

          } catch (Exception $e){
             
             $this->statusCode = 500;
             $this->prepareResponse($this->statusCode, $rawResult);
             return $this->response($rawResult);
          }
   }

   /* Lazy loading of Controlles */
   public function mapEndpointToController(){

      $typeOfClass = "Controller";
      $path = "Controllers/";
      $defaultEndpoint = [          "" => "Product", 
                          "addproduct" => "Product"];

      $endpoints = ["Product" => "[a-z]{0,10}product(a-z){0,10}"];
       
      $classNameArray = ['Abstract'];
      $controllerClass = null;

      if(array_key_exists($this->endpoint, $defaultEndpoint)){
            
            array_push($classNameArray, $defaultEndpoint[$this->endpoint]);
      } 
 
      foreach($endpoints as $endpoint => $pattern){
           
            preg_match("/".$pattern."/", $this->endpoint, $matches);
            
            if(!empty($matches) AND isset($className) === false){

                $className = $endpoint;
            }
      }

      foreach($classNameArray as $className){ 

            if(isset($className) === true){
                        
                $controllerClass = LoadClasses::loadClass($className.$typeOfClass, $path);  
            }
      }
        
      return $controllerClass;
    }
 
    public function runController($ControllerClass){

            var_dump($this->config, $this->parsedData);

            $this->currentController = new $ControllerClass($this->config, $this->parsedData);
            return $this->currentController->run();
    }

    public function getStatusMessage($statusCode) {

            $statusMessage = [
                    200 => 'OK',
                    404 => 'Not Found',
                    405 => 'Method Not Allowed',
                    500 => 'Internal Server Error',
                ];
            return ($statusMessage[$statusCode]) ? $statusMessage[$statusCode] : $statusMessage[500];
        }

    protected function prepareResponse($statusCode, $data){

        header("Access-Control-Allow-Orgin: *");
        header("Access-Control-Allow-Methods: *");
        header("Content-Type: application/json");
        header("HTTP/1.1 " . $statusCode . " " . $this->getStatusMessage($statusCode));
        
        return $data;
    }

   
    public function response($data){
        
        return json_encode($data);
    }
}
?>