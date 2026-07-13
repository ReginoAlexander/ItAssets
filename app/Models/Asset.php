<?php

require_once __DIR__ . '/Model.php';

class Asset extends Model{
    protected $table = 'assets';

    public function all(){
        $sql = "SELECT 
                    assets.id,
                    u.name,
                    assets.hostname,
                    brand.brand_name,
                    assets.model_name,
                    type.type,
                    loc.location_name,
                    department.department,
                    assets.purchase_date,
                    status.status_name

                FROM {$this->table} as assets
                LEFT JOIN locations as loc on loc.id = assets.location
                LEFT JOIN users as u on u.id = assets.assigned_to
                LEFT JOIN brands AS brand on brand.id = assets.brand
                LEFT JOIN assets_types AS type on type.id = assets.asset_type
                LEFT JOIN departments AS department on department.id = assets.department
                LEFT JOIN status on status.id = assets.status";

        return $this->db->query($sql)->fetchAll();
    }

    public function byLocationId($id){
        $sql = 
        "SELECT 
            assets.id,
            u.name,
            assets.hostname,
            brand.brand_name,
            assets.model_name,
            type.type,
            loc.location_name,
            department.department,
            assets.purchase_date,
            status.status_name

        FROM {$this->table} as assets
        LEFT JOIN locations as loc on loc.id = assets.location
        LEFT JOIN users as u on u.id = assets.assigned_to
        LEFT JOIN brands AS brand on brand.id = assets.brand
        LEFT JOIN assets_types AS type on type.id = assets.asset_type
        LEFT JOIN departments AS department on department.id = assets.department
        LEFT JOIN status on status.id = assets.status

        WHERE loc.id = ?";
        
        return $this->query($sql, [$id])->fetchAll();

    }

    public function byId($id){
        $sql = 
        "SELECT 
            assets.id,
            u.name,
            assets.hostname,
            brand.brand_name,
            assets.model_name,
            type.type,
            loc.location_name,
            department.department,
            assets.purchase_date,
            status.status_name,
            specs.ram_gb,
            specs.disk_type,
            specs.storage_gb,
            specs.cpu,
            specs.os

        FROM {$this->table} as assets
        LEFT JOIN locations as loc on loc.id = assets.location
        LEFT JOIN users as u on u.id = assets.assigned_to
        LEFT JOIN brands AS brand on brand.id = assets.brand
        LEFT JOIN assets_types AS type on type.id = assets.asset_type
        LEFT JOIN departments AS department on department.id = assets.department
        LEFT JOIN status on status.id = assets.status
        LEFT JOIN pc_specs as specs on specs.asset_id = assets.id

        WHERE assets.id = ?";

        $data = $this->query($sql, [$id]);
        return $data;
    }

// Models/Asset.php (agrega este método a la clase existente)

// Models/Asset.php

    public function create($assetData, $specsData){
        try {
            $this->db->beginTransaction();

            // 1. Insertar en assets
            $sql = "INSERT INTO assets 
                    (brand, model_name, asset_type, hostname, assigned_to, location, department, purchase_date, status)
                    VALUES 
                    (:brand, :model_name, :asset_type, :hostname, :assigned_to, :location, :department, :purchase_date, :status)";

            $stmt = $this->db->prepare($sql);
            $stmt->execute([
                ':brand'         => $assetData['brand_id'],
                ':model_name'    => $assetData['model_name'],
                ':asset_type'    => $assetData['asset_type'],
                ':hostname'      => $assetData['hostname'],
                ':assigned_to'   => $assetData['assigned_to'],
                ':location'      => $assetData['location_id'],
                ':department'    => $assetData['department_id'],
                ':purchase_date' => $assetData['purchase_date'],
                ':status'        => $assetData['status'],
            ]);

            $assetId = $this->db->lastInsertId();

            // 2. Insertar en pc_specs, usando el id recién creado
            $sqlSpecs = "INSERT INTO pc_specs 
                        (asset_id, ram_gb, disk_type, storage_gb, cpu, cpu_gen, os)
                        VALUES 
                        (:asset_id, :ram_gb, :disk_type, :storage_gb, :cpu, :cpu_gen, :os)";

            $stmtSpecs = $this->db->prepare($sqlSpecs);
            $stmtSpecs->execute([
                ':asset_id'   => $assetId,
                ':ram_gb'     => $specsData['ram_gb'],
                ':disk_type'  => $specsData['disk_type'],
                ':storage_gb' => $specsData['storage_gb'],
                ':cpu'        => $specsData['cpu'],
                ':cpu_gen'    => $specsData['cpu_gen'],
                ':os'         => $specsData['os'],
            ]);

            $this->db->commit();
            return $assetId;

        } catch (PDOException $e) {
            $this->db->rollBack();
            error_log('Error creando asset: ' . $e->getMessage());
            return false;
        }
    }
    

}


?>