<?php
include 'main.php';
// Prepare roles query
$bis_em= $pdo->query('SELECT bis_em, count(*) As votes, gamelist.gametitle, accounts.firstname, accounts.lastname FROM votes inner join gamelist as gamelist on votes.bis_em = gamelist.gamelistid inner join accounts as accounts on accounts.id = gamelist.ownerid GROUP BY bis_em, gamelist.gametitle
ORDER BY count(*) DESC LIMIT 2');
$bis_ss= $pdo->query('SELECT bis_ss, count(*) As votes, gamelist.gametitle, accounts.firstname, accounts.lastname FROM votes inner join gamelist as gamelist on votes.bis_ss = gamelist.gamelistid inner join accounts as accounts on accounts.id = gamelist.ownerid GROUP BY bis_ss, gamelist.gametitle
ORDER BY count(*) DESC LIMIT 2');
$bis_modern= $pdo->query('SELECT bis_modern, count(*) As votes, gamelist.gametitle, accounts.firstname, accounts.lastname FROM votes inner join gamelist as gamelist on votes.bis_modern = gamelist.gamelistid inner join accounts as accounts on accounts.id = gamelist.ownerid GROUP BY bis_modern, gamelist.gametitle
ORDER BY count(*) DESC LIMIT 2');
$bis_restore= $pdo->query('SELECT bis_restore, count(*) As votes, gamelist.gametitle, accounts.firstname, accounts.lastname FROM votes inner join gamelist as gamelist on votes.bis_restore = gamelist.gamelistid inner join accounts as accounts on accounts.id = gamelist.ownerid GROUP BY bis_restore, gamelist.gametitle
ORDER BY count(*) DESC LIMIT 2');
$bis_custom= $pdo->query('SELECT bis_custom, count(*) As votes, gamelist.gametitle, accounts.firstname, accounts.lastname FROM votes inner join gamelist as gamelist on votes.bis_custom = gamelist.gamelistid inner join accounts as accounts on accounts.id = gamelist.ownerid GROUP BY bis_custom, gamelist.gametitle
ORDER BY count(*) DESC LIMIT 2');
$bis_arcade = $pdo->query('SELECT bis_arcade, count(*) As votes, gamelist.gametitle, accounts.firstname, accounts.lastname FROM votes inner join gamelist as gamelist on votes.bis_arcade = gamelist.gamelistid inner join accounts as accounts on accounts.id = gamelist.ownerid GROUP BY bis_arcade, gamelist.gametitle
ORDER BY count(*) DESC LIMIT 2');



?>
<?=template_admin_header('Voting Results', 'Voting Results')?>

<h2>Voting Results</h2>

<div class="content-block">
    <div class="table">
        <table>
            <thead>
                <tr>
                    <td>Award</td>
                    <td>Machine</td>
                    <td>Owner</td>
                </tr>
            </thead>
            <tbody>              
                    <?php 
                    $count = 0;
                    while ($row = $bis_em->fetch()) {
                    echo "<tr style='background-color:#EBECED'>";
                    if( $count == 0 ) {echo "<td><b>Electro Mechanical Pinball (Winner)</b></td>";}
                    if( $count == 1 ) {echo "<td>Electro Mechanical Pinball (Runner-Up)</td>";}
                    echo "<td>".$row['gametitle']." (".$row['votes'].")</td><td>".$row['firstname']." ".$row['lastname']."</td></tr>";
                    $count=$count+1;
                    };
                    ?>
                        
                    <?php 
                    $count = 0;
                    while ($row = $bis_ss->fetch()) {
                    echo "<tr>";
                    if( $count == 0 ) {echo "<td><b>Solid State Pinball (Winner)</b></td>";}
                    if( $count == 1 ) {echo "<td>Solid State Pinball (Runner-Up)</td>";}
                    echo "<td>".$row['gametitle']." (".$row['votes'].")</td><td>".$row['firstname']." ".$row['lastname']."</td></tr>";
                    $count=$count+1;
                    };
                    ?>
                
                    <?php 
                    $count = 0;
                    while ($row = $bis_modern->fetch()) {
                    echo "<tr style='background-color:#EBECED'>";
                    if( $count == 0 ) {echo "<td><b>Modern Pinball (Winner)</b></td>";}
                    if( $count == 1 ) {echo "<td>Modern Pinball (Runner-Up)</td>";}
                    echo "<td>".$row['gametitle']." (".$row['votes'].")</td><td>".$row['firstname']." ".$row['lastname']."</td></tr>";
                    $count=$count+1;
                    };
                    ?>
            
                    <?php 
                    $count = 0;
                    while ($row = $bis_restore->fetch()) {
                    echo "<tr>";
                    if( $count == 0 ) {echo "<td><b>Restoration (Winner)</b></td>";}
                    if( $count == 1 ) {echo "<td>Restoration (Runner-Up)</td>";}
                    echo "<td>".$row['gametitle']." (".$row['votes'].")</td><td>".$row['firstname']." ".$row['lastname']."</td></tr>";
                    $count=$count+1;
                    };
                    ?>
                
                    <?php 
                    $count = 0;
                    while ($row = $bis_custom->fetch()) {
                    echo "<tr style='background-color:#EBECED'>";
                    if( $count == 0 ) {echo "<td><b>Custom (Winner)</b></td>";}
                    if( $count == 1 ) {echo "<td>Custom (Runner-Up)</td>";}
                    echo "<td>".$row['gametitle']." (".$row['votes'].")</td><td>".$row['firstname']." ".$row['lastname']."</td></tr>";
                    $count=$count+1;
                    };
                    ?>
                
                    <?php 
                    $count = 0;
                    while ($row = $bis_arcade->fetch()) {
                    echo "<tr>";
                    if( $count == 0 ) {echo "<td><b>Arcade (Winner)</b></td>";}
                    if( $count == 1 ) {echo "<td>Arcade (Runner-Up)</td>";}
                    echo "<td>".$row['gametitle']." (".$row['votes'].")</td><td>".$row['firstname']." ".$row['lastname']."</tr>";
                    $count=$count+1;
                    };
                    ?>
                </tr>
            </tbody>
        </table>
    </div>
</div>

<?=template_admin_footer()?>