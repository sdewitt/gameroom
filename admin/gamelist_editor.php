<?php

include 'main.php';

template_admin_header('Dashboard', 'dashboard');



ini_set('display_errors', 1);

ini_set('display_startup_errors', 1);

error_reporting(E_ALL);



require '_db.php';



$search_title = $_GET['search_title'] ?? '';

$search_type = $_GET['search_type'] ?? '';

$search_owner = $_GET['search_owner'] ?? '';

$show_checked_in = isset($_GET['show_checked_in']) ? 1 : 0;

$page = max(1, (int)($_GET['page'] ?? 1));

$perPage = 100;

$offset = ($page - 1) * $perPage;



$validSorts = ['yearlistid', 'gametitle', 'ownerid'];

$sort = in_array($_GET['sort'] ?? '', $validSorts) ? $_GET['sort'] : 'yearlistid';

$dir = ($_GET['dir'] ?? 'asc') === 'desc' ? 'desc' : 'asc';

$nextDir = $dir === 'asc' ? 'desc' : 'asc';



$accountStmt = $pdo->query("SELECT id, firstname, lastname, email, phone FROM accounts ORDER BY firstname");

$accountsRaw = $accountStmt->fetchAll(PDO::FETCH_ASSOC);

$accounts = [];

foreach ($accountsRaw as $a) {

    $name = $a['firstname'] . ' ' . $a['lastname'];

    $tooltip = "Email: {$a['email']}\nPhone: {$a['phone']}";

    $accounts[$a['id']] = ['name' => $name, 'tooltip' => $tooltip];

}



if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    if (isset($_POST['update'])) {

        $stmt = $pdo->prepare("UPDATE gamelist SET gametitle=?, gametype=?, builtyear=?, notes=?, ownerid=? WHERE gamelistid=? AND showyear='" . PRIOR_YEAR . "'");

        $stmt->execute([

            $_POST['gametitle'], $_POST['gametype'], $_POST['builtyear'], $_POST['notes'],

            $_POST['ownerid'], $_POST['gamelistid']

        ]);

        echo "<p style='color:green;'>Updated game #{$_POST['gamelistid']}</p>";

    } elseif (isset($_POST['add'])) {

        $idsPriorYear = $pdo->query("SELECT yearlistid FROM gamelist WHERE showyear = '" . PRIOR_YEAR . "' ORDER BY yearlistid")->fetchAll(PDO::FETCH_COLUMN);

        $nextID = 1;

        foreach ($idsPriorYear as $id) {

            if ((int)$id == $nextID) {

                $nextID++;

            } else {

                break;

            }

        }

        $stmt = $pdo->prepare("INSERT INTO gamelist (yearlistid, gametitle, gametype, builtyear, notes, ownerid, showyear, dateadded, checkedin)

                               VALUES (?, ?, ?, ?, ?, ?, '" . PRIOR_YEAR . "', NOW(), 0)");

        $stmt->execute([$nextID, $_POST['gametitle'], $_POST['gametype'], $_POST['builtyear'], $_POST['notes'], $_POST['ownerid']]);

        echo "<p style='color:blue;'>New game added with ID #$nextID.</p>";

    } elseif (isset($_POST['delete'])) {

        $stmt = $pdo->prepare("DELETE FROM gamelist WHERE gamelistid=? AND showyear='" . PRIOR_YEAR . "'");

        $stmt->execute([$_POST['gamelistid']]);

        echo "<p style='color:red;'>Deleted game #{$_POST['gamelistid']}</p>";

    } elseif (isset($_POST['checkin'])) {

        $stmt = $pdo->prepare("UPDATE gamelist SET checkedin = 1 WHERE gamelistid=?");

        $stmt->execute([$_POST['gamelistid']]);

        echo "<p style='color:orange;'>Checked in game #{$_POST['gamelistid']}</p>";

    }

}

?>



