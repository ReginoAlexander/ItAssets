                    
                    
                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title">Ubicacion</h3>
                        </div>
                        <div class="list-group list-group-flush" role="tablist">
                        <a class="list-group-item list-group-item-action location-item" data-id="0" data-bs-toggle="list" href="#" role="tab" aria-selected="false" tabindex="-1">
                            Todo
                        </a>
                        <?php foreach ($locations as $location): ?>
                            <a class="list-group-item list-group-item-action location-item" data-id="<?= $location['id']; ?>" data-bs-toggle="list" href="#" role="tab" aria-selected="false" tabindex="-1">
                                <?= $location['location_name'] ?>
                            </a>
                        <?php endforeach ?>
                        </div>
                    </div>