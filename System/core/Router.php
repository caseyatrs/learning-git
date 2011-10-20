<?php

/**
 * Description of Router
 *
 * @author sbditto85
 */
class Router {
    
    private $request = null;
    private $response = null;
    
    public function run($params){
        $this->initRequest();
        $this->initResponse();
        
        $base = "testing";
        $action = "index";
        //search for matching urls through all registered controllers
        $controllers = System::$app->getControllers($base);
        foreach($controllers as $controller){
            $action = $controller->getAction($action);
            
            $this->getResponse()->process($controller,$action);
            
            if($this->getResponse()->isDispatch()){
                return $this->getResponse()->dispatch();
            }
        }
    }
    
    public function getRequest(){
        if($this->request == null){
            $this->initRequest();
        }
        return $this->request;
    }
    
    public function getResponse(){
        if($this->response == null){
            $this->initResponse();
        }
        return $this->response;
    }
    
    private function initRequest(){
        $this->request = new Request();
    }
    
    private function initResponse(){
        $this->response = new Response();
    }
}

?>
