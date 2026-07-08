<?php

require_once __DIR__ . '/Model.php';

class Prices extends Model{
    protected $table = 'precios_disco';

    public function all(){
        $stmt = $this->db->query("SELECT * FROM {$this->table}");
        return $stmt->fetchAll();
    }

    public function getPreciosUnificados(): array
    {
        $filas = [];

        foreach ($this->query("SELECT ram_gb, costo FROM precios_ram")->fetchAll() as $r) {
            $filas[] = [
                'categoria' => 'RAM',
                'detalle'   => $r['ram_gb'] . ' GB',
                'costo'     => (float) $r['costo'],
            ];
        }

        foreach ($this->query("SELECT tipo, tamano_gb, costo FROM precios_disco")->fetchAll() as $r) {
            $filas[] = [
                'categoria' => 'Disco',
                'detalle'   => $r['tipo'] . ' ' . $r['tamano_gb'] . ' GB',
                'costo'     => (float) $r['costo'],
            ];
        }

        foreach ($this->query("SELECT concepto, costo FROM precios_generales")->fetchAll() as $r) {
            $filas[] = [
                'categoria' => 'General',
                'detalle'   => $r['concepto'],
                'costo'     => (float) $r['costo'],
            ];
        }

        return $filas;
    }

}


?>