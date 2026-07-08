<?php

require_once __DIR__ . '/../Models/Urgencias.php';

class UrgenciasController{
    private $urgenciasModel;

    public function __construct()
    {
        $this->urgenciasModel = new Urgencias();
    }

    public function index(){
        header('Content-Type: application/json');

        $eval = $this->urgenciasModel->all()->fetchAll(PDO::FETCH_ASSOC);
        echo json_encode(['data' => $eval]);
    }

    public function calcular()
    {
        $this->calcularYGuardarBeneficios();

        $regreso = $_POST['regreso'] ?? ($_SERVER['HTTP_REFERER'] ?? '/');
        header('Location: ' . $regreso);
        exit;
    }

    private const CAMPO_MAP = [
        'ram'            => 'ram_gb',
        'Tipo de disco'  => 'disk_type',
        'Generacion CPU' => 'cpu_gen',
    ];
    private const NOMBRE_CRITICIDAD_DEPTO = 'Criticidad departamento';
    private const RAM_OBJETIVO_GB  = 16;
    private const CPU_GEN_OBJETIVO = 13;

    private function calcularYGuardarBeneficios(): void
    {
        $db = $this->urgenciasModel;

        $parametros = [];
        foreach ($db->query('SELECT id, nombre, peso FROM parametros_urgencia')->fetchAll() as $row) {
            $parametros[$row['id']] = ['nombre' => $row['nombre'], 'peso' => (float) $row['peso']];
        }
        $idPorNombre = [];
        foreach ($parametros as $id => $p) {
            $idPorNombre[$p['nombre']] = $id;
        }

        $reglasPorParam = [];
        foreach ($db->query(
            'SELECT id_parametro, condicion, valor, puntaje FROM parametros_reglas ORDER BY id_parametro, id'
        )->fetchAll() as $row) {
            $reglasPorParam[$row['id_parametro']][] = $row;
        }

        $costosRam = [];
        foreach ($db->query('SELECT ram_gb, costo FROM precios_ram')->fetchAll() as $row) {
            $costosRam[(int) $row['ram_gb']] = (float) $row['costo'];
        }

        $costosDisco = [];
        foreach ($db->query('SELECT tipo, tamano_gb, costo FROM precios_disco')->fetchAll() as $row) {
            $costosDisco[$row['tipo'] . '_' . $row['tamano_gb']] = (float) $row['costo'];
        }

        $costosGenerales = [];
        foreach ($db->query('SELECT concepto, costo FROM precios_generales')->fetchAll() as $row) {
            $costosGenerales[$row['concepto']] = (float) $row['costo'];
        }

        $equipos = $db->query(
            'SELECT a.id AS asset_id,
                    d.priority AS dept_priority,
                    ps.ram_gb, ps.disk_type, ps.cpu_gen, ps.storage_gb
            FROM assets a
            LEFT JOIN pc_specs ps ON ps.asset_id = a.id
            LEFT JOIN departments d ON d.id = a.department'
        )->fetchAll();

        foreach ($equipos as $eq) {
            $beneficioTotal = 0.0;

            foreach (self::CAMPO_MAP as $nombreParam => $columna) {
                $idParam = $idPorNombre[$nombreParam] ?? null;
                if ($idParam === null) {
                    continue;
                }

                $peso      = $parametros[$idParam]['peso'];
                $valorReal = $eq[$columna] ?? null;
                $puntaje   = $valorReal === null
                    ? 0
                    : $this->obtenerPuntajeRegla($reglasPorParam[$idParam] ?? [], $valorReal);

                $beneficioTotal += $puntaje * $peso;

                $db->query(
                    'INSERT INTO asset_urgencia_scores (id_asset, id_parametro, puntaje, fecha_eval)
                    VALUES (?, ?, ?, NOW())',
                    [$eq['asset_id'], $idParam, $puntaje]
                );
            }

            $idParamDepto = $idPorNombre[self::NOMBRE_CRITICIDAD_DEPTO] ?? null;
            if ($idParamDepto !== null) {
                $peso    = $parametros[$idParamDepto]['peso'];
                $puntaje = $eq['dept_priority'] ?? 0;
                $beneficioTotal += $puntaje * $peso;

                $db->query(
                    'INSERT INTO asset_urgencia_scores (id_asset, id_parametro, puntaje, fecha_eval)
                    VALUES (?, ?, ?, NOW())',
                    [$eq['asset_id'], $idParamDepto, $puntaje]
                );
            }

            $costoMejora = $this->calcularCostoMejora($eq, $costosRam, $costosDisco, $costosGenerales);

            if ($eq['ram_gb'] !== null) {
                $db->query(
                    'UPDATE pc_specs SET costo_mejora = ?, beneficio_total = ? WHERE asset_id = ?',
                    [$costoMejora, $beneficioTotal, $eq['asset_id']]
                );
            }
        }
    }

    private function evaluarCondicion($valorReal, string $condicion, $valorRegla): bool
    {
        if (is_numeric($valorReal) && is_numeric($valorRegla)) {
            $a = (float) $valorReal;
            $b = (float) $valorRegla;
        } else {
            $a = (string) $valorReal;
            $b = (string) $valorRegla;
        }

        return match ($condicion) {
            '<=' => $a <= $b,
            '<'  => $a < $b,
            '>=' => $a >= $b,
            '>'  => $a > $b,
            '='  => $a == $b,
            default => false,
        };
    }

    private function obtenerPuntajeRegla(array $reglasParam, $valorReal): float
    {
        foreach ($reglasParam as $regla) {
            if ($this->evaluarCondicion($valorReal, $regla['condicion'], $regla['valor'])) {
                return (float) $regla['puntaje'];
            }
        }
        return 0;
    }

    private function costoMejoraRam(array $costosRam, int $ramGbDestino): float
    {
        ksort($costosRam);
        foreach ($costosRam as $gb => $costo) {
            if ($gb >= $ramGbDestino) {
                return $costo;
            }
        }
        return $costosRam ? end($costosRam) : 0;
    }

    private function costoMejoraDisco(array $costosDisco, string $tipo, int $gbDestino): float
    {
        $opciones = [];
        foreach ($costosDisco as $clave => $costo) {
            [$t, $gb] = explode('_', $clave);
            if ($t === $tipo) {
                $opciones[(int) $gb] = $costo;
            }
        }
        ksort($opciones);

        foreach ($opciones as $gb => $costo) {
            if ($gb >= $gbDestino) {
                return $costo;
            }
        }
        return $opciones ? end($opciones) : 0;
    }

    private function calcularCostoMejora(array $equipo, array $costosRam, array $costosDisco, array $costosGenerales): float
    {
        if ($equipo['ram_gb'] === null) {
            return $costosGenerales['equipo_nuevo'] ?? 0;
        }

        $costoTotal = 0.0;

        if ($equipo['ram_gb'] < self::RAM_OBJETIVO_GB) {
            $costoTotal += $this->costoMejoraRam($costosRam, self::RAM_OBJETIVO_GB);
        }

        if ($equipo['disk_type'] === 'HDD') {
            $gbActual = $equipo['storage_gb'] ?? 500;
            $costoTotal += $this->costoMejoraDisco($costosDisco, 'SSD', $gbActual);
        }

        if ($equipo['cpu_gen'] < self::CPU_GEN_OBJETIVO) {
            $costoTotal += $costosGenerales['cpu_upgrade'] ?? 0;
        }

        return $costoTotal;
    }


}

?>