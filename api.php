<?php
//namespace Scandiweb;

class API
{
   protected $method; //GET|POST|PUT|DELETE|OPTIONS|HEAD

   public $requestUri;
   public $requestParams;

   private $currentController = null;
   private array $route = [];
   private $config;
   private $statusCode = 200;
   private $rawResult = "";

   private array $parsedData = ["method" => "", "route" => [], "data" => "", "requestParams" => ""];
   
   public function __construct($config){

        $this->config = $config;
   }

   public function parse(){

        $implementedMethods = ["GET", "POST", "PUT", "DELETE", "OPTIONS", "HEAD"];
        $this->method = $_SERVER['REQUEST_METHOD'];
 

        if(in_array($this->method, $implementedMethods) === false){ 

            $this->$statusCode = 501;
            return $this->rawResult;
        }  

        $this->parsedData['method'] = $this->method;

        if($this->method === "OPTIONS"){

            return $this->rawResult = $implementedMethods;
        }

        if($this->method === "HEAD"){

            return $this->rawResult;
        }


        $this->requestUri = explode('/', trim($_SERVER['REQUEST_URI'],'/'));
        $lastElementIndexOfUri = count($this->requestUri)-1;

        if(in_array($this->method, ["POST", "PUT"])){
        
            $this->parsedData['data']  = json_decode(file_get_contents('php://input'), true);
            $this->parsedData['route'] = $this->requestUri;

        } else {

            $this->parsedData['data']  = $this->requestUri[$lastElementIndexOfUri];
            $this->parsedData['route'] = array_slice($this->requestUri, 0, $lastElementIndexOfUri);
        }

        $this->parsedData['requestParams'] = $this->requestUri;
 

       /* foreach($this->requestUri as $uri){

            preg_match("/^\/?[\w]+/", $uri, $matches);

            if(empty($matches) === false){
                array_push($this->route, $uri);
            }  

            $this->requestParams = $uri;
            $this->parsedData['requestParams'] = $uri;
        }

        return $implementedMethods;*/
    }

    public function run(){

          $this->parse();
          
          if($this->method === "OPTIONS" OR $this->method === "HEAD"){

                $res = $this->prepareResponse($this->statusCode, $this->rawResult);
                return $this->response($res);
          }
         
          $mappedController = $this->mapEndpointToController();
     
          if(isset($mappedController) === false){

             $this->statusCode = 404;
             $this->prepareResponse($this->statusCode, $this->rawResult);
             return $this->response($this->rawResult);   
          }                            

          try {

            $this->rawResult = $this->runController($mappedController);   
            $preparedResponse = $this->prepareResponse($this->statusCode, $this->rawResult);
            return $this->response($preparedResponse);    

          } catch (Exception $e){
             
             $this->statusCode = 500;
             $this->prepareResponse($this->statusCode, $this->rawResult);
             return $this->response($this->rawResult);
          }
    }

   /* Lazy loading of Controlles */
   public function mapEndpointToController(){

      $typeOfClass = "Controller";
      $path = "Controllers/";
    
      $className = null;
      $classNameArray = ['Abstract'];
      $controllerClass = null; 

      $defaultEndpoint = "product";
      $endpoints = ["Product" => "[a-z]{0,10}product(a-z){0,10}"];

      //all wrong uri redirects to Product controller;
      $endpoint = count($this->route) === 0 ? $defaultEndpoint : $this->route[0];
      
      foreach($endpoints as $controller => $pattern){
           
            preg_match("/".$pattern."/", $endpoint, $matches);

            if(!empty($matches) AND isset($className) === false){
                
                if($matches[0] === $endpoint){
                
                    $className = $controller;
                    array_push($classNameArray, $className);
                }    
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

            $this->currentController = new $ControllerClass($this->config, $this->parsedData);
            return $this->currentController->run();
    }

    public function getStatusMessage($statusCode) {

            $statusMessage = [
                    200 => 'OK',
                    404 => 'Not Found',
                    405 => 'Method Not Allowed',
                    500 => 'Internal Server Error',
                    501 => 'Not Implemented',
                ];
            return ($statusMessage[$statusCode]) ? $statusMessage[$statusCode] : $statusMessage[500];
        }

    protected function prepareResponse($statusCode, $data){

        header("Access-Control-Allow-Orgin: *");
        header("Access-Control-Allow-Methods: *");
        header("Content-Type: application/json");
        header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");
        header("HTTP/1.1 " . $statusCode . " " . $this->getStatusMessage($statusCode));
        
        return $data;
    }

   
    public function response($data){
        
        return json_encode($data);
    }
}
?>