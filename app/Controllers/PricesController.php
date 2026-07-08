<?php

require_once __DIR__ . '/../Models/Prices.php';

class PricesController{
    private $pricesModel;

    public function __construct()
    {
        $this->pricesModel = new Prices();
    }

    public function index(){
        $prices = $this->pricesModel->all();
    }


}

?>