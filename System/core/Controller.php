<?php

/**
 * Description of Router
 *
 * @author sbditto85
 */
class Controller extends Router {
    
    const ACTION_SUFFIX = "Action";
    
    public function getAction($action){
        return $action . self::ACTION_SUFFIX;
    }
}