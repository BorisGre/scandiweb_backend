<?php
//use ;

class DVDProduct extends MainProduct
{
    public int $size = "[1-9]{1}[0-9]{0,49}";
    
    public function __construct($itemsPerPage = 0, $DB) {
        parent::__construct($itemsPerPage, $DB);

        $validatingFieldsArray = array();   

        foreach ($this as $key => $value) {

            $validatingFieldsArray[$key] = $value;
        }

        $this->validatingFieldsArray = $validatingFieldsArray;
        array_push($validatingFieldsArray, parent->productMainFieldsArray());
    }

    public function add($newProduct = []){

        return parent->addProduct($newProduct, $this->validatingFieldsArray);
    }

    public function get($pageNumber = 0){
       
        return parent->getProduct($pageNumber);
    }

    public function update($updatingProduct = []){

        return parent->updateProduct($updatingProduct, $this->validatingFieldsArray);
    }

    public function delete($skuArray = []){
      
        return parent->deleteProduct($skuArray);
    }  
}