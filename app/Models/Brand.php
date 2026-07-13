<?php

require_once __DIR__ . '/Model.php';

class Brand extends Model{
    protected $table = 'brands';

    public function all(){
        $stmt = $this->db->query("SELECT * FROM {$this->table}");
        return $stmt->fetchAll();
    }

}


?>