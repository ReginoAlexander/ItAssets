
<div class="card-header">
    <h5 class="card-title">
        Inventario de <?= $assets[0]['location_name'] ?>
    </h5>
    <div class="card-actions">
        <a href="#" class="btn btn-primary btnNew">Nuevo</a>
    </div>
</div>
<div class="card-body">

    <?php foreach($assets as $i => $asset): ?>
        <?php if($i%3 === 0): ?>
            <div class="row">
        <?php endif; ?>


        <div class="col-md-4 mb-md-0">
            <div class="card text-center h80">
                <div class="card-body flex-column">
                    <div class="mb-2">
                        <h5><?= $asset['hostname'] ?></h5>
                        <span class="display-4"><img src="/ItAssets/public/assets/img/<?= $asset['type'] ?>.png" style="max-width: 50px;" alt=""></span>
                    </div>
                    <h6><?= $asset['name'] ?></h6>
                    <ul class="list-unstyled">
                        <li class="mb-2"><?= $asset['location_name'] ?></li>
                    </ul>
                    <div class="mt-auto">
                        <a href="#" class="btn btn-lg btn-outline-primary btn-details" data-id="<?= $asset['id'] ?>" >Ver detalle</a>
                    </div>
                </div>
            </div>
        </div>
        <?php if($i% 3 ==2 || $i == count($assets) -1 ): ?>
            </div>
        <?php endif; ?>
        <?php endforeach; ?>

    </div>
</div>