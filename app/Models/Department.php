<?php

require_once __DIR__ . '/Model.php';

class Department extends Model{
    protected $table = 'departments';

    public function all(){
        $stmt = $this->db->query("SELECT * FROM {$this->table}");
        return $stmt->fetchAll();
    }

}


?>