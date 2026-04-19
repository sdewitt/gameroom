<?php
include 'main2.php';
?>
<?php
// include db config
include_once("../config_grid.php");

// include and create object
include("../lib/inc/jqgrid_dist.php");

// Database config file to be passed in phpgrid constructor
$db_conf = array( 	
					"type" 		=> PHPGRID_DBTYPE, 
					"server" 	=> PHPGRID_DBHOST,
					"user" 		=> PHPGRID_DBUSER,
					"password" 	=> PHPGRID_DBPASS,
					"database" 	=> PHPGRID_DBNAME
				);

$g = new jqgrid($db_conf);

$grid["caption"] = "SFGE - Machine List"; // expand grid to screen width
$grid["autowidth"] = true; // expand grid to screen width
$grid["multiselect"] = true; // allow you to multi-select through checkboxes
$grid["form"]["position"] = "center";
$grid["view_options"] = array("width"=>"500");
$grid["height"] = "700px";
$grid["multiselectWidth"] = "5";
$grid["tooltip"] = true;
$grid["scroll"] = true;
$grid["delete_options"]["afterSubmit"] = 'function(response) { return [true,""]; }';

$grid["sortname"] = "yearlistid";
$grid["hotkeys"] = true;

$g->set_options($grid);

$g->set_actions(array(	
						"add"=>false, // allow/disallow add
						"edit"=>true, // allow/disallow edit
						"delete"=>true, // allow/disallow delete
						"view"=>true, // allow/disallow delete
                        "bulkedit"=>true, // allow/disallow delete
						"autofilter" => false,
						"rowactions"=>true, // show/hide row wise edit/del/save option
						"search" =>true, // show single/multi field search condition (e.g. simple or advance)
						"showhidecolumns" =>false,
						"export_excel"=>true, // export excel button
						"export_pdf"=>true, // export pdf button
						"export_csv"=>true, // export csv button
					) 
				);

// this db table will be used for add,edit,delete
$g->table = "gamelist";
$g->select_command = "SELECT games.gamelistid, games.yearlistid, games.approved, games.tournamentpin, games.emailed, games.gametitle, games.gametype, user.firstname, user.lastname, user.email, games.ownerid, games.builtyear, games.manufacturer, games.awards, games.showyear, IF(LENGTH(games.notes) > 0, 'Yes', '') as HasNotes, games.notes 
 FROM gamelist as games
LEFT JOIN accounts as user on games.ownerid=user.id where games.showyear=" . $_SESSION['showyear'];

//remove to work BELOW
$col = array();
$col["title"] = "Id"; 
$col["name"] = "gamelistid"; 
$col["editable"] = false;
$col["width"] = "15";
$col["show"] = array("list"=>false, "add"=>false, "edit"=>false, "view"=>false, "bulkedit"=>false);
$cols[] = $col;	

$col = array();
$col["title"] = "Id"; 
$col["name"] = "yearlistid"; 
$col["editable"] = true;
$col["width"] = "20";
$col["show"] = array("list"=>true, "add"=>true, "edit"=>true, "view"=>true, "bulkedit"=>false);
$cols[] = $col;	

$col = array();
$col["title"] = "Approved";
$col["name"] = "approved"; 
$col["editable"] = true;
$col["formatter"] = "checkbox";
$col["edittype"] = "checkbox";
$col["width"] = "10"; 
$col["show"] = array("list"=>false, "edit"=>true, "view"=>true);
$col["editoptions"] = array("value"=>"1:0");
$cols[] = $col;

$col = array();
$col["title"] = "Game Title"; 
$col["name"] = "gametitle"; 
$col["editable"] = true;
$col["show"] = array("list"=>true, "add"=>true, "edit"=>true, "view"=>true, "bulkedit"=>false);
$cols[] = $col;	

$col = array();
$col["title"] = "Type"; 
$col["name"] = "gametype"; 
$col["edittype"] = "select";
$col["width"] = "40"; 
$col["formatter"] = "select";
$col["editoptions"] = array("value"=>'p:Pinball;v:Arcade;c:Custom');
$cols[] = $col;	

$col = array();
$col["title"] = "Tournament";
$col["name"] = "tournamentpin"; 
$col["editable"] = true;
$col["formatter"] = "checkbox";
$col["edittype"] = "checkbox";
$col["width"] = "45"; 
$col["show"] = array("list"=>true, "edit"=>true, "view"=>true);
$col["editoptions"] = array("value"=>"1:0");
$cols[] = $col;

$col = array();
$col["title"] = "First Name"; 
$col["name"] = "firstname"; 
$cols[] = $col;	

$col = array();
$col["title"] = "Last Name"; 
$col["name"] = "lastname";
$cols[] = $col;	

$col = array();
$col["title"] = "Email"; 
$col["name"] = "email";
$cols[] = $col;

