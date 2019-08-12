<?php

if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

/**
 * Model kiem tra va tao ket noi voi DB thu 2 trong config
 * Class M_admin_db2nd_connect
 *
 * @author chuvantinh1991@gmail.com
 */
class M_admin_db2nd_connect extends CI_Model
{

    // trang thai connect toi db thu 2
    // mac dinh la khong connect toi db2nd duoc.
    var $db2nd_connect_status = TRUE;
    
    // luu tru thong tin ket noi toi db db2nd
    var $db2nd = FALSE;

    // DB 2nd can ket noi toi
    var $active_group_db2nd = '';
    
    public function __construct()
    {
        // kiem tra trang thai ket noi toi db lms
        $this->db2nd_connect_status = $this->check_db2nd_connect();

        // tao ket noi toi db lms
        $this->db2nd_connect($this->db2nd, $this->db2nd_connect_status);
        parent::__construct();
    }
    
    /*
     * ham thuc hien kiem tra ket noi toi db cua db2nd
     * tra ve:  true - neu ket noi thanh cong
     *          false - neu khong the ket noi
     */
    public function check_db2nd_connect()
    {
        switch (ENVIRONMENT) {
            case 'development':
                error_reporting(E_ALL);
                ini_set('display_errors', 1);
                break;
            case 'testing':
            case 'production':
                ini_set('display_errors', 0);
                if (version_compare(PHP_VERSION, '5.3', '>=')) {
                    error_reporting(E_ALL & ~E_NOTICE & ~E_DEPRECATED & ~E_STRICT & ~E_USER_NOTICE & ~E_USER_DEPRECATED);
                } else {
                    error_reporting(E_ALL & ~E_NOTICE & ~E_STRICT & ~E_USER_NOTICE);
                }
                // error_reporting(0); // bo qua canh bao cua php
                break;
            default:
                header('HTTP/1.1 503 Service Unavailable.', TRUE, 503);
                echo 'The application environment is not set correctly.';
                exit(1); // EXIT_ERROR
        }

        // kiem tra su ton tai cua file config database
        if ( !defined('ENVIRONMENT') OR !file_exists($file_path = APPPATH.'config/'.ENVIRONMENT.'/database.php')) {
            if ( !file_exists($file_path = APPPATH.'config/database.php')) {
                show_error('No database 2nd connection settings were found in the database config file.');
            }
        }

        // include file database.php
        $active_group_db2nd = '';
        $db = array();
        include($file_path);

        if ( !isset($db) OR count($db) == 0 OR !isset($active_group_db2nd) OR !isset($db[$active_group_db2nd]) OR count($db[$active_group_db2nd]) == 0) {
            show_error('No database 2nd connection settings were found in the database config file.');
            return FALSE;
        }
        $this->active_group_db2nd = $active_group_db2nd;

        // lay thong tin cua config db2nd
        $db2nd_dbdriver	= isset($db[$active_group_db2nd]['dbdriver']) ? rawurldecode($db[$active_group_db2nd]['dbdriver']) : 'mysqli' ;
        $db2nd_hostname	= isset($db[$active_group_db2nd]['hostname']) ? rawurldecode($db[$active_group_db2nd]['hostname']) : 'localhost' ;
        $db2nd_username	= isset($db[$active_group_db2nd]['username']) ? rawurldecode($db[$active_group_db2nd]['username']) : '' ;
        $db2nd_password	= isset($db[$active_group_db2nd]['password']) ? rawurldecode($db[$active_group_db2nd]['password']) : '' ;
        $db2nd_database	= isset($db[$active_group_db2nd]['database']) ? rawurldecode($db[$active_group_db2nd]['database']) : '' ;
        $db2nd_port	    = isset($db[$active_group_db2nd]['port']) ? rawurldecode($db[$active_group_db2nd]['port']) : 3306 ;

        if($db2nd_dbdriver == 'mysqli'){
            $conn = @mysqli_connect($db2nd_hostname, $db2nd_username, $db2nd_password, $db2nd_database, $db2nd_port);
        } else {
            $conn = @mysql_connect($db2nd_hostname, $db2nd_username, $db2nd_password, $db2nd_database, $db2nd_port);
        }

        if(!$conn){
            return FALSE;
        } else {
            return TRUE;
        }
    } 
    
    /*
     * ham load database va ket noi toi db thu 2 la db cua lms
     * tra ve:  Fasle - neu trang thai ket noi toi db lms = FALSE
     *          Thong tin ket noi thanh cong toi db lms
     */
    public function db2nd_connect(&$db2nd, $db2nd_connect_status = FALSE)
    {
        // neu trang thai ket noi toi db2nd khong thanh cong thi dung ket noi
        if($db2nd_connect_status === FALSE){
            $db2nd = FALSE;
            return $db2nd;
        }
        
        // tao ket noi toi db2nd, co lay trang thai tra ve
        $db2nd = $this->load->database($this->active_group_db2nd, TRUE);
    }
}

