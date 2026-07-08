<?php

require_once __DIR__ . '/../Models/Location.php';

class LocationController{
    private $locationModel;

    public function __construct()
    {
        $this->locationModel = new Location();
    }

    public function index(){
        $locations = $this->locationModel->all();
        require_once __DIR__ . '/../views/ItAssets.php';
    }


}

?>