$col = array();
$col["title"] = "Owner ID";
$col["name"] = "ownerid"; 
$col["width"] = "80";
$col["editable"] = true;
$col["formatter"] = "select";
$col["edittype"] = "select";
$str = $g->get_dropdown_values("SELECT id as k, CONCAT(lastname, ', ', firstname) as v FROM `accounts` where id>1 order by v");
$col["editoptions"] = array("value"=>$str);
$col["show"] = array("list"=>false, "add"=>true, "edit"=>true, "view"=>true, "bulkedit"=>false);
$cols[] = $col;



$col = array();
$col["title"] = "Year"; 
$col["name"] = "builtyear"; 
$col["width"] = "60"; 
$col["editable"] = true;
$col["show"] = array("list"=>false, "add"=>true, "edit"=>true, "view"=>true, "bulkedit"=>false);
$cols[] = $col;	

$col = array();
$col["title"] = "Manufacturer"; 
$col["name"] = "manufacturer"; 
$col["width"] = "60"; 
$col["editable"] = true;
$col["show"] = array("list"=>true, "add"=>true, "edit"=>true, "view"=>true, "bulkedit"=>false);
$cols[] = $col;	

$col = array();
$col["title"] = "Awards"; 
$col["name"] = "awards"; 
$col["edittype"] = "select";
$col["formatter"] = "select";
$col["width"] = "60"; 
$col["editoptions"] = array("value"=>':;1:EM Pinball;2:Solid State Pinball;3:Modern Pinball;4:Restoration;5:Custom');
$col["editable"] = true;
$col["show"] = array("list"=>true, "add"=>true, "edit"=>true, "view"=>true, "bulkedit"=>false);
$cols[] = $col;	

$col = array();
$col["title"] = "Notes";
$col["name"] = "HasNotes";
$col["width"] = "65"; 
$col["editable"] = false;
$col["show"] = array("list"=>true, "add"=>true, "edit"=>true, "view"=>true, "bulkedit"=>false);
$cols[] = $col;

$col = array();
$col["title"] = "Notes:"; 
$col["name"] = "notes";
$col["show"] = array("list"=>false, "edit"=>true, "add"=>true, "view"=>true, "bulkedit"=>false);
$col["editable"] = true;
$col["edittype"] = "textarea";
$col["editoptions"] = array("rows"=>2, "cols"=>20);
$cols[] = $col;

$g->set_columns($cols);

$f = array();
$f["column"] = "emailed"; // exact column name, as defined above in set_columns or sql field name
$f["op"] = "eq"; // cn - contains, eq - equals
$f["value"] = 0;
$f["cellcss"] = "'background-color':'#ffb8a8','border':'1px solid darkgray','color':'black'"; 
$f["target"] = "gametitle";
$f_conditions[] = $f;

$f = array();
$f["column"] = "approved"; // exact column name, as defined above in set_columns or sql field name
$f["op"] = "eq"; // cn - contains, eq - equals
$f["value"] = 1;
$f["class"] = "approved-row"; // css class name
$f_conditions[] = $f;



//$f = array();
//$f["column"] = "emailed";
//$f["op"] = "eq";
//$f["value"] = "0";
//$f["cellcss"] = "'background-color':'#c8d578','border':'1px solid darkgray','color':'white'"; 
//$f["target"] = "gametitle";
//$f_conditions[] = $f;

$g->set_conditional_css($f_conditions);

//remove to work above



// generate grid output, with unique grid name as 'list1'

$out = $g->render("list1");
?>


<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.1//EN" "http://www.w3.org/TR/xhtml11/DTD/xhtml11.dtd">
<html>
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width,minimum-scale=1">
        <title>SFGE - Gameroom Machines</title>
        <link href="admin.css" rel="stylesheet" type="text/css">
        <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.7.1/css/all.css">
        <link rel="stylesheet" type="text/css" media="screen" href="../lib/js/themes/redmond/jquery-ui.custom.css"></link>	
        <link rel="stylesheet" type="text/css" media="screen" href="../lib/js/jqgrid/css/ui.jqgrid.css"></link>	
        <script src="../lib/js/jquery.min.js" type="text/javascript"></script>
        <script src="../lib/js/jqgrid/js/i18n/grid.locale-en.js" type="text/javascript"></script>
        <script src="../lib/js/jqgrid/js/jquery.jqGrid.min.js" type="text/javascript"></script>	
        <script src="../lib/js/themes/jquery-ui.custom.min.js" type="text/javascript"></script>
		<script src="https://kit.fontawesome.com/2a4ace1f1d.js" crossorigin="anonymous"></script>
    <style>
	tr.focus-row
	{
		background: #c8d578;
		color: green;
		border: 1px solid darkgray;
	}
  tr.approved-row
	{
		background: #72f795;
		color: black;
		border: 1px solid darkgray;
	}
  
	</style>
    </head>
    <body class="admin">
        <aside class="responsive-width-100 responsive-hidden">
            <h1>Admin Panel</h1>
            
        <a href="index.php"><i class="fas fa-tachometer-alt"></i>Dashboard</a>
        <a href="accounts.php"><i class="fas fa-users"></i>Accounts</a>
        <div class="sub">
            <a href="accounts.php"><span>&#9724;</span>View Accounts</a>
            <a href="account.php"><span>&#9724;</span>Create Account</a>
        </div>
        <a href="machines.php" class="selected"><i class="fa-solid fa-joystick"></i>Machines</a>
        <a href="machines_fullscreen.php"><i class="fa-solid fa-maximize"></i>Machines (Full Screen)</a>
        <a href="machines_prior.php"><i class="fa-light fa-joystick"></i>Machines Prior</a>
        <a href="processapproved.php"><i class="fas fa-users"></i>Send Emails</a>
        <a href="roles.php"><i class="fas fa-list"></i>Roles</a>
        <a href="emailtemplate.php"><i class="fas fa-envelope"></i>Email Templates</a>
        <a href="settings.php"><i class="fas fa-tools"></i>Settings</a>

        </aside>
        <main class="responsive-width-100">
            <header>
                <a class="responsive-toggle" href="#">
                    <i class="fas fa-bars"></i>
                </a>
                <div class="space-between"></div>
                <a href="about.php" class="right"><i class="fas fa-question-circle"></i></a>
                <a href="account.php?id={$_SESSION['id']}" class="right"><i class="fas fa-user-circle"></i></a>
                <a href="../logout.php" class="right"><i class="fas fa-sign-out-alt"></i></a>
            </header>
	<div style="margin:0px">
	<?php echo $out?>
	</div>
