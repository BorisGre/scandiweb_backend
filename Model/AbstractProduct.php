<?php
//use \Jacwright\RestServer\RestException;

abstract class AbstractProduct
{
    public string $sku;
    public string $name;
    public string $price;
    public string $type;

    abstract public function addProduct($newProduct);

    abstract public function getProduct($pageNumber);

    abstract public function updateProduct($updatingProduct);

    abstract public function deleteProduct($skuArray);

    abstract public function validate($objArray, $patternArray);
}