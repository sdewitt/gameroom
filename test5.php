<?php header("Cache-Control: no-cache"); ?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.1//EN" "http://www.w3.org/TR/xhtml11/DTD/xhtml11.dtd">
<html>
<head>
    <!-- these css and js files are required by php grid -->
    <link rel="stylesheet" href="lib/js/themes/redmond/jquery-ui.custom.css"></link>
    <link rel="stylesheet" href="lib/js/jqgrid/css/ui.jqgrid.css"></link>
    <script src="lib/js/jquery.min.js" type="text/javascript"></script>
    <script src="lib/js/jqgrid/js/i18n/grid.locale-en.js" type="text/javascript"></script>
    <script src="lib/js/jqgrid/js/jquery.jqGrid.min.js" type="text/javascript"></script>
    <script src="lib/js/themes/jquery-ui.custom.min.js" type="text/javascript"></script>
    <!-- these css and js files are required by php grid -->

</head>
<?php
include_once("config_grid.php");
include("lib/inc/jqgrid_dist.php");

$db_conf = array(
    "type"      => PHPGRID_DBTYPE,
    "server"    => PHPGRID_DBHOST,
    "user"      => PHPGRID_DBUSER,
    "password"  => PHPGRID_DBPASS,
    "database"  => PHPGRID_DBNAME
);

$g = new jqgrid($db_conf);

// set few params
$opt["caption"] = "Sample Grid";
$g->set_options($opt);

// set database table for CRUD operations
$g->table = "gamelist";

// render grid and get html/js output
$out = $g->render("list1");
?>
<body>
    <div style="margin:10px">

    <!-- display grid here -->
    <?php echo $out?>
    <!-- display grid here -->

    </div>
</body>
</html>
