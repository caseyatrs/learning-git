<?php

/**
 * Description of App
 *
 * @author caseya
 */
class App {
    
    const MODULE_CONFIG_DIR = "etc";
    const MODULE_CONFIG_FILE = "config.xml";
    
    private $modules = array();
    private $config = null;
    //registry
    
    
    private function init($params){
        //get all the modules
        $this->loadModulesConfig();
        
        //load all there configs into an app config
        $this->loadAppConfig();
    }
    
    private function loadModulesConfig(){
        $modDir = (string)System::getConfig()->paths->app . "etc/modules/";
        
        $dir = opendir($modDir);
        if($dir){
            while($file = readdir($dir)){
                if($file != "." && $file != ".."){
                    $xml = simplexml_load_file($modDir . $file);
                    if(is_array($xml->module)){
                        foreach($xml->module as $mod){
                            $this->process_module_xml($mod);
                        }
                    } else {
                        $this->process_module_xml($xml->module);
                    }
                }
            }
        }
    }
    
    private function process_module_xml($mod){
        if($mod->enabled){
            $this->modules[] = (string)$mod->module_name;
        }
    }
    
    private function loadAppConfig(){
        foreach($this->modules as $module){
//            dump($module);
            $modConfigFile = System::getPath('app_code') . $module . DIRECTORY_SEPARATOR . self::MODULE_CONFIG_DIR . DIRECTORY_SEPARATOR . self::MODULE_CONFIG_FILE;
//            dump($modConfigFile);
            if(is_file($modConfigFile)){
                $moduleConfig = simplexml_load_file($modConfigFile);
//                dump($moduleConfig);
                if($this->getConfig() == null){
                    $this->setConfig($moduleConfig);
                } else {
                    $this->addConfig($moduleConfig);
                }
            }
            dump($this->getConfig()->modules);
        }
    }
    
    public function getConfig(){
        return $this->config;
    }
    
    private function setConfig($config){
        $newConfig = new SimpleXMLElement('<app></app>');
        $modArr = array();
        foreach($config->module as $mod){
            $modArr[] = $mod;
//            dump($mod);
//            $newConfig->modules->addChil $mod;
        }
        dump($modArr);
        $newConfig->addChild('modules',$modArr);
        dump($newConfig);
        $this->config = $newConfig;
        return $this;
    }
    
    private function addConfig($config){
        //merge the two config modules thingers
    }
    
    public function run($params = array()){
        $this->init($params);
        System::fireEvent("request");
    }
    
    public function getControllers($base){
        //get all the controllers assigned to that base name and return them as objects
        $dumbController = new HelloWorld_Controller_Index();
        return array($dumbController);
    }
}