<?php
//use \Jacwright\RestServer\RestException;

abstract class AbstractProduct
{
    public string $sku;
    public string $name;
    public string $price;
    public string $type;

    abstract public function addProduct($newProduct, $patternArray);

    abstract public function getProduct($pageNumber);

    abstract public function updateProduct($updatingProduct, $patternArray);

    abstract public function deleteProduct($skuArray);

    abstract public function validate($objArray, $patternArray);
}