</main>
        <script>
        let aside = document.querySelector("aside"), main = document.querySelector("main"), header = document.querySelector("header");
        let asideStyle = window.getComputedStyle(aside);
        if (localStorage.getItem("admin_menu") == "closed") {
            aside.classList.add("closed", "responsive-hidden");
            main.classList.add("full");
            header.classList.add("full");
        }
        document.querySelector(".responsive-toggle").onclick = event => {
            event.preventDefault();
            if (asideStyle.display == "none") {
                aside.classList.remove("closed", "responsive-hidden");
                main.classList.remove("full");
                header.classList.remove("full");
                localStorage.setItem("admin_menu", "");
            } else {
                aside.classList.add("closed", "responsive-hidden");
                main.classList.add("full");
                header.classList.add("full");
                localStorage.setItem("admin_menu", "closed");
            }
        };
        document.querySelectorAll(".tabs a").forEach((element, index) => {
            element.onclick = event => {
                event.preventDefault();
                document.querySelectorAll(".tabs a").forEach((element, index) => element.classList.remove("active"));
                document.querySelectorAll(".tab-content").forEach((element2, index2) => {
                    if (index == index2) {
                        element.classList.add("active");
                        element2.style.display = "block";
                    } else {
                        element2.style.display = "none";
                    }
                });
            };
        });
        if (document.querySelector(".filters a")) {
            let filtersList = document.querySelector(".filters .list");
            let filtersListStyle = window.getComputedStyle(filtersList);
            document.querySelector(".filters a").onclick = event => {
                event.preventDefault();
                if (filtersListStyle.display == "none") {
                    filtersList.style.display = "flex";
                } else {
                    filtersList.style.display = "none";
                }
            };
            document.onclick = event => {
                if (!event.target.closest(".filters")) {
                    filtersList.style.display = "none";
                }
            };
        }
        document.querySelectorAll(".msg").forEach(element => {
            element.querySelector(".fa-times").onclick = () => {
                element.remove();
                history.replaceState && history.replaceState(null, '', location.pathname + location.search.replace(/[\?&]success_msg=[^&]+/, '').replace(/^&/, '?') + location.hash);
                history.replaceState && history.replaceState(null, '', location.pathname + location.search.replace(/[\?&]error_msg=[^&]+/, '').replace(/^&/, '?') + location.hash);
            };
        });
        history.replaceState && history.replaceState(null, '', location.pathname + location.search.replace(/[\?&]success_msg=[^&]+/, '').replace(/^&/, '?') + location.hash);
        history.replaceState && history.replaceState(null, '', location.pathname + location.search.replace(/[\?&]error_msg=[^&]+/, '').replace(/^&/, '?') + location.hash);
        </script>
        <style>
        .ui-jqgrid .ui-jqgrid-htable th div {
            height:auto;
            height:40px; /* your own height in pixel */
            overflow:hidden;
            padding-right:4px;
            padding-top:2px;
            position:relative;
            vertical-align:text-top;
            white-space:normal !important;
            }
            .ui-pager-control .ui-icon, .ui-custom-icon { zoom: 100%; -moz-transform: scale(1.45); }
            .ui-jqgrid .ui-jqgrid-pager .ui-pg-div span.ui-icon { margin: 0px 2px; }
            .ui-jqgrid .ui-jqgrid-pager { height: 28px; }
            .ui-jqgrid .ui-jqgrid-pager .ui-pg-div { line-height: 25px; }
</style>
    </body>
</html>
