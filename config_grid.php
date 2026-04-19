<?php
include_once 'config.php';

// PHP Grid database connection settings, Only need to update these in new project

define("PHPGRID_DBTYPE","mysqli"); // mysql,oci8(for oracle),mssql,postgres,sybase
define("PHPGRID_DBHOST",db_host);
define("PHPGRID_DBUSER",db_user);
define("PHPGRID_DBPASS",db_pass);
define("PHPGRID_DBNAME",db_name);


define("PHPGRID_LIBPATH","../../lib/");
