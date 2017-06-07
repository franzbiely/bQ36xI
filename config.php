<?php

// MAINTENANCE MODE... set the second parameter to true or false
define("MAINTENANCE_MODE",false);

// General Settings
define("FRONT_PAGE","login");
define("ITEM_DISPLAY_COUNT",100);
define("SECRET_GATE","secretcode");

if (preg_match('/testclients.susumamas.org.pg$/', $_SERVER['HTTP_HOST'])) {
    define( 'ENV', 'testsite' );
} 
elseif (preg_match('/susumamas.org.pg$/', $_SERVER['HTTP_HOST'])) {
    define( 'ENV', 'stage' );
}
else {
	define( 'ENV', 'local-franz' );
}

switch (ENV) {
	case 'testsite':
    	error_reporting(0);
        define("DBHOST","localhost");
		define("DBUSER","cldbtest_clients");
		define("DBPASS","hMD6QFrGELnL");
		define("DBNAME","cldbtest_clients_2");
		define("SITE_URL","http://testclients.susumamas.org.pg");
        break;

    case 'stage':
    	error_reporting(0);
        define("DBHOST","localhost");
		define("DBUSER","clientsu_ssmclsu");
		define("DBPASS","hMD6QFrGELnL");
		define("DBNAME","clientsu_ssmcldb");
		define("SITE_URL","http://clients.susumamas.org.pg");
        break;

    case 'local-franz':
        // Database
		define("DBHOST","localhost");
		define("DBUSER","root");
		define("DBPASS","");
		define("DBNAME","susumama");
		define("SITE_URL","http://susumama.dev");
        break;
}