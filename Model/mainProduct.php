<?php
//use ;

class MainProduct extends AbstractProduct
{
    public string $sku = "[a-zA-Z_0-9]{0,50}";
    public string $name = "[a-zA-Z 0-9,.]{0,50}";
    public string $price = "[1-9]{0,1}[0-9]{0,45}[,]{0,1}[.]{0,1}[0-9]{0,2}";
    public string $type = "[a-zA-Z]{1,20}";
    private string $pageNumberPattern = "[0-9]{1,5}";

    private $DB;

    public function __construct($config) {

       $this->itemsPerPage = $config->itemsPerPage;  
       $this->DB = new DB($config->DB, $this->itemsPerPage);
    }

    public function addProduct($argsArray){

        $newProduct = $argsArray['newProduct'];
        $checkingFiledsArray = $argsArray['validation'];

        echo "QQQQ add product\n";
        var_dump($checkingFiledsArray);
        echo "QQQ add end product\n";

        $status = false;
        $numOfProductAfter = 0;

        try {
                $validateStatus = $this->validate($newProduct, $checkingFiledsArray);

                if($validateStatus === true){
                   
                   $numOfProductBefore = ($this->DB->product)()->count('');
                   $addProductStatus = ($this->DB->product)()->add($newProduct);
                   $numOfProductAfter = ($this->DB->product)()->count('');
                   
                   $status = ($addProductStatus AND $numOfProductBefore < $numOfProductAfter) ? true : false; 
                }

           } catch(Exception $e) {

               return ["status" => $status, "error" => $e];
        }
       return ["status" => $status, "product" => $newProduct, "total" => $numOfProductAfter];
    }

    public function getProduct($pageNumber = 0){
        
         $status = false;
         $next = false;

         echo "getproduct\n";
            var_dump($pageNumber);
         echo "end get product\n";

         try {
                 $validateStatus = $this->validate(["pageNumber" => $pageNumber], 
                                                   ["pageNumber" => $this->pageNumberPattern]
                                                   );

                 if($validateStatus === true){
                    
                    $products = ($this->DB->product)()->get($pageNumber);
                    $allProductsCount = ($this->DB->product)()->count('');
                    $totalPages = $allProductsCount/$this->itemsPerPage;

                    $pagesSkip = ($pageNumber+1)*$this->itemsPerPage;  
                                       
                    $next =  ($totalPages >= $pagesSkip) ? true : false; 
   
                    $status = true;
                 }

            } catch(Exception $e) {

                return ["status" => $status, "error" => $e];
         }
        return ["next" => $next, "products" => $products];
    }

    public function updateProduct($argsArray){

        echo "QQQQQ update product\n";
        //var_dump($updatingProduct);

        $updatingProduct = $argsArray['updatingProduct'];
        $checkingFiledsArray = $argsArray['validation'];

        $status = false;
        
        try {
                $validateStatus = $this->validate($updatingProduct, $checkingFiledsArray);

                if($validateStatus === true){
                   
                   $updateProductStatus = ($this->DB->product)()->update($updatingProduct); 
                   $status = $updateProductStatus ? true : false;
                }

           } catch(Exception $e) {

               return ["status" => $status, "error" => $e];
        }
       return ["status" => $status, "product" => $updatingProduct];
    }

    public function deleteProduct($skuArray){

        echo "QQQQQ delete product\n";   
        $skuArray = is_array($skuArray) ? $skuArray : [$skuArray];

        var_dump($skuArray);

        $status = false;
        $numOfProductAfter = 0;

        try {

            foreach($skuArray as $sku){

              $validateStatus = $this->validate(["sku" => $sku], ["sku" => $this->sku]);   
             
                if($validateStatus === true){

                    echo "Validate status true\n";
                    $numOfProductBefore = ($this->DB->product)()->count('');
                    $deleteProductStatus = ($this->DB->product)()->delete(["sku" => $sku]);
                    $numOfProductAfter = ($this->DB->product)()->count('');
                    

                    var_dump($deleteProductStatus, $numOfProductBefore, $numOfProductAfter);
                    
                    $status = ($deleteProductStatus AND $numOfProductBefore < $numOfProductAfter) ? true : false; 
                }
            } 

           } catch(Exception $e) {

             return ["status" => $status, "error" => $e];
        }

        return ["status" => $status, "total" => $numOfProductAfter];
    }

    public function validate($obj, $checkingFiledsArray){
 
        $validationStatus = false;
        echo "Validate \n";
        var_dump($obj, $checkingFiledsArray);
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