<?php
require(dirname(__FILE__).DIRECTORY_SEPARATOR.'../components/global.php');

// This is the configuration for yiic console application.
// Any writable CConsoleApplication properties can be configured here.
$a = array(
	'basePath'=>dirname(__FILE__).DIRECTORY_SEPARATOR.'..',
	'name'=>'My Console Application',

	// preloading 'log' component
	'preload'=>array('log'),

	'import'=>array(
		'application.models.*',
		'application.components.*',

		'application.components.*',
    'application.components.rating.*',
    'application.components.parsers.*',
    //'application.components.functions.*',
    //'application.components.extenders.*',
    //'application.components.behaviours.*',
    //'application.components.lib.*',
    'ext.giix-components.*', // giix components
      
    'ext.mail.YiiMailMessage', // mail system      
	),    

	// application components
	'components'=>array(
    'db' => array(
                  'enableProfiling'=>YII_DEBUG,
                  'enableParamLogging'=>YII_DEBUG,
                  'initSQLs'=>array("set time_zone='+00:00';  wait_timeout=60;"),
            ),
		// uncomment the following to use a MySQL database
		/*
		'db'=>array(
			'connectionString' => 'mysql:host=localhost;dbname=testdrive',
			'emulatePrepare' => true,
			'username' => 'root',
			'password' => '',
			'charset' => 'utf8',
		),
		*/
		'log'=>array(
			'class'=>'CLogRouter',
			'routes'=>array(
				array(
					'class'=>'CFileLogRoute',
					'levels'=>'error, warning',
                    'logFile' => 'console.log',
                    'enabled'=>true,
				),
                array(
  					'levels'=>'trace, info',
                    'class'=>'CFileLogRoute',
                    'logFile' => 'console-info.log',
                    'enabled'=>true,
                    //'enabled'=>YII_DEBUG,
                    /*'categories'=>'system.db.*',*/
                ),
			),
		),
      
    'mail' => array(
        'class' => 'ext.mail.YiiMail',
        'transportType' => 'php', //smtp
        /*'transportOptions' => array_merge(array(
            'host' => 'smtp.gmail.com',
            'port' => '465',
            'encryption'=>'tls',
          ),require(dirname(__FILE__) . '/local-mail.php')
        ),*/
        'viewPath' => 'application.views.layouts.mail',
        'logging' => YII_DEBUG,
        'dryRun' => YII_DEBUG
    ),  
      
	),
	'params'=>array(
		// this is used in contact page
		'noreplyEmail'=>array('no-reply@crowdfundingrss.com'=>'Crowdfunding RSS'),
		'adminEmail'=>array('info@crowdfundingrss.com'=>'Crowdfunding RSS'),
    'scriptEmail'=>array('script@crowdfundingrss.com'=>'Script CF RSS'),
		'username'=>'',
		'pass'=>'',
    'absoluteHost' => 'http://crowdfundingrss.com/',
	),
    
);

$b = require(dirname(__FILE__) . '/local-console.php');

return array_merge_recursive_distinct($a,$b);