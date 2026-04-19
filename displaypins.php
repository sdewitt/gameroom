<?
define('db_host','gameatl.com:3306');
define('db_user','u0vunj7bxc6ww');
define('db_pass','f8lmh2l15m2m');
define('db_name','db0fnwzcvwqnvk');
define('db_charset','utf8');

try {
	$pdo = new PDO('mysql:host=' . db_host . ';dbname=' . db_name . ';charset=' . db_charset, db_user, db_pass);
	$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $exception) {
	// If there is an error with the connection, stop the script and display the error.
	exit('Failed to connect to database!');
}
?>

<table id="tablepress-18" class="tablepress tablepress-id-18">
<thead>
    <tr class="row-1 odd"><th class="column-1">Pinball Game Title</th></tr>
</thead>
<tbody class="row-hover">

<?
$stmt = $pdo->prepare('SELECT * FROM gamelist WHERE approved = 1 order by gametitle');
$stmt->execute();

$articles = $stmt->fetchAll();
$cycle = "even";
$count = 2;
foreach ($articles as $article) {
    echo "<tr class='row-". $count . " "; 
    if ($count % 2 == 0){
        echo "even"; 
    }
    else{
        echo "odd";
    }
    $count = $count + 1;
    echo "'><td class='column-1'>" . $article['gametitle'] . "</td></tr>";
}
?>

</tbody>
</table>