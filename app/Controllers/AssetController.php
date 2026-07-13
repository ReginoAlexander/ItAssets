<?php

require_once __DIR__ . '/../Models/Asset.php';
require_once __DIR__ . '/../Models/Brand.php';
require_once __DIR__ . '/../Models/Location.php';

$brandModel = new Brand();


$brands = $brandModel->all();


class AssetController{
    private $assetModel;
    private $brandModel;

    public function __construct()
    {
        $this->assetModel = new Asset();
    }


    public function filter(){
        $locationId = $_GET['id'] ?? 0;
        

        $assets = ($locationId == 0)
            ? $this->assetModel->all()
            : $this->assetModel->byLocationId($locationId);

        require_once __DIR__ . '/../views/components/assets_card.php';
    }

    public function details(){

        header('Content-Type: application/json');
        $assetId = $_GET['id'] ?? null;

        if(!$assetId) {
            http_response_code(400);
            echo json_encode(['error' => 'Id no proporcionado']);
            return;
        }

        $detail = $this->assetModel->byId($assetId)->fetch(PDO::FETCH_ASSOC);
        echo json_encode($detail);
    }

    public function store(){
        $requiredAsset = ['brand_id', 'model_name', 'asset_type', 'hostname', 'assigned_to', 'location_id', 'department_id'];
        $requiredSpecs = ['ram_gb', 'disk_type', 'storage_gb', 'cpu', 'cpu_gen', 'os'];

        foreach (array_merge($requiredAsset, $requiredSpecs) as $field) {
            if (empty($_POST[$field])) {
                http_response_code(400);
                echo "Falta el campo: $field";
                return;
            }
        }

        $assetData = [
            'brand_id'      => (int) $_POST['brand_id'],
            'model_name'    => trim($_POST['model_name']),
            'asset_type'    => (int) $_POST['asset_type'],
            'hostname'      => trim($_POST['hostname']),
            'assigned_to'   => (int) $_POST['assigned_to'],
            'location_id'   => (int) $_POST['location_id'],
            'department_id' => (int) $_POST['department_id'],
            'purchase_date' => date('Y-m-d'),
            'status'        => 1,
        ];

        $specsData = [
            'ram_gb'     => (int) $_POST['ram_gb'],
            'disk_type'  => $_POST['disk_type'],
            'storage_gb' => (int) $_POST['storage_gb'],
            'cpu'        => trim($_POST['cpu']),
            'cpu_gen'    => (int) $_POST['cpu_gen'],
            'os'         => trim($_POST['os']),
        ];

        $assetId = $this->assetModel->create($assetData, $specsData);

        if ($assetId) {
            header('Location: /ItAssets/public/locations');
            exit;
        } else {
            http_response_code(500);
            echo "Error al guardar el activo";
        }
    }


}

?>