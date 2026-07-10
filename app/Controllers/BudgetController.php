<?php

require_once __DIR__ . '/../Models/Budget.php';
require_once __DIR__ . '/../Models/Prices.php';

class BudgetController{
    private $budgetModel;

    // Deben coincidir con los objetivos que usa UrgenciasController
    private const RAM_OBJETIVO_GB    = 16;
    private const CPU_GEN_OBJETIVO   = 13;
    private const CPU_OBJETIVO_LABEL = 'Intel Core i5-13xxx'; // ajusta al modelo real que compres

    public function __construct()
    {
        $this->budgetModel = new Budget();
    }

    public function index(){
        $prices = new Prices();

        $precios = $prices->getPreciosUnificados();
        $budgets = $this->budgetModel->getLatest();
        
        require_once __DIR__ . '/../views/budgets.php';
    }

    public function getBudget(){
        $budgetId = $_GET['budget'];
        $budget = $this->budgetModel->byId($budgetId)->fetchAll();

        require_once __DIR__ . '/../views/budgetDetails.php';

    }

    public function calcularMochila()
    {
        $nombre    = trim($_POST['nombre'] ?? '');
        $capacidad = (float) ($_POST['capacidad'] ?? 0);
    
        if ($nombre === '' || $capacidad <= 0) {
            header('Location: /ItAssets/public/budgets');
            exit;
        }
    
        // --- 1) Traer los objetos: assets con beneficio (valor) y costo_mejora (peso) ---
        $items = $this->budgetModel->query(
            "SELECT ps.asset_id, ps.beneficio_total AS valor, ps.costo_mejora AS peso
            FROM pc_specs ps
            WHERE ps.beneficio_total IS NOT NULL
            AND ps.costo_mejora IS NOT NULL
            AND ps.costo_mejora > 0"
        )->fetchAll();
    
        // --- 2) Correr búsqueda tabú ---
        $seleccion = $this->tabuKnapsack($items, $capacidad);
    
        // --- 3) Guardar el presupuesto y los assets seleccionados ---
        $this->budgetModel->query(
            "INSERT INTO presupuestos (nombre, total, fecha, status) VALUES (?, ?, CURDATE(), 1)",
            [$nombre, $capacidad]
        );
        $idPresupuesto = $this->budgetModel->lastId();
    
        foreach ($seleccion['items'] as $item) {
            $this->budgetModel->query(
                "INSERT INTO mejoras_seleccionadas (id_presupuesto, id_asset, beneficio_total, costo_mejora)
                VALUES (?, ?, ?, ?)",
                [$idPresupuesto, $item['asset_id'], $item['valor'], $item['peso']]
            );

            // --- 4) Aplicar la mejora físicamente en pc_specs ---
            $this->aplicarMejora((int) $item['asset_id']);
        }
    
        header('Location: /ItAssets/public/budgets');
        exit;
    }

    /**
     * Actualiza en pc_specs solo los campos que necesitaban mejora para este asset
     * (mismo criterio que usa UrgenciasController para calcular el costo).
     * No toca lo que ya estaba al nivel objetivo.
     */
    private function aplicarMejora(int $assetId): void
    {
        $specs = $this->budgetModel->query(
            "SELECT ram_gb, disk_type, cpu_gen FROM pc_specs WHERE asset_id = ?",
            [$assetId]
        )->fetch();

        if (!$specs) {
            return; // asset sin fila en pc_specs (ej. equipo nuevo), no hay nada que actualizar aquí
        }

        $sets   = [];
        $params = [];

        if ($specs['ram_gb'] < self::RAM_OBJETIVO_GB) {
            $sets[]   = 'ram_gb = ?';
            $params[] = self::RAM_OBJETIVO_GB;
        }

        if ($specs['disk_type'] === 'HDD') {
            $sets[]   = 'disk_type = ?';
            $params[] = 'SSD';
        }

        if ($specs['cpu_gen'] < self::CPU_GEN_OBJETIVO) {
            $sets[]   = 'cpu_gen = ?';
            $params[] = self::CPU_GEN_OBJETIVO;
            $sets[]   = 'cpu = ?';
            $params[] = self::CPU_OBJETIVO_LABEL;
        }

        if (empty($sets)) {
            return; // nada que actualizar
        }

        // Ya se invirtió en este equipo: su costo de mejora pendiente vuelve a 0.
        // El beneficio_total se recalcula correctamente la próxima vez que
        // le des clic a "Calcular beneficios" (los valores de urgencia bajan
        // automáticamente porque las specs ya son mejores).
        $sets[]   = 'costo_mejora = 0';
        $sets[]   = 'updated_at = CURDATE()';
        $params[] = $assetId;

        $sql = 'UPDATE pc_specs SET ' . implode(', ', $sets) . ' WHERE asset_id = ?';
        $this->budgetModel->query($sql, $params);
    }
    
