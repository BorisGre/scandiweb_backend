<?php
//use ;
//require_once "DB.php"
require_once "AbstractProduct.php";

class Product extends AbstractProduct
{
    public string $sku;
    public string $name;
    public decimal $price;
    public string $type;
    private int $itemsPerPage;

    private $DB;

    public function __construct($itemsPerPage = 0, $product = [], $DB) {
       $this->itemsPerPage = $itemsPerPage;  
    }

    public function addProduct($newProduct){

       $status = false;

       try {
            //DB->product->add($newProduct);
          $status = true;

       } catch(Exeception $e) {
         
       }
       return array("status" => $status);
    }

    public function getProduct($pageNumber = 0){
        
         $status = false;

         try {
                 //$products = DB->product->get($pageNumber, $this->itemsPregPage);
                 //$allProducts = DB->count()
                 //$totalPages = floor($allProducts/$this->itemsPerPage);
                 //$next = $totalPages >= $pageNumber ?? false : true; 
            $status = true;

            } catch(Exception $e) {
           
         }
        return array("next" => $next, "products" => $products);
    }

    public function updateProduct(){

        return array("status" => true);
    }

    public function deleteProduct($skuArray){

        $status = false;

        try {
             //DB->product->delete(skuArray);
           $status = true;

           } catch(Exception $e) {
          
        }

        return array("status" => true);
    }

    public function validate($newProduct){}
}