<?php

namespace Source\App;

use Source\Core\Controller;
use Source\Models\Auth;
use Source\Support\Message;

class App extends Controller {
    
    /*
     * @var User
     */
    private $user;
    
    public function __construct() {
        parent::__construct(__DIR__ . '/../../themes/' . CONF_VIEW_APP . "/");
        
        //RESTRIÇÃO
        //SÓ FAZ TUDO SE ESTIVER LOGADO
        if(!Auth::user()) {
            $this->message->warning("Você não esta autenticado")->flash();
            redirect('/entrar');
        }
        
         (new \Source\Models\Report\Access())->report();
        (new Online())->report();
    }
    
    public function home() {
        echo flash();
        var_dump(Auth::user());
        
        echo "<a title='sair' href='" . url("/app/sair") . "'>Sair</a>";
    }
    
    
    
    public function logout() {
        (new Message())->info('Você saiu do sistema ' . ucfirst(Auth::user()->first_name) . ". Volte logo :) ")->flash();
        
        Auth::logout();
        redirect('/entrar');
    }

}
