<?php
include_once("config_grid.php");
include("lib/inc/jqgrid_dist.php");

include 'main.php';
check_loggedin($pdo);
$prioryear = $_SESSION['showyear'] - 1;

$countquery = "SELECT COUNT(*) AS total FROM gamelist where ownerid=" . $_SESSION['id'] . " and showyear=" . $prioryear  ;
$total_count_prior = $pdo->query($countquery)->fetchColumn();

$db_conf = array( 	
					"type" 		=> PHPGRID_DBTYPE, 
					"server" 	=> PHPGRID_DBHOST,
					"user" 		=> PHPGRID_DBUSER,
					"password" 	=> PHPGRID_DBPASS,
					"database" 	=> PHPGRID_DBNAME
				);

$g = new jqgrid($db_conf);

$grid["caption"] = "Machine List"; 
$grid["autowidth"] = true; // expand grid to screen width
$grid["multiselect"] = false; // allow you to multi-select through checkboxes
$grid["form"]["position"] = "center";
$grid["view_options"] = array("width"=>"500");
$grid["toolbar"] = "bottom";
$grid["height"] = "70%";
$grid["tooltip"] = true;
$grid["rowNum"] = 100;
$grid["rowList"] = array();
$grid["pgbuttons"] = false;
$grid["pgtext"] = null;
$grid["delete_options"]["afterSubmit"] = 'function(response) { if(response.status == 200)
{
window.location.reload();
}
}';

$g->set_options($grid);

$g->set_actions(array(	
						"add"=>false, // allow/disallow add
						"edit"=>false, // allow/disallow edit
						"delete"=>false, // allow/disallow delete
						"view"=>true, // allow/disallow delete
						"autofilter" => false,
						"rowactions"=>false, // show/hide row wise edit/del/save option
						"search" =>false, // show single/multi field search condition (e.g. simple or advance)
						"showhidecolumns" => false,
						"export_pdf"=>true,
                        "export_excel"=>true
					) 
				);

// this db table will be used for add,edit,delete
$g->table = "gamelist";
$g->select_command = "SELECT gamelistid, approved, gametitle, gametype, ownerid, dateadded, showyear, builtyear, manufacturer, awards, IF(LENGTH(notes) > 0, 'Yes', '') as HasNotes, notes FROM gamelist where ownerid=" . $_SESSION['id'] . " and showyear=" . $prioryear;

//remove to work BELOW
$col = array();
$col["title"] = "Id"; 
$col["name"] = "gamelistid"; 
$col["hidden"] = true;
$cols[] = $col;	



$col = array();
$col["title"] = "Game Title"; 
$col["viewable"] = true;
$col["editrules"]["readonly"] = true;
$col["name"] = "gametitle"; 
$cols[] = $col;	

$col = array();
$col["title"] = "<center>Game Type</center>"; 
$col["name"] = "gametype"; 
$col["edittype"] = "select";
$col["formatter"] = "select";
$col["width"] = "100";
$col["align"] = "center";
$col["editoptions"] = array("value"=>'p:Pinball;v:Arcade;c:Custom');
$cols[] = $col;	

$col = array();
$col["title"] = "Owner Id";
$col["name"] = "ownerid"; 
$col["hidden"] = true;
$cols[] = $col;

$col = array();
$col["title"] = "Date Added";
$col["name"] = "dateadded"; 
$col["hidden"] = true;
$cols[] = $col;

$col = array();
$col["title"] = "Show Year";
$col["name"] = "showyear"; 
$col["hidden"] = true;
$cols[] = $col;

$col = array();
$col["title"] = "<center>Year</center>";
$col["name"] = "builtyear"; 
$col["width"] = "70"; 
$col["align"] = "center";
$col["editable"] = False;
$cols[] = $col;	

$col = array();
$col["title"] = "<center>Manufacturer</center>"; 
$col["name"] = "manufacturer";
$col["width"] = "105"; 
$col["align"] = "center"; 
$col["editable"] = False;
$cols[] = $col;	

$col = array();
$col["title"] = "<center>Awards</center>"; 
$col["name"] = "awards"; 
$col["edittype"] = "select";
$col["formatter"] = "select";
$col["width"] = "105"; 
$col["align"] = "center";
$col["editoptions"] = array("value"=>':;1:EM Pinball;2:Solid State Pinball;3:Modern Pinball;4:Restoration;5:Custom;6:Arcade');
$col["editable"] = False;
$cols[] = $col;	

