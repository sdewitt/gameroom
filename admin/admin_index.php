<?php
include 'main.php';
$accounts = $pdo->query('SELECT * FROM accounts WHERE cast(registered as DATE) = cast(now() as DATE) ORDER BY registered DESC')->fetchAll(PDO::FETCH_ASSOC);
$accounts_total = $pdo->query('SELECT COUNT(*) AS total FROM accounts')->fetchColumn();
$inactive_accounts = $pdo->query('SELECT COUNT(*) AS total FROM accounts WHERE last_seen < date_sub(now(), interval 1 month)')->fetchColumn();
$active_accounts = $pdo->query('SELECT * FROM accounts WHERE last_seen > date_sub(now(), interval 1 day) ORDER BY last_seen DESC')->fetchAll(PDO::FETCH_ASSOC);
$active_accounts2 = $pdo->query('SELECT COUNT(*) AS total FROM accounts WHERE last_seen > date_sub(now(), interval 1 month)')->fetchColumn();
$total_count = $pdo->query('SELECT COUNT(*) AS total FROM gamelist where showyear=2023')->fetchColumn();
$total_count_prior = $pdo->query('SELECT COUNT(*) AS total FROM gamelist where showyear=2022')->fetchColumn();
$machinesdown_count = $pdo->query('SELECT COUNT(distinct machineid) FROM machineissues where status=1')->fetchColumn();
$opentickets_count = $pdo->query('SELECT COUNT(*) FROM machineissues where status=1')->fetchColumn();
$votes_count = $pdo->query('SELECT COUNT(*) FROM votes')->fetchColumn();

if ($_SESSION['name']=='sdewitt') {
    $gamecounts = $pdo->query('SELECT gamelist.ownerid, accounts.firstname, accounts.lastname, count(*) as count FROM gamelist inner join accounts as accounts on accounts.id = gamelist.ownerid where gamelist.showyear=2023 GROUP BY gamelist.ownerid ORDER BY COUNT DESC');
    $total_owed=0;
    $number_payees=0;
    while ($row = $gamecounts->fetch()) {
    if ($row['count'] > 9) {
    $total_owed=$total_owed+400;
    $number_payees=$number_payees+1;
    } elseif ($row['count'] > 7) {
    $total_owed=$total_owed+250;
    $number_payees=$number_payees+1;   
    } elseif ($row['count'] > 5) {
    $total_owed=$total_owed+175;
    $number_payees=$number_payees+1;     
    };
    };

};

date_default_timezone_set('America/New_York');
$date_votingopens = new DateTime('2023-07-28 16:00:00');
$date_votingcloses = new DateTime('2023-07-30 11:00:00');
$now = new DateTime();

?>
<?=template_admin_header('Dashboard', 'dashboard')?>

<h2>Machine Counts</h2>

<div class="dashboard">
    <div class="content-block stat">
        <div>
            <h3>Machines (Check-In/Total)</h3>
            <p>0 / <?=number_format($total_count)?><br>
            <font size=-2>2022 Total - <?=number_format($total_count_prior)?></font></p>
        </div>
        <i class="fa fa-bell"></i>
    </div>
    <div class="content-block badstat">
        <div>
            <h3>Machines Down</h3>
            <p><?=number_format($machinesdown_count)?><br>
        </div>
        <i class="fa-solid fa-down"></i>
    </div>
    <div class="content-block badstat">
        <div>
            <h3>Open Tickets</h3>
            <p><?=number_format($opentickets_count)?><br>
        </div>
        <i class="fa-solid fa-message-arrow-down"></i>
    </div>
    <?php
if ($_SESSION['name']=='sdewitt') {
    echo "<div class='content-block stat'><div>";
    echo "<h3>Exhibitor Payout</h3><p>";
    $formatter = new NumberFormatter('en_US', NumberFormatter::CURRENCY);
    echo $formatter->formatCurrency($total_owed, 'USD'), PHP_EOL;
    echo "<br><font size=-2>Number of Payees: ".$number_payees;
    echo "</font><br></div></div>";
} else {
echo "<div class='content-block stat'><div></div></div>";
};
?>


</div>
<div class="dashboard">
    <div class="content-block stat">
        <div>
            <h3>Vote Count</h3>
            <p><?=number_format($votes_count)?><br>
			<font size=-2>
			<?php
			if ($now > $date_votingcloses) {
echo "(Voting NOW CLOSED) - <a href='voting_results.php'>Vote Results</a>";
} elseif ($now > $date_votingopens) {
$interval = $date_votingcloses->diff($now);
echo "Voting Closes July 30th at 11:00am<br>";
echo $interval->format("%a days, %h hours, %i minutes");
echo "<br><a href='voting_results.php'>Vote Results</a>";
} else {
$interval = $date_votingopens->diff($now);
echo "Voting Opens July 28th at 4:00pm<br>";
echo $interval->format("%a days, %h hours, %i minutes");
echo "<br><a href='voting_results.php'>Vote Results</a>";
};
			?>
			</font></p>
        </div>
        <i class="fa-sharp fa-solid fa-trophy-star"></i>
    </div>
    <div class="content-block stat">
        <div></div>
    </div>
    <div class="content-block stat">
        <div></div>
    </div>
    <div class="content-block stat">
        <div></div>
    </div>
