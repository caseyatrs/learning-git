<?php

/**
 * Description of System
 *
 * @author caseya
 */
final class System {

    private $config; //the config simplexml obj

    private $appPath; //the path to the app handler

    private $initParams; //the init params

    /**
     *
     * @param <Array> $params used to pass path information
     */
    public function __construct($params){
        self::$initParams = $params;
    }

    public function init(){
        self::loadConfig();
        self::loadAutoloader();
        self::loadApp();
    }

    public function run(){
        self::init();
    }

    /**
     *
     * @param <Array> $params used pass path information
     */
    private function loadConfig(){
        if(empty(self::$initParams['system_config_path'])){
            throw Exception("Must init System with the System Config Path");
        }
        self::$config = simplexml_load_file(self::$initParams['system_config_path']);
    }

    private function loadApp(){
        if(!isset(self::$config->paths->app) && empty(self::$initParams['system_app_path'])){
            throw Exception("Must init System with the App Path");
        }

        try {
            self::$appPath = self::$config->paths->app;

            if(!empty(self::$initParams['system_app_path'])){
                self::$appPath = self::$initParams['system_app_path'];
            }

            if(!empty(self::$initParams['system_app_class'])){
                self::$app = self::$initParams['system_app_class'];
            } else {
                self::$app = self::$config->app->class;
            }
        } catch(Exception $e){
            throw Exception("Error setting up App: " . $e->getMessage());
        }

    }

    private function loadAutoloader(){
        try {
            $autoloaderPath = self::$config->paths->autoloader;

            if(!empty(self::$initParams['system_autoloader_path'])){
                $autoloaderPath = self::$initParams['system_autoloader_path'];
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

    private function autoload($class){
        //loading stuffs
    }

    //registry

    //fire event
        //types
            //return
            //request
                //forward
            //update
}