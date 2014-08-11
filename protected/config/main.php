<?php
require(dirname(__FILE__).DIRECTORY_SEPARATOR.'../components/global.php');

// uncomment the following to define a path alias
// Yii::setPathOfAlias('local','path/to/local-folder');

// This is the main Web application configuration. Any writable
// CWebApplication properties can be configured here.
$a = array(
	'basePath'=>dirname(__FILE__).DIRECTORY_SEPARATOR.'..',
	'name'=>'Crowdfunding RSS',
  
  'timeZone'=>'GMT',    
// preloading 'log' component
	'preload'=>array('log'),

	// autoloading model and component classes
	'import'=>array(
		'application.models.*',
		'application.components.*',

		'application.components.*',
    'application.components.rating.*',
    //'application.components.extenders.*',
    //'application.components.behaviours.*',
    //'application.components.lib.*',
    'ext.giix-components.*', // giix components
      
    'ext.mail.YiiMailMessage', // mail system      
	),

	'modules'=>array(
		'gii'=>array(
			'class'=>'system.gii.GiiModule',
      'generatorPaths' => array(
  			'ext.giix-core', // giix generators
    	),        
			// If removed, Gii defaults to localhost only. Edit carefully to taste.
			'ipFilters'=>array('127.0.0.1','::1'),
		),
	),

	// application components
	'components'=>array(
		'user'=>array(
			// enable cookie-based authentication
			'allowAutoLogin'=>true,
      'autoUpdateFlash' => false, 
		),
		// uncomment the following to enable URLs in path-format
		
		'urlManager'=>array(
			'urlFormat'=>'path',
      'showScriptName'=>false,
			'rules'=>array(
        ''=>'site/index',
        'gii/<controller:\w+>/<action:[\w-]+>' => 'gii/<controller>/<action>',
				'<controller:\w+>/<id:\d+>'=>'<controller>/view',
				'<controller:\w+>/<action:\w+>/<id:\d+>'=>'<controller>/<action>',
				'<controller:\w+>/<action:\w+>/<data:\w+>'=>'<controller>/<action>',
				'<controller:\w+>/<action:\w+>'=>'<controller>/<action>',
			),
		),
    'clientScript'=>array(
      'coreScriptPosition'=>CClientScript::POS_END,
      'defaultScriptPosition'=>CClientScript::POS_END,
      'defaultScriptFilePosition'=>CClientScript::POS_END,
    ),      
		
		// uncomment the following to use a MySQL database
		'db' => array(
                  'enableProfiling'=>YII_DEBUG,
                  'enableParamLogging'=>YII_DEBUG,
                  'initSQLs'=>array("set time_zone='+00:00';"),
            ),
		'errorHandler'=>array(
			// use 'site/error' action to display errors
			'errorAction'=>'site/error',
		),
		'log'=>array(
			'class'=>'CLogRouter',
			'routes'=>array(
				array(
					//'class'=>'CWebLogRoute',
					'levels'=>'error, warning, trace, info',
					'class'=>'ext.yii-debug-toolbar.YiiDebugToolbarRoute',
          'ipFilters'=>array('127.0.0.1'),
          'enabled'=>YII_DEBUG,
				),
        array(
  					'levels'=>'error',
            'class'=>'CEmailLogRoute',
            'emails' => array('info@crowdfundingrss.com'),
            //'categories' => 'exception.*, system.*',
            'sentFrom'   => 'script@crowdfundingrss.com',
            'subject'    => 'Error on production for crowd RSS',
            'utf8' => true,
            'enabled'=>(!YII_DEBUG),  // send mail only from production
            //'enabled'=>YII_DEBUG,
            //*'categories'=>'system.db.*',* /
            'except'=>'exception.CHttpException.*'
        ),
        array(
  					'levels'=>'error',
            'class'=>'CFileLogRoute',
            'logFile' => 'application.error.log',
            'enabled'=>!YII_DEBUG,
            //'enabled'=>YII_DEBUG,
            /*'categories'=>'system.db.*',*/
        ),          
        array(
  					'levels'=>'warning',
            'class'=>'CFileLogRoute',
            'logFile' => 'application.warning.log',
            'enabled'=>!YII_DEBUG,
            /*'categories'=>'system.db.*',*/
        ),          
				// uncomment the following to show log messages on web pages
				/*
				array(
					'class'=>'CWebLogRoute',
				),*/
				
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

	// application-level parameters that can be accessed
	// using Yii::app()->params['paramName']
	'params'=>array(
		// this is used in contact page
		'noreplyEmail'=>array('no-reply@crowdfundingrss.com'=>'Crowdfunding RSS'),
		'adminEmail'=>array('info@crowdfundingrss.com'=>'Crowdfunding RSS'),
		'username'=>'',
		'pass'=>'',
    'absoluteHost' => 'http://www.crowdfundingrss.com/',
	),
);

$b = require(dirname(__FILE__) . '/local-main.php');

return array_merge_recursive_distinct($a,$b);