<?php

require_once __DIR__ . '/Model.php';

class Location extends Model{
    protected $table = 'locations';

    public function all(){
        $stmt = $this->db->query("SELECT * FROM {$this->table}");
        return $stmt->fetchAll();
    }

}


?>