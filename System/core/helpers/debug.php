<?php

if(!function_exists('dump')){
    function dump($var){
        echo "<pre>";
        var_dump($var);
        echo "</pre>";
    }
}