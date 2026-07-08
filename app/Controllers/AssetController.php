<?php

require_once __DIR__ . '/../Models/Asset.php';

class AssetController{
    private $assetModel;

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


}

?>