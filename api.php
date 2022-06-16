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
   private $config;
   private $parsedData;
   private $classLoader;

   
   public function __construct($config, $classLoader) {

        $this->classLoader = $classLoader;
        $this->config = $config;
   }

   public function parse(){

        $this->requestUri = explode('/', trim($_SERVER['REQUEST_URI'],'/'));
        $this->requestParams = $_REQUEST;
         // QUERY_STRING
         //
        $allMethods = ["GET", "POST", "PUT", "DELETE"];
        $allwodeMethods = $allMethods;
        $this->method = $_SERVER['REQUEST_METHOD'];
        $endpoints = ["/", "addproduct"];

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
    }

   public function run(){

          $this->parse();
        
          $mappedController = $this->mapEndpointToController();
          $rawResult = [];

          echo "ISSET: ".$mappedController."\n"; 
           
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

   public function mapEndpointToController(){

      $typeOfClass = "Controller";
      $defaultEndpoint = ["" => "Product"];

      $endpoints = ["Product" => "[a-z]{0,10}product(a-z){0,10}"];
       
      $className = null;
      $controllerClass = null;

      if(array_key_exists($this->endpoint, $defaultEndpoint)){
            
            $className = $defaultEndpoint[$this->endpoint];
      } 
 
      foreach($endpoints as $endpoint => $pattern){
           
            preg_match("/".$pattern."/", $this->endpoint, $matches);
            
            if(!empty($matches) AND isset($className) === false){

                $className = $endpoint;
            }
      }

      if(isset($className) === true){
    
        $path = "Controllers/";
      
        $controllerClass = $this->classLoader::loadClass($className.$typeOfClass, $path);    
        //var_dump($controllerClass, $className.$typeOfClass);
      }
        
     return $controllerClass;
   }
 
   public function runController($ControllerClass){
  
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