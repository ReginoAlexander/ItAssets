<?php

require_once __DIR__ . '/Model.php';

class Urgencias extends Model{
    protected $table = 'asset_urgencia_scores';

    public function all(){
        $sql = 
                "SELECT 
            assets.hostname,
            SUM(scores.puntaje) AS puntaje,
            pc_specs.costo_mejora AS costo,
            MAX(scores.fecha_eval) AS fecha_eval

        FROM {$this->table} AS scores
        INNER JOIN (
            SELECT id_asset, id_parametro, MAX(fecha_eval) AS max_fecha
            FROM asset_urgencia_scores
            GROUP BY id_asset, id_parametro
        ) AS ultimo
            ON ultimo.id_asset = scores.id_asset
        AND ultimo.id_parametro = scores.id_parametro
        AND ultimo.max_fecha = scores.fecha_eval

        JOIN assets ON assets.id = scores.id_asset
        JOIN pc_specs ON pc_specs.asset_id = scores.id_asset

        GROUP BY scores.id_asset, assets.hostname, pc_specs.costo_mejora"
        ;
        $data = $this->db->query($sql);
        return $data;
    }

}


?>