<?php namespace Ran\Client\Contracts;

use GuzzleHttp\Client;

Interface RanServiceContract {
    public function createUser($email);
    public function getProduct($productID);
    public function createCart($customerID, $storeID);
    public function getCartItems($cartID);
    public function destroyCart($cartID);
    public function addItems($cartID, $products);
    public function updateItem($cartID, $itemID, $productID);
    public function createCartAndItem($customerID, $products);
    public function checkout($cartID, $data);
    public function payment($cartID, $data);
}


?>