<style>

    table {

        width: 100%;

        border-collapse: collapse;

    }

    th, td {

        padding: 8px;

        border: 1px solid #ccc;

    }

    .checked-in-row {

        background-color: #d0f0d0;

    }

    .checkin-button {

        background-color: #4CAF50;

        color: white;

        font-weight: bold;

        padding: 4px 8px;

        border: none;

        cursor: pointer;

    }

    #addForm {

        background: #f0f8ff;

        border: 1px solid #ccc;

        padding: 10px;

        margin-bottom: 20px;

    }

</style>



<!-- Add New Game Button + Hidden Form at the top -->

<button type="button" onclick="toggleAddForm()">➕ Add New Game</button>

<div id="addForm" style="display:none;">

<form method="POST">

    <p><strong>Next Available ID:</strong> <span id="nextIDDisplay">Loading...</span></p>

    <input type="hidden" name="add" value="1">

    <input type="text" name="gametitle" placeholder="Title" required>

    <select name="gametype">

        <option value="P">Pinball</option>

        <option value="V">Video Game</option>

        <option value="C">Custom</option>

    </select>

    <input type="text" name="builtyear" placeholder="Year">

    <input type="text" name="notes" placeholder="Notes">

    <select name="ownerid" required>

        <option value="" disabled selected>Select Owner</option>

        <?php foreach ($accounts as $id => $info): ?>

            <option value="<?= $id ?>"><?= htmlspecialchars($info['name']) ?></option>

        <?php endforeach; ?>

    </select>

    <button type="submit">Add Game</button>

</form>

</div>



<form method='GET' style='margin-bottom:10px;'>

    <input type='text' name='search_title' placeholder='Search Title' value='<?= htmlspecialchars($search_title) ?>'>

    <select name='search_type'>

        <option value=''>All Types</option>

        <option value='P'<?= $search_type === 'P' ? ' selected' : '' ?>>Pinball</option>

        <option value='V'<?= $search_type === 'V' ? ' selected' : '' ?>>Video Game</option>

        <option value='C'<?= $search_type === 'C' ? ' selected' : '' ?>>Custom</option>

    </select>

    <select name='search_owner'>

        <option value=''>All Owners</option>

        <?php foreach ($accounts as $id => $info): ?>

            <option value='<?= $id ?>'<?= $search_owner == $id ? ' selected' : '' ?>><?= $info['name'] ?></option>

        <?php endforeach; ?>

    </select>

    <label><input type='checkbox' name='show_checked_in' value='1'<?= $show_checked_in ? ' checked' : '' ?>> Show Checked-In</label>

    <button type='submit'>Search</button>

</form>



<?php

$where = "showyear = '" . PRIOR_YEAR . "'";

$params = [];



if (!$show_checked_in) {

    $where .= " AND (checkedin IS NULL OR checkedin = 0)";

}

if ($search_title !== '') {

    $where .= " AND gametitle LIKE ?";

    $params[] = "%$search_title%";

}

if ($search_type !== '') {

    $where .= " AND gametype = ?";

    $params[] = $search_type;

}

if ($search_owner !== '') {

    $where .= " AND ownerid = ?";

    $params[] = $search_owner;

}



$countStmt = $pdo->prepare("SELECT COUNT(*) FROM gamelist WHERE $where");

$countStmt->execute($params);

$totalRecords = $countStmt->fetchColumn();

$totalPages = ceil($totalRecords / $perPage);



$query = "SELECT * FROM gamelist WHERE $where ORDER BY $sort $dir LIMIT $perPage OFFSET $offset";

$stmt = $pdo->prepare($query);

$stmt->execute($params);

$games = $stmt->fetchAll(PDO::FETCH_ASSOC);

?>



<table>

<thead>

    <tr>

        <th><a href='?sort=yearlistid&dir=<?= $nextDir ?>'>ID</a></th>

        <th><a href='?sort=gametitle&dir=<?= $nextDir ?>'>Title</a></th>

        <th>Type</th><th>Year</th><th>Notes</th>

        <th><a href='?sort=ownerid&dir=<?= $nextDir ?>'>Owner</a></th>

        <th>Action</th>

    </tr>

