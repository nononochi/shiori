<?php

// Define path to application directory
defined('APPLICATION_PATH')
        || define('APPLICATION_PATH', realpath(dirname(__FILE__) . '/../application'));


// Define application environment
defined('APPLICATION_ENV')
        || define('APPLICATION_ENV', (getenv('APPLICATION_ENV') ? getenv('APPLICATION_ENV') : 'production'));

// Ensure library/ is on include_path
set_include_path(implode(PATH_SEPARATOR, array(
            realpath(APPLICATION_PATH . '/../library'),
            get_include_path(),
        )));

set_include_path(
        get_include_path() .
        PATH_SEPARATOR . '../application/models'
);


require_once 'Zend/Loader/Autoloader.php';
$autoloader = Zend_Loader_Autoloader::getInstance();
$autoloader->setFallbackAutoloader(true);


// DB接続
require_once 'Zend/Db.php';
require_once '../application/configs/system.conf';
$params = array(
    'host'     => '127.0.0.1',
    'username' => USERNAME,
    'password' => PASSEORD,
    'dbname'   => 'shiori',
    'charset'  => 'UTF8'
);
$db = Zend_Db::factory('PDO_MYSQL', $params);
Zend_Db_Table_Abstract::setDefaultAdapter($db);

/** Zend_Application */
require_once 'Zend/Application.php';


// Create application, bootstrap, and run
$application = new Zend_Application(
                APPLICATION_ENV,
                APPLICATION_PATH . '/configs/application.ini'
);
$application->bootstrap()->run();

