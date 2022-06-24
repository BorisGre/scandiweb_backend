<?php
//use ;

class MainProduct extends AbstractProduct
{
    public string $sku = "[a-zA-Z_0-9]{0,50}";
    public string $name = "[a-zA-Z 0-9,.]{0,50}";
    public string $price = "[1-9]{0,1}[0-9]{0,45}[,]{0,1}[.]{0,1}[0-9]{0,2}";
    public string $type = "[a-zA-Z]{1,20}";
    private string $pageNumberPattern = "[0-9]{1,5}";
    private int $itemsPerPage;

    private $DB;

    public function __construct($config) {

       $this->itemsPerPage = $config->itemsPerPage;  
       $this->DB = new DB($config->DB);
    }

    public function addProduct($newProduct, $checkingFiledsArray){

        $status = false;
        $numOfProductAfter = 0;

        try {
                $validateStatus = $this->validate($newProduct, $checkingFiledsArray);

                if($validateStatus === true){
                   
                   $numOfProductBefore = ($this->DB->product)()->count('');
                   $addStatus = ($this->DB->product)()->add('', $newProduct);
                   $numOfProductAfter = ($this->DB->product)()->count('');
                   
                   $status = ($addStatus AND $numOfProductBefore < $numOfProductAfter) ? true : false; 
                }

           } catch(Exception $e) {

               return ["status" => $status, "error" => $e];
        }
       return ["status" => $status, "product" => $newProduct, "total" => $numOfProductAfter];
    }

    public function getProduct($pageNumber = 0){
        
         $status = false;
         $next = false;

         try {
                 $validateStatus = $this->validate(["pageNumber" => $pageNumber], 
                                                   ["pageNumber" => $this->pageNumberPattern]
                                                   );

                 if($validateStatus === true){
                    
                    $products = ($this->DB->product)()->get('', $pageNumber, $this->itemsPerPage);
                    $allProducts = ($this->DB->product)()->count('');
                    $totalPages = floor($allProducts/$this->itemsPerPage);
   
                    $next = $totalPages >= $pageNumber ? false : true; 
   
                    $status = true;
                 }

            } catch(Exception $e) {

                return ["status" => $status, "error" => $e];
         }
        return ["next" => $next, "products" => $products];
    }

    public function updateProduct($updatingProduct, $checkingFiledsArray){

        $status = false;
        
        try {
                $validateStatus = $this->validate($updatingProduct, $checkingFiledsArray);

                if($validateStatus === true){
                   
                   $updateStatus = ($this->DB->product)()->update('', $updatingProduct); 
                   $status = $updateStatus ? true : false;
                }

           } catch(Exception $e) {

               return ["status" => $status, "error" => $e];
        }
       return ["status" => $status, "product" => $updatingProduct];
    }

    public function deleteProduct($skuArray){

        $status = false;
        $numOfProductAfter = 0;

        try {

            foreach($skuArray as $sku){

              $validateStatus = $this->validate(["sku" => $sku], ["sku" => $this->sku]);   
             
                if($validateStatus === true){

                    $numOfProductBefore = ($this->DB->product)()->count('');
                    $deleteStatus = ($this->DB->product)()->delete(["sku" => $sku]);
                    $numOfProductAfter = ($this->DB->product)()->count('');
                    
                    $status = ($deleteStatus AND $numOfProductBefore < $numOfProductAfter) ? true : false; 
                }
            } 

           } catch(Exception $e) {

             return ["status" => $status, "error" => $e];
        }

        return ["status" => $status, "total" => $numOfProductAfter];
    }

    public function validate($obj, $checkingFiledsArray){
 
        $validationStatus = false;
        
         foreach($obj as $key => $value) {

           preg_match("/".$checkingFiledsArray[$key]."/", $value, $matches);  

           $validationStatus = $matches[0] == $value ? true : false;
           
           if($validationStatus === false){
            throw new Exception("Field ".$key." value is doesn't valid");
           }
         }
        
        return $validationStatus;
    }

    public function productMainFieldsArray(){

        return ["sku" => $this->sku, "name" => $this->name, "price" => $this->price, "type" => $this->type];
    }
}