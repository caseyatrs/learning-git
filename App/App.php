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
                    if(count($xml->module) > 1){
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
            $modConfigFile = System::getPath('app_code') . $module . DIRECTORY_SEPARATOR . self::MODULE_CONFIG_DIR . DIRECTORY_SEPARATOR . self::MODULE_CONFIG_FILE;
            if(is_file($modConfigFile)){
                $moduleConfig = simplexml_load_file($modConfigFile);
                if($this->getConfig() == null){
                    $this->setConfig($moduleConfig);
                } else {
                    $this->addConfig($moduleConfig);
                }
            }
        }
    }
    
    public function getConfig(){
        return $this->config;
    }
    
    private function setConfig($config){
        $this->config = $config;
        return $this;
    }
    
    private function addConfig($config){
        //merge the two config modules thingers
        if(count($config->modules->module) > 1){
            foreach($config->modules as $mod){
                $this->appendSimpleXML($this->config->modules, $mod);
            }
        } else {
            $this->appendSimpleXML($this->config->modules, $config->modules);
        }
        return $this;
    }
    
    private function appendSimpleXML(&$simplexmlTo, &$simplexmlFrom){
        foreach ($simplexmlFrom->children() as $simplexmlChild){
            $simplexmlTemp = $simplexmlTo->addChild($simplexmlChild->getName(), (string) $simplexmlChild);
            foreach ($simplexmlChild->attributes() as $attrKey => $attrValue){
                $simplexmlTemp->addAttribute($attrKey, $attrValue);
            }

            $this->appendSimpleXML($simplexmlTemp, $simplexmlChild);
        }
    }
    
    public function run($params = array()){
        $this->init($params);
        System::fireEvent("request");
    }
    
    public function getControllers($base){
        $controllersArr = array();
        foreach($this->getConfig()->modules->module as $module){
            foreach($module->routes->children() as $route){
                if($route->getName() == $base) {
                    $className = (string)$route->controller->class;
                    $controllersArr[] = new $className;
                }
            }
        }
        if(count($controllersArr) == 0){
            //TODO: fireoff 404 event
        }
        return $controllersArr;
    }
}