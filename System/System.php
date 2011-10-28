<?php

/**
 * Description of System
 *
 * @author caseya
 */
final class System {

    static private $config; //the config simplexml obj

    static private $appPath; //the path to the app handler
    static private $appClass; //the class of the app handler
    static public $app; //the app handler
    
    static private $initParams; //the init params

    public function init($params){
        System::$initParams = $params;
        System::loadConfig();
        System::loadPaths();
        System::loadAutoloader();
        System::loadApp();
    }

    public function run($params){
        System::init($params);
        System::loadHelpers();
        System::loadLib();
        System::$app->run();
    }
    
    public function getConfig(){
        return System::$config;
    }

    /**
     *
     * @param <Array> $params used pass path information
     */
    private function loadConfig(){
        if(empty(System::$initParams['system_config_path'])){
            throw Exception("Must init System with the System Config Path");
        }
        System::$config = simplexml_load_file(System::$initParams['system_config_path']);
    }
    
    /**
     * Setup all the paths needed for the system
     */
    private function loadPaths(){
        //root
        if(empty(System::$config->paths->root)){
            if(!empty(System::$initParams['system_root_path'])){
                System::$config->paths->root = System::$initParams['system_root_path'];
            } else {
                throw Exception("No root path set.");
            }
        }
        if(!empty(System::$initParams['system_root_path'])){
            System::$config->paths->root = System::$initParams['system_root_path'];
        }
        
        //app
        if(empty(System::$config->paths->app)){
            if(!empty(System::$initParams['system_app_path'])){
                System::$config->paths->app = System::$initParams['system_app_path'];
            } else {
                throw Exception("No app path set.");
            }
        }
        if(!empty(System::$initParams['system_app_path'])){
            System::$config->paths->app = System::$initParams['system_app_path'];
        }
        System::$config->paths->app = System::replacePathMarkers(System::$config->paths->app);
        
        //TODO: decide if i want to make this more configurable
        //app_code
        System::$config->paths->app_code = (string)System::$config->paths->app . "code/";
        
        //system core
        if(empty(System::$config->paths->system_core)){
            if(!empty(System::$initParams['system_core_path'])){
                System::$config->paths->system_core = System::$initParams['system_core_path'];
            } else {
                throw Exception("No system core path set.");
            }
        }
        if(!empty(System::$initParams['system_core_path'])){
            System::$config->paths->system_core = System::$initParams['system_core_path'];
        }
        System::$config->paths->system_core = System::replacePathMarkers(System::$config->paths->system_core);
        
        //system lib
        if(empty(System::$config->paths->system_lib)){
            if(!empty(System::$initParams['system_lib_path'])){
                System::$config->paths->system_lib = System::$initParams['system_lib_path'];
            } else {
                throw Exception("No system core path set.");
            }
        }
        if(!empty(System::$initParams['system_lib_path'])){
            System::$config->paths->system_lib = System::$initParams['system_lib_path'];
        }
        System::$config->paths->system_lib = System::replacePathMarkers(System::$config->paths->system_lib);
        
    }

    private function replacePathMarkers($path){
        $newPath = preg_replace('/{{(.*)}}(.*)/','(string)System::$config->paths->$1 . "$2";',(string)$path);
        eval("\$newPath = $newPath");
        return $newPath;
    }
    
    private function loadApp(){
        try {
            System::$appPath = System::getPath("app");

            if(!empty(System::$initParams['system_app_class'])){
                System::$appClass = System::$initParams['system_app_class'];
            } else {
                System::$appClass = (string)System::$config->app->class;
            }
            
            System::$app = new System::$appClass();
            
        } catch(Exception $e){
            throw new Exception("Error setting up App: " . $e->getMessage() . " : " . $e->getLine());
        }

    }

    private function loadAutoloader(){
        //set include paths
        $DS = DIRECTORY_SEPARATOR;
        $PS = PATH_SEPARATOR;
        $curPath = get_include_path();
        $newPath = $curPath . $PS . System::getPath('system_lib') . $PS . System::getPath('system_core') . $PS . System::getPath('app') . $PS . System::getPath('app_code');
        set_include_path($newPath);
        
        try {
            $autoloaderPath = System::getPath('autoloader');

            if(!empty(System::$initParams['system_autoloader_path'])){
                $autoloaderPath = System::$initParams['system_autoloader_path'];
            }
        } catch (Exception $e) {
            $autoloaderPath = null;
        }

        if($autoloaderPath){
            include_once($autoloaderPath);
        } else {
            //load the System Autoloader
            spl_autoload_register(array("System","autoload"));
        }
    }
    
    //gets the absolute path for said path
    public function getPath($path){
        return (string)System::$config->paths->$path;
    }

    static public function autoload($class){
        //loading stuffs
            //replace _ with DIRECTORY_SEPARATOR;
//        var_dump("CLASS:" . $class);
        $class = str_replace("_",DIRECTORY_SEPARATOR,$class);
        include_once($class.".php");
        //v2.0 handle rewrites
    }

    private function loadHelpers(){
        if(!empty(System::$config->helpers)){
            $dir = (string)System::$config->paths->system_core . (string)System::$config->helpers->directory;
            foreach(System::$config->helpers->helper as $helper){
                include_once($dir . $helper->file);
            }
        }
    }
    
    private function loadLib(){
        ///meh i'll do it later
    }
    
    //fire event
        //types
            //request
                //forward
            //update
    static public function fireEvent($type,$params = array()){
        $objClass = (string)System::$config->events->$type;
        $obj = new $objClass();
        return $obj->run($params);
    }
}