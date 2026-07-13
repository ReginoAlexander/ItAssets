
<div class="modal modal-blur fade" id="modalNew" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered" role="document">
        <div class="modal-content">
            <form action="/ItAssets/public/asset/store" method="post">
                <div class="modal-header">
                    <h5 class="modal-title" id="modalNombre">Nuevo equipo</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="space-y">
                        <div>
                            <label class="form-label">Marca</label>
                            <select name="brand_id" id="marca" class="form-select" required>
                                <option value="">Selecciona una marca</option>
                                <?php foreach($brands as $brand): ?>
                                <option value="<?= $brand['id'] ?>"><?= htmlspecialchars($brand['brand_name']) ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div>
                            <label class="form-label">Modelo</label>
                            <input type="text" name="model_name" class="form-control" placeholder="Ingresa el modelo" id="modelo" required>
                        </div>
                        <div>
                            <label class="form-label">Tipo</label>
                            <select name="asset_type" id="tipo" class="form-select" required>
                                <option value="1">Escritorio</option>
                                <option value="2">Laptop</option>
                            </select>
                        </div>
                        <div>
                            <label class="form-label">Hostname</label>
                            <input type="text" name="hostname" class="form-control" placeholder="Ingresa el hostname" id="hostname" required>
                        </div>
                        <div>
                            <label class="form-label">CPU</label>
                            <input type="text" name="cpu" class="form-control" placeholder="Ingresa el modelo de CPU" id="cpu" required>
                        </div>
                        <div>
                            <label class="form-label">Generación de CPU</label>
                            <input type="number" name="cpu_gen" class="form-control" placeholder="Ej. 10" id="cpu_gen" required>
                        </div>
                        <div>
                            <label class="form-label">Sistema Operativo</label>
                            <input type="text" name="os" class="form-control" placeholder="Ej. Windows 11" id="os" required>
                        </div>
                        <div>
                            <label class="form-label">RAM</label>
                            <input type="number" name="ram_gb" class="form-control" placeholder="Ingresa la cantidad de memoria RAM" id="ram" required>
                        </div>
                        <div>
                            <label class="form-label">Almacenamiento</label>
                            <input type="number" name="storage_gb" class="form-control" placeholder="Ingresa la cantidad de almacenamiento" id="storage" required>
                        </div>
                        <div>
                            <label class="form-label">Tipo</label>
                            <select name="disk_type" id="storage_type" class="form-select" required>
                                <option value="SSD">SSD</option>
                                <option value="HDD">HDD</option>
                            </select>
                        </div>
                        <div>
                            <label class="form-label">Asignar a</label>
                            <input type="number" name="assigned_to" class="form-control" placeholder="Ingresa el numero de empleado" id="user" required>
                        </div>
                        <div>
                            <label class="form-label">Ubicacion</label>
                            <select name="location_id" id="ubicacion" class="form-select" required>
                                <option value="">Selecciona ubicación</option>
                                <?php foreach($locations as $location): ?>
                                <option value="<?= $location['id'] ?>"><?= htmlspecialchars($location['location_name']) ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div>
                            <label class="form-label">Departamento</label>
                            <select name="department_id" id="departamento" class="form-select" required>
                                <option value="">Selecciona departamento</option>
                                <?php foreach($departments as $department): ?>
                                <option value="<?= $department['id'] ?>"><?= htmlspecialchars($department['department']) ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn me-auto" data-bs-dismiss="modal">Cerrar</button>
                    <button type="submit" class="btn btn-success">Enviar</button>
                </div>
            </form>
        </div>
    </div>
</div>