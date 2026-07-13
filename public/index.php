<?php


//ini_set('display_errors', 1);
//ini_set('display_startup_errors', 1);
//error_reporting(E_ALL);


session_start();

require_once __DIR__ . '/../app/config/database.php';
require __DIR__ . '/../app/Models/Model.php';
require __DIR__ . '/../app/Controllers/AuthController.php';
require __DIR__ . '/../app/Controllers/LocationController.php';
require __DIR__ . '/../app/Controllers/AssetController.php';
require __DIR__ . '/../app/Controllers/BudgetController.php';
require __DIR__ . '/../app/Controllers/UrgenciasController.php';




$uri = str_replace('/ItAssets/public', '', $_SERVER['REQUEST_URI']);
$uri = parse_url($uri, PHP_URL_PATH); 
$method = $_SERVER['REQUEST_METHOD'];
 

$auth = new AuthController();
$location = new LocationController();
$asset = new AssetController();
$budget = new BudgetController();
$urgencias = new UrgenciasController();

if($uri === '/login' && $method === 'GET'){
    $auth->showLogin();
}elseif ($uri === '/login' && $method === 'POST'){
    $auth->login();
}elseif ($uri === '/logout'){
    $auth->logout();
} elseif ($uri === '/main' && $method === 'GET') {
    require_once __DIR__ . '/../app/views/main.php';
}elseif ($uri === '/locations' && $method === 'GET'){
    $location->index();
}elseif ($uri === '/asset/filter' && $method === 'GET'){
    $asset->filter();
}elseif ($uri === '/asset/details' && $method === 'GET'){
    $asset->details();
}elseif ($uri === '/asset/store' && $method === 'POST'){
    $asset->store();
}
elseif ($uri === '/budgets' && $method === 'GET'){
    $budget->index();
}elseif ($uri === '/budgets' && $method === 'POST'){
    $budget->calcularMochila();
}elseif ($uri === '/budgets/details' && $method === 'GET'){
    $budget->getBudget();
}
elseif ($uri === '/urgencias/all' && $method === 'GET'){
    $urgencias->index();
}elseif ($uri === '/urgencias/calcular' && $method === 'POST'){
    $urgencias->calcular();
}

else{
    echo "Ruta no encontrada";
}


?>



