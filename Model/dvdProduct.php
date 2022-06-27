<?php
//use ;

class DvdProduct extends MainProduct
{
    public string $size = "[1-9]{1}[0-9]{0,49}";
    
    public function __construct($config) {
        parent::__construct($config);

        $validatingFieldsArray = [];   

        foreach ($this as $key => $value) {

            $validatingFieldsArray[$key] = $value;
        }

        $this->validatingFieldsArray = array_merge(parent::productMainFieldsArray(), $validatingFieldsArray);

        var_dump($this->validatingFieldsArray);
        echo "end of construct dvd\n"; 
    }

    public function addProduct($newProduct){
        
        $dto['newProduct'] = $newProduct;
        $dto['validation'] = $this->validatingFieldsArray;

        return parent::addProduct($dto);
    }

    public function updateProduct($updatingProduct){

        $dto['updatingProduct'] = $updatingProduct;
        $dto['validation'] = $this->validatingFieldsArray;
        
        return parent::updateProduct($dto);
    }
}