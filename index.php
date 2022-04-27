<?php
ob_start();
require __DIR__ . "/vendor/autoload.php";
/**
 * BOOTSTRAP
 */
use Source\Core\Session;
use CoffeeCode\Router\Router;

$session = new Session();
$route = new Router(url(), ":");

/*
 * WEB ROUTER
 */
$route->namespace("source\App");
$route->get("/", "Web:home");
$route->get("/sobre", "Web:about");
$route->get("/test", "Web:teste");
$route->post("/testeForm", "Web:testePost");
$route->get("/testTwo", "Web:testeTwo");

//blog
$route->group("/blog");
$route->get("/", "Web:blog");
$route->get("/p/{page}", "Web:blog");
$route->get("/{uri}", "Web:blogPost");
$route->post("/buscar", "Web:blogSearch");
$route->get("/buscar/{terms}/{page}", "Web:blogSearch");
$route->get("/em/{category}", "Web:blogCategory");
$route->get("/em/{category}/{page}", "Web:blogCategory");

//auth
$route->group(null);
$route->get("/entrar", "Web:login");
$route->post("/entrar", "Web:login");
$route->get("/cadastrar", "Web:register");
$route->post("/cadastrar", "Web:register");
$route->get("/recuperar", "Web:forget");
$route->get("/recuperar", "Web:forget");
$route->post("/recuperar", "Web:forget");
$route->get("/recuperar/{code}", "Web:reset");
$route->post("/recuperar/resetar", "Web:reset");


//APP
$route->group('/app');
$route->get('/', "App:home");
$route->get('/sair', "App:logout");


/**
 * OPTIN
 */
$route->group(null);
$route->get("/confirma", "Web:confirm");
$route->get("/obrigado/{email}", "Web:success");
$route->get("/obrigado/{email}", "Web:success");


/**
 * SERVICES
 */
$route->get("/termos", "Web:terms");





/*
 * ERROR ROUTES
 */
$route->namespace("Source\App")->group("/ops");
$route->get("/{errcode}", "Web:error");

/*
 * ROUTE
 */
$route->dispatch();

/*
 * ERROR REDICT
 */
if($route->error()){
    $route->redirect("/ops/{$route->error()}");
}

ob_end_flush();