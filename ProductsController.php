<?php

//use Scandiweb\ProductsAPI\ProductsAPICOn;
//use Scandiweb\ProudctsAPI\Product;
require_once "DB.php";
require_once "Model/product.php";
require_once "AbsctractAPIController.php";

class ProductsAPIController extends AbsctractAPIController
{
   protected $method; //GET|POST|PUT|DELETE

   public $requestUri;
   public $requestParams;

   protected $action; //endpoint 

   protected $DB;

   protected $itemsPerPage;
   private $pageNumber;
   private $product;
   private $skuArray;

   
   public function __construct($config) {

     $this->itemsPerPage = $config->itemsPerPage;
     $this->DB = new DB($config->DB);
   }

   public function parse(){

        $this->requestUri = explode('/', trim($_SERVER['REQUEST_URI'],'/'));
        $this->requestParams = $_REQUEST;
         // QUERY_STRING
         //
        $methods = ["GET", "POST", "PUT", "DELETE"];
        $this->method = $_SERVER['REQUEST_METHOD'];

        //$mapParamsToMethod

        //Endpoint 
        //$this->requestUri;

        if($this->method === "GET"){
            $this->pageNumber = $this->requestUri[0];
        }

        if($this->method === "POST" AND $this->requestUri[0] === 'addproduct'){
            $this->product = $_POST['newproduct'];
        }


        /*var_dump($_SERVER);
            echo "bbb";*/
        var_dump(
            $_SERVER,
         $this->requestUri,
         //$_POST,
         $_REQUEST,
        //, $this->requestParams );
        );
        echo $_REQUEST['newProduct'];
        $this->post = $_POST;
            /*echo "ccc";
        var_dump($_REQUEST);*/

        $postParams = $_POST;
        
        $this->pageNumber = "";
        $this->skuArray = "";
        $this->product = "";
    
    if(array_key_exists('HTTP_X_HTTP_METHOD', $_SERVER) === false OR in_array($this->method, $methods) === false){ 
       new Exception("Unexpected Method");
    } 
   }

    public function lazyLoadClass($type, $types){

        if(array_key_exists($type, $types) AND file_exists($type.'Product.php')){
         
            spl_autoload_register(fn($type) => require_once $type . 'Product.php');

            return true;
        }
        return false;
   }

   public function run(){

          //$this->parse();
         // var_dump($this->DB->describe("product"));
         /* $ms = new mysqli;
          $ms->real_connect("localhost", "test", "testroot", "Scandiweb");
          $ms->real_query("select * from product limit 0,12");
          //$ms->real_query("describe product");
          if ($result = $ms->use_result()){
            echo "CCC";
            $rows = mysqli_fetch_all($result, MYSQLI_ASSOC);
            foreach ($rows as $row) {
               var_dump($row);
            }
            $result->close();
          }*/

          //$data = $this->DB->get("product", 0, $this->itemsPerPage);
          //$data = $this->DB->count("product");
          $this->DB->delete("product", ['sku' => "ABC1234", "sku" => "ABCD234"]);
          var_dump($data);
          /*$actionToExecute = $this->mapRequestToAction();
          $rawResponse = $this->executeAction($actionToExecute);*/

          //$this->response($rawResponse);
          
          //return [$this->method, $this->requestUri, $this->requestParams];
   }

   public function fetch(){

   }
 
   public function mapRequestToAction(){}

   /*public function mapRequestToAction(){

       if($this->method === 'POST' OR $this->method === 'PUT'){

           $types = $this->DB->getTypes(); 
           $this->product = array_key_exists('product', $postedData) ? $postedData['product'] : "";
           $type = array_key_exists('type', $product) ? $product['type'] : "";

           $this->lazyLoadClass($type, $types); 
           $className = array_pop(get_declared_classes());
           $Obj = new className();          
       }

      //$product = new Product($this->itemsPerPage);  
       //var_dump($product);
      $methodToAction = array("GET" => fn() => Product::getProduct($this->$pageNumber), 
                              "POST" => fn() => $Obj->addProduct($this->product), 
                              "PUT" => fn() => $Obj->updateProduct($this->product),
                              "DELETE" => fn() => Product::deleteProduct($this->skuArray),
                             );

       var_dump($methodToAction);
      //return $methodToAction[$this->method];
   }*/
 
   public function executeAction($mappedAction){

       return $this->prepareResponse($mappedAction());   
   }

   public function getStatusMessage($statusCode) {

        $statusMessage = array(
            200 => 'OK',
            404 => 'Not Found',
            405 => 'Method Not Allowed',
            500 => 'Internal Server Error',
            );
        return ($statusMessage[$statusCode]) ? $statusMessage[$statusCode] : $statusMessage[500];
    }

    protected function prepareResponse($data){
        //status 
        //next: true|false
        //get("next"=> true|false, "products"=> $data['products'])
        //add("status" => true|false)
        //delete("status" => true|false)
        /*$methodToResp = array("GET" => array("next" => $data['next'], ), 
                              "POST" => $data, 
                              "PUT" => $data,
                              "DELETE" => $data
                              );*/
        return json_encode($data);
    }

   
    public function response($statusCode, $data){
        header("Access-Control-Allow-Orgin: *");
        header("Access-Control-Allow-Methods: *");
        header("Content-Type: application/json");
        header("HTTP/1.1 " . $statusCode . " " . $this->getStatusMessage($statusCode));

        return $data;
    }
}
