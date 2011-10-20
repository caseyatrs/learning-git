<?php

/**
 * Description of App
 *
 * @author caseya
 */
class App {
    
    //registry
    
    
    private function init($params){
        //get all the modules
        //load all there configs into an app config
    }
    
    public function run($params = array()){
        $this->init($params);
        System::fireEvent("request");
    }
    
    public function getControllers($base){
        //get all the controllers assigned to that base name and return them as objects
        $dumbController = new HelloWorld_Controllers_Index();
        return array($dumbController);
    }
}