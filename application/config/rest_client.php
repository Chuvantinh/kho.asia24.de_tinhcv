<?php

defined('BASEPATH') OR exit('No direct script access allowed');


/*
  |--------------------------------------------------------------------------
  | REST Database Group
  |--------------------------------------------------------------------------
  |
  | Connect to a database group for keys, logging, etc. It will only connect
  | if you have any of these features enabled.
  |
  |	'default'
  |
 */
$config['entrest_client_db_group'] = ENVIRONMENT;
/*
  |--------------------------------------------------------------------------
  | REST API Logs Table Name
  |--------------------------------------------------------------------------
  |
  | The table name in your database that stores logs.
  |
  |	'logs'
  |
 */
$config['rest_client_logs_table']       = 'admin_rest_client_logs';

/*
  |--------------------------------------------------------------------------
  | REST Enable Logging
  |--------------------------------------------------------------------------
  |
  | When set to true REST_Controller will log actions based on key, date,
  | time and IP address. This is a general rule that can be overridden in the
  | $this->method array in each controller.
  |
  |	FALSE
  |
        CREATE TABLE `admin_rest_client_logs` (
          `id` int(11) NOT NULL AUTO_INCREMENT,
          `uri` varchar(255) NOT NULL,
          `method` varchar(6) NOT NULL,
          `params` text DEFAULT NULL,
          `api_key` varchar(40) NOT NULL,
          `server` varchar(255) DEFAULT NULL,
          `response` text DEFAULT NULL,
          `rtime` float DEFAULT NULL,
          `created_at` int(11) DEFAULT NULL,
          PRIMARY KEY (`id`)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8;
  |
 */
$config['rest_client_enable_logging'] = TRUE;