</thead>

<tbody>

<?php foreach ($games as $game): ?>

    <?php $isCheckedIn = $game['checkedin'] == 1; ?>

    <form method="POST">

    <tr class="<?= $isCheckedIn ? 'checked-in-row' : '' ?>">

        <td><?= $game['yearlistid'] ?></td>

        <td><input type="text" name="gametitle" value="<?= htmlspecialchars($game['gametitle']) ?>"></td>

        <td>

            <select name="gametype">

                <option value="P"<?= $game['gametype'] === 'P' ? ' selected' : '' ?>>Pinball</option>

                <option value="V"<?= $game['gametype'] === 'V' ? ' selected' : '' ?>>Video Game</option>

                <option value="C"<?= $game['gametype'] === 'C' ? ' selected' : '' ?>>Custom</option>

            </select>

        </td>

        <td><input type="text" name="builtyear" value="<?= htmlspecialchars($game['builtyear']) ?>"></td>

        <td><input type="text" name="notes" value="<?= htmlspecialchars($game['notes']) ?>"></td>

        <td>

            <select name="ownerid">

                <?php foreach ($accounts as $id => $info): ?>

                    <option value="<?= $id ?>"<?= $game['ownerid'] == $id ? ' selected' : '' ?>><?= $info['name'] ?></option>

                <?php endforeach; ?>

            </select>

        </td>

        <td>

            <input type="hidden" name="gamelistid" value="<?= $game['gamelistid'] ?>">

            <button name="update">Save</button>

            <button name="delete" onclick="return confirm('Are you sure?')">Delete</button>

            <?php if (!$isCheckedIn): ?>

                <button name="checkin" class="checkin-button">Check-In</button>

            <?php endif; ?>

        </td>

    </tr>

    </form>

<?php endforeach; ?>

</tbody>

</table>



<div style='margin-top:10px;'>

    <?php for ($i = 1; $i <= $totalPages; $i++): ?>

        <a href='?page=<?= $i ?>&search_title=<?= $search_title ?>&search_type=<?= $search_type ?>&search_owner=<?= $search_owner ?>&sort=<?= $sort ?>&dir=<?= $dir ?>&show_checked_in=<?= $show_checked_in ?>' style='margin-right:5px;'><?= $i ?></a>

    <?php endfor; ?>

</div>



<script>

function toggleAddForm() {

    const form = document.getElementById('addForm');

    const span = document.getElementById('nextIDDisplay');

    if (form.style.display === 'none') {

        span.textContent = 'Loading...';

        fetch('next_yearlistid.php')

            .then(response => response.text())

            .then(id => {

                span.textContent = id;

                form.style.display = 'block';

            })

            .catch(() => {

                span.textContent = 'Error';

                form.style.display = 'block';

            });

    } else {

        form.style.display = 'none';

    }

}

</script>

        <option value=''>All Types</option>
        <option value='P'<?= $search_type === 'P' ? ' selected' : '' ?>>Pinball</option>
        <option value='V'<?= $search_type === 'V' ? ' selected' : '' ?>>Video Game</option>
        <option value='C'<?= $search_type === 'C' ? ' selected' : '' ?>>Custom</option>
    </select>
    <select name='search_owner'>
        <option value=''>All Owners</option>
        <?php foreach ($accounts as $id => $info): ?>
            <option value='<?= $id ?>'<?= $search_owner == $id ? ' selected' : '' ?>><?= $info['name'] ?></option>
        <?php endforeach; ?>
    </select>
    <label><input type='checkbox' name='show_checked_in' value='1'<?= $show_checked_in ? ' checked' : '' ?>> Show Checked-In</label>
    <button type='submit'>Search</button>
</form>

<?php
$where = "showyear = '2025'";
$params = [];

