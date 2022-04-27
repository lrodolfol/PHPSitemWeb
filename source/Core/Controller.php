<?php

namespace Source\Core;

use Source\Support\Seo;
use Source\Support\Message;
/**
 * FSPHP | Class Controller
 *
 * @author Robson V. Leite <cursos@upinside.com.br>
 * @package Source\Core
 */
class Controller
{
    protected $view;
    protected $seo;
    protected $message;
    
    
    
    public function __construct($pathToViews = null) {
        $this->view = new View($pathToViews);
        $this->seo = new Seo();
        $this->message = new Message();
    }
    
}