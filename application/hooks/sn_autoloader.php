<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of sn_autoloader
 *
 * @author Pham Trong
 */
class sn_autoloader {

    //put your code here
    private $_include_paths = array();

    public function register(array $paths = array()) {
        $this->_include_paths = $paths;

        spl_autoload_register(array($this, 'autoloader'));
    }

    public function autoloader($class) {
        foreach ($this->_include_paths as $path) {
            $filepath = $path . $class . EXT;

            if (!class_exists($class, FALSE) AND is_file($filepath)) {
                include_once($filepath);
                break;
            }
        }
    }

}