<?php
//use \Jacwright\RestServer\RestException;

abstract class AbstractProduct
{
    public string $sku;
    public string $name;
    public decimal $price;
    public string $type;

    public int $size;

    public int $weight;

    public int $width;
    public int $height;
    public int $length;

    abstract public function addProduct($newProduct);

    abstract public function getProduct($pageNumber);

    abstract public function updateProduct();

    abstract public function deleteProduct($skuArray);

    abstract public function validate($newProduct);
}