</div>



<h2>Accounts</h2>

<div class="dashboard">
    <div class="content-block stat">
        <div>
            <h3>New Accounts (&lt;1 day)</h3>
            <p><?=number_format(count($accounts))?></p>
        </div>
        <i class="fa fa-user-plus"></i>
    </div>

    <div class="content-block stat">
        <div>
            <h3>Total Accounts</h3>
            <p><?=number_format($accounts_total)?></p>
        </div>
        <i class="fas fa-users"></i>
    </div>

    <div class="content-block stat">
        <div>
            <h3>Active Accounts (&lt;30 days)</h3>
            <p><?=number_format($active_accounts2)?></p>
        </div>
        <i class="fas fa-user-clock"></i>
    </div>

    <div class="content-block stat">
        <div>
            <h3>Inactive Accounts (&gt;30 days)</h3>
            <p><?=number_format($inactive_accounts)?></p>
        </div>
        <i class="fas fa-user-clock"></i>
    </div>

</div>

<h2>New Accounts <span>(&lt;1 day)</span></h2>

<div class="content-block">
    <div class="table">
        <table>
            <thead>
                <tr>
                    <td>#</td>
                    <td>Username</td>
                    <td class="responsive-hidden">Email</td>
                    <td class="responsive-hidden">Activation Code</td>
                    <td class="responsive-hidden">Role</td>
                    <td class="responsive-hidden">Registered Date</td>
                    <td class="responsive-hidden">Last Seen</td>
                    <td>Actions</td>
                </tr>
            </thead>
            <tbody>
                <?php if (!$accounts): ?>
                <tr>
                    <td colspan="8" style="text-align:center;">There are no newly registered accounts</td>
                </tr>
                <?php endif; ?>
                <?php foreach ($accounts as $account): ?>
                <tr>
                    <td><?=$account['id']?></td>
                    <td><?=$account['username']?></td>
                    <td class="responsive-hidden"><?=$account['email']?></td>
                    <td class="responsive-hidden"><?=$account['activation_code'] ? $account['activation_code'] : '--'?></td>
                    <td class="responsive-hidden"><?=$account['role']?></td>
                    <td class="responsive-hidden"><?=$account['registered']?></td>
                    <td class="responsive-hidden" title="<?=$account['last_seen']?>"><?=time_elapsed_string($account['last_seen'])?></td>
                    <td>
                        <a href="account.php?id=<?=$account['id']?>">Edit</a>
                        <a href="accounts.php?delete=<?=$account['id']?>" onclick="return confirm('Are you sure you want to delete this account?')">Delete</a>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>

<br><br>

<h2>Active Accounts <span>(&lt;1 day)</span></h2>

<div class="content-block">
    <div class="table">
        <table>
            <thead>
                <tr>
                    <td>#</td>
                    <td>Username</td>
                    <td class="responsive-hidden">Email</td>
                    <td class="responsive-hidden">Activation Code</td>
                    <td class="responsive-hidden">Role</td>
                    <td class="responsive-hidden">Registered Date</td>
                    <td class="responsive-hidden">Last Seen</td>
                    <td>Actions</td>
                </tr>
            </thead>
            <tbody>
                <?php if (!$active_accounts): ?>
                <tr>
                    <td colspan="8" style="text-align:center;">There are no active accounts</td>
                </tr>
                <?php endif; ?>
                <?php foreach ($active_accounts as $account): ?>
                <tr>
                    <td><?=$account['id']?></td>
                    <td><?=$account['username']?></td>
                    <td class="responsive-hidden"><?=$account['email']?></td>
                    <td class="responsive-hidden"><?=$account['activation_code'] ? $account['activation_code'] : '--'?></td>
                    <td class="responsive-hidden"><?=$account['role']?></td>
                    <td class="responsive-hidden"><?=$account['registered']?></td>
                    <td class="responsive-hidden" title="<?=$account['last_seen']?>"><?=time_elapsed_string($account['last_seen'])?></td>
                    <td>
                        <a href="account.php?id=<?=$account['id']?>">Edit</a>
                        <a href="accounts.php?delete=<?=$account['id']?>" onclick="return confirm('Are you sure you want to delete this account?')">Delete</a>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>

<?=template_admin_footer()?>