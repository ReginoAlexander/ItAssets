<?php

require_once __DIR__ . '/../Models/Location.php';
require_once __DIR__ . '/../Models/Brand.php';
require_once __DIR__ . '/../Models/Department.php';

class LocationController{
    private $locationModel;
    private $brandModel;
    private $departmentModel;

    public function __construct()
    {
        $this->locationModel = new Location();
        $this->brandModel = new Brand();
        $this->departmentModel = new Department();
    }

    public function index(){
        $locations = $this->locationModel->all();
        $brands = $this->brandModel->all();
        $departments = $this->departmentModel->all();
        require_once __DIR__ . '/../views/ItAssets.php';
    }


}

?>