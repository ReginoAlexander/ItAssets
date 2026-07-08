<?php

require __DIR__ . '/../../app/config/bootstrap.php';


?>

<div class="page-wrapper">
    <div class="page-header d-print-none">
        <div class="container-xl">
            <div class='row g-2 align-items-center'>
                    <div class="page-pretitle">Presupuestos de TI</div>
                    <h1 class="page-title">Presupuestos y precios</h1>
            </div>
        </div>
    </div>

    <div class="page-body" id='content'>
        <div class="container-xl">

            <div class="row mb-4">
                <div class="col-md-12 col-lg-9">
                    <div class="card" id="">
                        <div class="card-header">
                            <h3 class="card-title" >Nuevo Presupuesto</h3>
                            <div class="card-actions">
                            </div>
                        </div>        
                        <div class="card-body">
                            <form action="/ItAssets/public/budgets" method="post">
                                <div class="row mx-6">
                                    <div class="col-md-12 col-lg-5 gx-8">
                                        <label for="" class="form-label">Nombre</label>
                                        <input type="text" class="form-control" name="nombre" required placeholder="Nombre del presupuesto">
                                    </div>
                                    <div class="col-md-12 col-lg-5 gx-8">
                                        <label for="" class="form-label">Presupuesto</label>
                                        <div class="input-group">
                                            <span class="input-group-text"> $ </span>
                                            <input type="number" class="form-control" step="0.01" name="capacidad" placeholder="Presupuesto" required autocomplete="off">
                                        </div>
                                    </div>
                                    <div class="col-md-12 col-lg-2 gx-8">
                                        <button type="submit" class="btn btn-primary mt-4 mb-3">Calcular</button>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>  
                
                <div class="col-md-12 col-lg-3">
                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title">Historial de presupuestos</h3>
                        </div>
                        <div class="card-body">
                            <form action="/ItAssets/public/budgets/details" method="GET" class="row d-flex">
                                <select name="budget" id="budget" class="form-select">
                                    <option value="">Seleccionar</option>
                                    <?php foreach($budgets as $budget): ?>
                                        <option value="<?= $budget['id'] ?>"><?= $budget['nombre'] ?></option>
                                    <?php endforeach; ?>
                                </select>
                                <button type="submit" class="btn btn-primary btn-sm mh-20 mt-2">Ver</button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>


            <div class="row">
                <div class="col-md-12 col-lg-7">
                    <div class="card" id="eval">
                        <div class="card-header">
                            <h3 class="card-title" >Evaluacion de equipos</h3>
                            <div class="card-actions">
                                <form method="post" action="/ItAssets/public/urgencias/calcular">
                                    <input type="hidden" name="regreso" value="<?= htmlspecialchars($_SERVER['REQUEST_URI']) ?>">
                                    <button type="submit" name="calcular" value="1" class="btn btn-primary">Calcular costos</button>
                                </form>
                            </div>
                        </div>        
                        <div class="table-responsive">
                            <table class="table table-selectable card-table table-vcenter text-nowrap datatable" id="evaluaciones">
                                <thead>
                                    <tr>
                                        <th>Equipo</th>
                                        <th>Beneficio</th>
                                        <th>Costo</th>
                                        <th>Fecha de evaluacion</th>
                                    </tr>
                                </thead>
                                <tbody>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>



                <div class="col-md-12 col-lg-5">
                    <div class="card" id="prices">
                        <div class="card-header">
                            <h3 class="card-title" >Precios de las mejoras</h3>
                        </div>
                        <div class="card-table table-responsive">
                            <table class="table table-vcenter">
                                <thead>
                                    <tr>
                                        <th>Mejora</th>
                                        <th>Descripcion</th>
                                        <th>Precio</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach($precios as $precio): ?> 
                                    <tr>
                                        <td><?= $precio['categoria'] ?></td>
                                        <td><?= $precio['detalle'] ?></td>
                                        <td>$<?= $precio['costo'] ?></td>
                                    </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

        </div>
    </div>

</div>

<?php require VIEW . 'layout/footer.php'; ?>
<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>   
<link rel="stylesheet" href="https://cdn.datatables.net/2.3.8/css/dataTables.dataTables.css">
<script src="https://cdn.datatables.net/2.3.8/js/dataTables.js"></script>

<script>

$('#evaluaciones').DataTable( {
    ajax: {
        url: '/ItAssets/public/urgencias/all',
        dataSrc: 'data'
    },
    columns: [
        { data: 'hostname' },
        { data: 'puntaje' },
        { data: 'costo' }, // no tienes "Costo" en el JSON todavía
        { data: 'fecha_eval' }
    ]
} );

</script>