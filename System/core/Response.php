<?php

/**
 * Description of Router
 *
 * @author sbditto85
 */
class Response extends Router {
    
    private $body;
    
    public function isDispatch(){
        return true;
    }
    
    public function process($controller,$action){
        ob_start();
        $controller->$action();
        $this->body = ob_get_clean();
    }
    
    public function dispatch(){
        //setup headers
        //send content
        echo $this->body;
        //set flag that content sent
    }
}