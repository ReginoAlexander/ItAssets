<?php

require_once __DIR__ . '/Model.php';

class Budget extends Model{
    protected $table = 'presupuestos';

    public function all(){
        $stmt = $this->db->query("SELECT * FROM {$this->table}");
        return $stmt->fetchAll();
    }

    public function getLatest(){
        $stmt = $this->db->query(
            "SELECT * FROM {$this->table}
            ORDER BY id DESC
            LIMIT 3");
        return $stmt->fetchAll();
    }

    public function byId($id){
        $sql = "SELECT
                    p.id,
                    p.nombre,
                    p.total,
                    p.fecha,
                    p.status,
                    s.beneficio_total,
                    s.costo_mejora,
                    asset.hostname
                FROM presupuestos as p
                JOIN mejoras_seleccionadas s on s.id_presupuesto = p.id
                JOIN assets asset on asset.id = s.id_asset
                WHERE p.id = ?";
        $data = $this->query($sql, [$id]);
        return $data;
    }

}


?>