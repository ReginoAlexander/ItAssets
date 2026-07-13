<?php

require __DIR__ . '/../../app/config/bootstrap.php';


?>

<div class="page-wrapper">
    <div class="page-header d-print-none">
        <div class="container-xl">
            <div class='row g-2 align-items-center'>
                    <div class="page-pretitle">Tecnologias de la informacion</div>
                    <h1 class="page-title">Inventario de activos</h1>
            </div>
        </div>
    </div>

    <div class="page-body" id='content'>
        <div class="container-xl">
            <div class="row">

                <div class="col-md-12 col-lg-2">
                    <?php include VIEW . 'components/locations.php'?>
                </div>

                <div class="col-md-12 col-lg-10">
                    <div class="card" id="assets-container">
                        <?php include VIEW . 'components/assets_card.php' ?>
                    </div>
                </div>
            </div>
        </div>
    </div>

</div>

<?php include VIEW . 'components/asset_modal.php';?>
<?php include VIEW . 'components/new_asset_modal.php'; ?>
<?php require VIEW . 'layout/footer.php'; ?>


<script>
    document.querySelectorAll('.location-item').forEach(item => {
        item.addEventListener('click', function (e){
            e.preventDefault();

            const id = this.dataset.id;

            fetch(`/ItAssets/public/asset/filter?id=${id}`)
                .then(res => res.text())
                .then(html => {
                    document.getElementById('assets-container').innerHTML = html
                });
        });
    });

    document.getElementById('assets-container').addEventListener('click', async (e) => {
        const btn = e.target.closest('.btn-details');
        const btnNew = e.target.closest('.btnNew');
        

        if(btn){
            e.preventDefault();
            const assetId = btn.dataset.id;

            try {
                const res = await fetch(`/ItAssets/public/asset/details?id=${assetId}`);
                if (!res.ok) throw new Error('Error');
                const data = await res.json();
                console.log(data);

                document.getElementById('modalNombre').textContent = data.hostname;
                document.getElementById('modalMarca').textContent = data.brand_name;
                document.getElementById('modalModelo').textContent = data.model_name;
                document.getElementById('modalDate').textContent = data.purchase_date;
                document.getElementById('modalStatus').textContent = data.status_name;
                document.getElementById('modalRAM').textContent = data.ram_gb + " GB" ;
                document.getElementById('modalCPU').textContent = data.cpu;
                document.getElementById('modalStorage').textContent = data.storage_gb + "GB " + data.disk_type;
                document.getElementById('modalSO').textContent = data.os;

                const modal = new bootstrap.Modal(document.getElementById('modal-large'));
                modal.show();
            } catch (err) {
                console.error(err);
            }
            return;
        }

        if(btnNew){
            e.preventDefault();
            console.log('pene');
            const modalNew = new bootstrap.Modal(document.getElementById('modalNew'));
            modalNew.show(); 
        }



    });


</script>