<?php

define('ROOT', dirname(__DIR__)); 

define('BASE_URL', '/ItAssets/public/');

define('VIEW', ROOT . '/views/');

define('TABLER', BASE_URL. 'assets/tabler/core/');


require VIEW . 'layout/header.php';
require VIEW .  'layout/sidebar.php';
require VIEW . 'layout/scripts.php';




?>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2/dist/umd/popper.min.js"></script>
   <script src="https://cdn.jsdelivr.net/npm/bootstrap@5/dist/js/bootstrap.min.js"></script>