$col = array();
$col["title"] = "Notes";
$col["name"] = "HasNotes";
$col["editable"] = false;
$col["show"] = array("list"=>true, "edit"=>false, "add"=>false, "view"=>false);
$col["width"] = "35"; 
$cols[] = $col;

$col = array();
$col["title"] = "<center>Notes</center>"; 
$col["name"] = "notes";
$col["show"] = array("list"=>false, "edit"=>true, "add"=>true, "view"=>true);
$col["editable"] = true;
$col["edittype"] = "textarea";
$col["editoptions"] = array("rows"=>4, "cols"=>25);
$col["editable"] = False;
$cols[] = $col;

$f = array();
$f["column"] = "approved"; // exact column name, as defined above in set_columns or sql field name
$f["op"] = "eq"; // cn - contains, eq - equals
$f["value"] = 1;
$f["class"] = "focus-row"; // css class name
$f_conditions[] = $f;

$g->set_conditional_css($f_conditions);




$g->set_columns($cols);
//remove to work above

$out = $g->render("list1");
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.1//EN" "http://www.w3.org/TR/xhtml11/DTD/xhtml11.dtd">
<html>
<head>
	<title>SFGE - Game Registration</title>
	<link rel="stylesheet" href="style.css"></link>
	<link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.7.1/css/all.css">
	<link rel="stylesheet" type="text/css" media="screen" href="lib/js/themes/redmond/jquery-ui.custom.css"></link>	
	<link rel="stylesheet" type="text/css" media="screen" href="lib/js/jqgrid/css/ui.jqgrid.css"></link>	
	<script src="lib/js/jquery.min.js" type="text/javascript"></script>
	<script src="lib/js/jqgrid/js/i18n/grid.locale-en-custom.js" type="text/javascript"></script>
	<script src="lib/js/jqgrid/js/jquery.jqGrid.min.js" type="text/javascript"></script>	
	<script src="lib/js/themes/jquery-ui.custom.min.js" type="text/javascript"></script>
	<link rel="apple-touch-icon" sizes="180x180" href="/apple-touch-icon.png">
    <link rel="icon" type="image/png" sizes="32x32" href="/favicon-32x32.png">
    <link rel="icon" type="image/png" sizes="16x16" href="/favicon-16x16.png">
    <script src="https://kit.fontawesome.com/2a4ace1f1d.js" crossorigin="anonymous"></script>


	<style>
	tr.focus-row
	{
		background: #c8d578;
		color: green;
		border: 1px solid darkgray;
	}
	</style>
	</head>
	<body class="loggedin">
		<nav class="navtop">
			<div>
				<h1>Southern-Fried Gaming Expo - Game Registration</h1>
				<a href="home.php"><i class="fas fa-home"></i>Home</a>
				<a href="profile.php"><i class="fas fa-user-circle"></i>Profile</a>
				<?php if ($_SESSION['role'] == 'Admin'): ?>
				<a href="admin/index.php" target="_blank"><i class="fas fa-user-cog"></i>Admin</a>
				<?php endif; ?>
				<a href="logout.php"><i class="fas fa-sign-out-alt"></i>Logout</a>
			</div>
		</nav>
		<div class="content">
			<p class="block">Welcome Back, <?=$_SESSION['fname']?>!<br><br>
			<?

			If ($total_count_prior == 0) {
				echo "<b>We see no record of machines registered for SFGE".$prioryear."</b><br><br>";
			} elseif ($total_count_prior == 1) {
				echo "<b>You had 1 machine registered for SFGE".$prioryear."</b><br><br>";
			} else {
				echo "<b>You registered ".$total_count_prior." machines for SFGE".$prioryear."</b><br><br>";
			}


			echo "<a class='btn-gold' href='home.php'><i class='fa-solid fa-rotate-left'></i> Return to SFGE". $_SESSION['showyear'] . " Submissions</a>";

?>          </p>
	<?php echo $out?>
	<?php echo $out?>
	<div class="greenfooter">Machines highlighted green have been approved for the event!</div>
	</div>

</body>
</html>
<script>
var opts = {
    'ondblClickRow': function (id) {
        jQuery(this).jqGrid('editGridRow', id, jQuery(this).jqGrid('getGridParam','edit_options'));
    }
};
</script>
