<?php

require __DIR__ . '/../../app/config/bootstrap.php';


?>

<div class="page-wrapper">
    <div class="page-header d-print-none">
        <div class="container-xl">
            <div class='row g-2 align-items-center'>
                    <div class="page-pretitle">Presupuestos de TI</div>
                    <h1 class="page-title">Presupuesto <?= $budget[0]['nombre'] ?></h1>
            </div>
        </div>
    </div>

    <div class="page-body" id='content'>
        <div class="container-xl">
            <div class="row">
            
                <div class="col-md-12 col-lg-12">
                    <div class="card" id="assets-container">
                        <div class="card-header d-flex justify-content-between align-items-center">
                            <h3 class="card-title">Presupuesto #<?= $budget[0]['id'] ?> — <?= htmlspecialchars($budget[0]['nombre']) ?></h3>
                            <span class="badge bg-<?= $budget[0]['status'] == 1 ? 'success' : 'secondary' ?>">
                                <?= $budget[0]['status'] == 1 ? 'Activo' : 'Inactivo' ?>
                            </span>
                        </div>

                        <div class="card-body">
                            <?php
                                $totalPresupuesto = (float) $budget[0]['total'];
                                $totalGastado     = array_sum(array_column($budget, 'costo_mejora'));
                                $totalBeneficio   = array_sum(array_column($budget, 'beneficio_total'));
                                $restante         = $totalPresupuesto - $totalGastado;
                            ?>

                            <div class="row mb-4">
                                <div class="col-md-3">
                                    <small class="text-muted d-block">Fecha</small>
                                    <strong><?= htmlspecialchars($budget[0]['fecha']) ?></strong>
                                </div>
                                <div class="col-md-3">
                                    <small class="text-muted d-block">Presupuesto</small>
                                    <strong>$<?= number_format($totalPresupuesto, 2) ?></strong>
                                </div>
                                <div class="col-md-3">
                                    <small class="text-muted d-block">Gastado</small>
                                    <strong>$<?= number_format($totalGastado, 2) ?></strong>
                                </div>
                                <div class="col-md-3">
                                    <small class="text-muted d-block">Restante</small>
                                    <strong class="<?= $restante < 0 ? 'text-danger' : 'text-success' ?>">
                                        $<?= number_format($restante, 2) ?>
                                    </strong>
                                </div>
                            </div>

                            <table class="table table-striped">
                                <thead>
                                    <tr>
                                        <th>Equipo</th>
                                        <th class="text-end">Beneficio</th>
                                        <th class="text-end">Costo mejora</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($budget as $fila): ?>
                                    <tr>
                                        <td><?= htmlspecialchars($fila['hostname']) ?></td>
                                        <td class="text-end"><?= number_format($fila['beneficio_total'], 2) ?></td>
                                        <td class="text-end">$<?= number_format($fila['costo_mejora'], 2) ?></td>
                                    </tr>
                                    <?php endforeach; ?>
                                </tbody>
                                <tfoot>
                                    <tr class="fw-bold">
                                        <td>Total</td>
                                        <td class="text-end"><?= number_format($totalBeneficio, 2) ?></td>
                                        <td class="text-end">$<?= number_format($totalGastado, 2) ?></td>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

</div>


<?php require VIEW . 'layout/footer.php'; ?>