    /**
     * Búsqueda tabú para el problema de la mochila 0/1.
     * Representación: vector binario, 1 = objeto incluido.
     * Vecindario: 1-flip (se invierte un solo objeto por vecino).
     *
     * $items: array de ['asset_id' => ..., 'valor' => ..., 'peso' => ...]
     * $capacidad: capacidad máxima (presupuesto)
     */
    private function tabuKnapsack(array $items, float $capacidad): array
    {
        $n = count($items);
        if ($n === 0) {
            return ['items' => [], 'valor_total' => 0, 'peso_total' => 0];
        }
    
        // --- Solución inicial: greedy por valor/peso (constructiva simple) ---
        $orden = $items;
        usort($orden, function ($a, $b) {
            $ra = $a['peso'] > 0 ? $a['valor'] / $a['peso'] : $a['valor'];
            $rb = $b['peso'] > 0 ? $b['valor'] / $b['peso'] : $b['valor'];
            return $rb <=> $ra; // descendente
        });
    
        $indicePorAsset = [];
        foreach ($items as $i => $it) {
            $indicePorAsset[$it['asset_id']] = $i;
        }
    
        $x = array_fill(0, $n, 0); // vector binario de la solución
        $pesoActual = 0.0;
        foreach ($orden as $it) {
            $i = $indicePorAsset[$it['asset_id']];
            if ($pesoActual + $it['peso'] <= $capacidad) {
                $x[$i] = 1;
                $pesoActual += $it['peso'];
            }
        }
    
        // --- Parámetros de la búsqueda tabú ---
        $MAX_ITERACIONES     = 200;
        $TENENCIA_TABU       = 7;   // iteraciones que un movimiento queda prohibido
        $SIN_MEJORA_LIMITE   = 40;  // criterio de paro por estancamiento
    
        $evaluar = function (array $sol) use ($items, $capacidad) {
            $valor = 0.0;
            $peso  = 0.0;
            foreach ($sol as $i => $bit) {
                if ($bit === 1) {
                    $valor += $items[$i]['valor'];
                    $peso  += $items[$i]['peso'];
                }
            }
            return ['valor' => $valor, 'peso' => $peso, 'factible' => $peso <= $capacidad];
        };
    
        $actual       = $x;
        $evalActual   = $evaluar($actual);
        $mejor        = $actual;
        $evalMejor    = $evalActual;
    
        $listaTabu   = array_fill(0, $n, 0); // 0 = libre; >0 = iteraciones restantes prohibido
        $sinMejora   = 0;
    
        for ($iter = 0; $iter < $MAX_ITERACIONES && $sinMejora < $SIN_MEJORA_LIMITE; $iter++) {
    
            $mejorVecino     = null;
            $mejorVecinoEval = null;
            $mejorVecinoIdx  = null;
    
            // --- Generar y evaluar todos los vecinos (1-flip) ---
            for ($i = 0; $i < $n; $i++) {
                $vecino = $actual;
                $vecino[$i] = 1 - $vecino[$i]; // alterna: si estaba fuera entra, si estaba dentro sale
    
                $evalVecino = $evaluar($vecino);
    
                // Si el flip mete un objeto y se pasa del presupuesto, se descarta (no factible)
                if (!$evalVecino['factible']) {
                    continue;
                }
    
                $esTabu = $listaTabu[$i] > 0;
    
                // Criterio de aspiración: aceptar aunque sea tabú si mejora al mejor global
                $aspiracion = $evalVecino['valor'] > $evalMejor['valor'];
    
                if ($esTabu && !$aspiracion) {
                    continue; // prohibido y no hay aspiración: se descarta
                }
    
                if ($mejorVecinoEval === null || $evalVecino['valor'] > $mejorVecinoEval['valor']) {
                    $mejorVecino     = $vecino;
                    $mejorVecinoEval = $evalVecino;
                    $mejorVecinoIdx  = $i;
                }
            }
    
            // Si no hubo ningún vecino factible/aceptable, se detiene
            if ($mejorVecino === null) {
                break;
            }
    
            // --- Moverse al mejor vecino encontrado ---
            $actual     = $mejorVecino;
            $evalActual = $mejorVecinoEval;
    
            // Actualizar lista tabú: prohibir revertir este movimiento por un tiempo
            $listaTabu[$mejorVecinoIdx] = $TENENCIA_TABU;
            foreach ($listaTabu as $i => $val) {
                if ($val > 0) {
                    $listaTabu[$i]--;
                }
            }
    
            // --- Actualizar mejor solución global ---
            if ($evalActual['valor'] > $evalMejor['valor']) {
                $mejor      = $actual;
                $evalMejor  = $evalActual;
                $sinMejora  = 0;
            } else {
                $sinMejora++;
            }
        }
    
        // --- Armar resultado final con los items seleccionados ---
        $seleccionados = [];
        foreach ($mejor as $i => $bit) {
            if ($bit === 1) {
                $seleccionados[] = $items[$i];
            }
        }
    
        return [
            'items'       => $seleccionados,
            'valor_total' => $evalMejor['valor'],
            'peso_total'  => $evalMejor['peso'],
        ];
    }


}

?>