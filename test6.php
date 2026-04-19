<?php 
/**
 * PHP Grid Component
 *
 * @author Abu Ghufran <gridphp@gmail.com> - http://www.phpgrid.org
 * @version 2.0.0
 * @license: see license.txt included in package
 */

// include db config
include_once("config_grid.php");

// include and create object
include("lib/inc/jqgrid_dist.php");

// Database config file to be passed in phpgrid constructor
$db_conf = array( 	
					"type" 		=> PHPGRID_DBTYPE, 
					"server" 	=> PHPGRID_DBHOST,
					"user" 		=> PHPGRID_DBUSER,
					"password" 	=> PHPGRID_DBPASS,
					"database" 	=> PHPGRID_DBNAME
				);

$g = new jqgrid($db_conf);

$grid["caption"] = "Autocomplete Demo"; // expand grid to screen width
$grid["autowidth"] = true; // expand grid to screen width
$grid["multiselect"] = false; // allow you to multi-select through checkboxes
$grid["form"]["position"] = "center";
$grid["view_options"] = array("width"=>"500");

$g->set_options($grid);



$g->set_actions(array(	
						"add"=>true, // allow/disallow add
						"edit"=>true, // allow/disallow edit
						"delete"=>true, // allow/disallow delete
						"view"=>true, // allow/disallow delete
						"rowactions"=>true, // show/hide row wise edit/del/save option
						"search" => "advance", // show single/multi field search condition (e.g. simple or advance)
						"showhidecolumns" => false
					) 
				);

// this db table will be used for add,edit,delete
$g->table = "gamelist";

$g->select_command = "SELECT games.gamelistid, games.gametitle, games.gametype, user.firstname, user.lastname, games.ownerid, games.builtyear, games.manufacturer, games.awards
FROM gamelist as games
LEFT JOIN accounts as user on games.ownerid=user.id";

$col = array();
$col["title"] = "Id"; // caption of column
$col["name"] = "gamelistid"; // grid column name, must be exactly same as returned column-name from sql (tablefield or field-alias)
$col["width"] = "10";
$col["hidden"] = true; // hide column by default
$cols[] = $col;		

$col = array();
$col["title"] = "Game Title"; // caption of column
$col["name"] = "gametitle"; // grid column name, must be exactly same as returned column-name from sql (tablefield or field-alias)
$cols[] = $col;	

$col = array();
$col["title"] = "Game Type"; // caption of column
$col["name"] = "gametitle"; // grid column name, must be exactly same as returned column-name from sql (tablefield or field-alias)
$cols[] = $col;	

// pass the cooked columns to grid
$g->set_columns($cols);


// generate grid output, with unique grid name as 'list1'
$out = $g->render("list1");
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.1//EN" "http://www.w3.org/TR/xhtml11/DTD/xhtml11.dtd">
<html>
<head>
	<link rel="stylesheet" type="text/css" media="screen" href="lib/js/themes/redmond/jquery-ui.custom.css"></link>	
	<link rel="stylesheet" type="text/css" media="screen" href="lib/js/jqgrid/css/ui.jqgrid.css"></link>	
	<script src="lib/js/jquery.min.js" type="text/javascript"></script>
	<script src="lib/js/jqgrid/js/i18n/grid.locale-en.js" type="text/javascript"></script>
	<script src="lib/js/jqgrid/js/jquery.jqGrid.min.js" type="text/javascript"></script>	
	<script src="lib/js/themes/jquery-ui.custom.min.js" type="text/javascript"></script>
</head>
<body>
	<div style="margin:10px">
	<?php echo $out?>
	</div>
</body>
</html>