if (!$show_checked_in) {
    $where .= " AND (checkedin IS NULL OR checkedin = 0)";
}
if ($search_title !== '') {
    $where .= " AND gametitle LIKE ?";
    $params[] = "%$search_title%";
}
if ($search_type !== '') {
    $where .= " AND gametype = ?";
    $params[] = $search_type;
}
if ($search_owner !== '') {
    $where .= " AND ownerid = ?";
    $params[] = $search_owner;
}

$countStmt = $pdo->prepare("SELECT COUNT(*) FROM gamelist WHERE $where");
$countStmt->execute($params);
$totalRecords = $countStmt->fetchColumn();
$totalPages = ceil($totalRecords / $perPage);

$query = "SELECT * FROM gamelist WHERE $where ORDER BY $sort $dir LIMIT $perPage OFFSET $offset";
$stmt = $pdo->prepare($query);
$stmt->execute($params);
$games = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<table>
<thead>
    <tr>
        <th><a href='?sort=yearlistid&dir=<?= $nextDir ?>'>ID</a></th>
        <th><a href='?sort=gametitle&dir=<?= $nextDir ?>'>Title</a></th>
        <th>Type</th><th>Year</th><th>Notes</th>
        <th><a href='?sort=ownerid&dir=<?= $nextDir ?>'>Owner</a></th>
        <th>Action</th>
    </tr>
</thead>
<tbody>
<?php foreach ($games as $game): ?>
    <?php $isCheckedIn = $game['checkedin'] == 1; ?>
    <form method="POST">
    <tr class="<?= $isCheckedIn ? 'checked-in-row' : '' ?>">
        <td><?= $game['yearlistid'] ?></td>
        <td><input type="text" name="gametitle" value="<?= htmlspecialchars($game['gametitle']) ?>"></td>
        <td>
            <select name="gametype">
                <option value="P"<?= $game['gametype'] === 'P' ? ' selected' : '' ?>>Pinball</option>
                <option value="V"<?= $game['gametype'] === 'V' ? ' selected' : '' ?>>Video Game</option>
                <option value="C"<?= $game['gametype'] === 'C' ? ' selected' : '' ?>>Custom</option>
            </select>
        </td>
        <td><input type="text" name="builtyear" value="<?= htmlspecialchars($game['builtyear']) ?>"></td>
        <td><input type="text" name="notes" value="<?= htmlspecialchars($game['notes']) ?>"></td>
        <td>
            <select name="ownerid">
                <?php foreach ($accounts as $id => $info): ?>
                    <option value="<?= $id ?>"<?= $game['ownerid'] == $id ? ' selected' : '' ?>><?= $info['name'] ?></option>
                <?php endforeach; ?>
            </select>
        </td>
        <td>
            <input type="hidden" name="gamelistid" value="<?= $game['gamelistid'] ?>">
            <button name="update">Save</button>
            <button name="delete" onclick="return confirm('Are you sure?')">Delete</button>
            <?php if (!$isCheckedIn): ?>
                <button name="checkin" class="checkin-button">Check-In</button>
            <?php endif; ?>
        </td>
    </tr>
    </form>
<?php endforeach; ?>
</tbody>
</table>

<div style='margin-top:10px;'>
    <?php for ($i = 1; $i <= $totalPages; $i++): ?>
        <a href='?page=<?= $i ?>&search_title=<?= $search_title ?>&search_type=<?= $search_type ?>&search_owner=<?= $search_owner ?>&sort=<?= $sort ?>&dir=<?= $dir ?>&show_checked_in=<?= $show_checked_in ?>' style='margin-right:5px;'><?= $i ?></a>
    <?php endfor; ?>
</div>

<script>
function toggleAddForm() {
    const form = document.getElementById('addForm');
    const span = document.getElementById('nextIDDisplay');
    if (form.style.display === 'none') {
        span.textContent = 'Loading...';
        fetch('next_yearlistid.php')
            .then(response => response.text())
            .then(id => {
                span.textContent = id;
                form.style.display = 'block';
            })
            .catch(() => {
                span.textContent = 'Error';
                form.style.display = 'block';
            });
    } else {
        form.style.display = 'none';
    }
}
</script>
