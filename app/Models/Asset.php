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

    

}


?>