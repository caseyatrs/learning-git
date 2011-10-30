<?php

/**
 * Description of Router
 *
 * @author sbditto85
 */
class Request extends Router {
    
    private $base = null;
    private $action = null;
    
    public function __construct(){
        $this->init();
    }
    
    private function init(){
        //get url object
    }
    
    public function getBase(){
        if($this->base == null){
            //use url object to get base
            $this->base = "install";
        }
        return $this->base;
    }
    
    public function getAction(){
        if($this->action == null){
            //use url object to get action
            $this->action = "index";
        }
        return $this->action;
    }
}