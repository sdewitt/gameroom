<?php
include 'main2.php';

header('X-Content-Type-Options: nosniff');

$showYear = isset($_SESSION['showyear']) ? (int)$_SESSION['showyear'] : 0;

if (isset($_GET['api'])) {
    header('Content-Type: application/json');

    if ($_GET['api'] === 'list') {
        $stmt = $pdo->prepare(
            "SELECT g.gamelistid, g.yearlistid, g.approved, g.tournamentpin, g.emailed, g.gametitle, g.gametype,
                    a.firstname, a.lastname, a.email, g.ownerid, g.builtyear, g.manufacturer, g.awards, g.showyear,
                    IF(LENGTH(g.notes) > 0, 'Yes', '') AS HasNotes, g.notes
             FROM gamelist g
             LEFT JOIN accounts a ON g.ownerid = a.id
             WHERE g.showyear = ?"
        );
        $stmt->execute([$showYear]);
        echo json_encode(["rows" => $stmt->fetchAll(PDO::FETCH_ASSOC)]);
        exit;
    }

    if ($_GET['api'] === 'owners') {
        $stmt = $pdo->query("SELECT id, CONCAT(lastname, ', ', firstname) AS label FROM accounts WHERE id > 1 ORDER BY label");
        echo json_encode(["rows" => $stmt->fetchAll(PDO::FETCH_ASSOC)]);
        exit;
    }

    if ($_GET['api'] === 'update' && $_SERVER['REQUEST_METHOD'] === 'POST') {
        $input = json_decode(file_get_contents('php://input'), true);
        $allowed = [
            'yearlistid','approved','gametitle','gametype','tournamentpin','ownerid','builtyear','manufacturer','awards','notes'
        ];

        $id = isset($input['gamelistid']) ? (int)$input['gamelistid'] : 0;
        $field = $input['field'] ?? '';
        $value = $input['value'] ?? null;

        if ($id <= 0 || !in_array($field, $allowed, true)) {
            http_response_code(400);
            echo json_encode(['ok' => false, 'error' => 'Invalid request']);
            exit;
        }

        if (in_array($field, ['approved', 'tournamentpin', 'ownerid', 'yearlistid', 'builtyear', 'awards'], true)) {
            $value = ($value === '' || $value === null) ? null : (int)$value;
        }

        $sql = "UPDATE gamelist SET {$field} = :value WHERE gamelistid = :id AND showyear = :showyear";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([
            ':value' => $value,
            ':id' => $id,
            ':showyear' => $showYear,
        ]);

        echo json_encode(['ok' => true]);
        exit;
    }

    if ($_GET['api'] === 'delete' && $_SERVER['REQUEST_METHOD'] === 'POST') {
        $input = json_decode(file_get_contents('php://input'), true);
        $ids = array_values(array_filter(array_map('intval', $input['ids'] ?? [])));

        if (!$ids) {
            echo json_encode(['ok' => true, 'deleted' => 0]);
            exit;
        }

        $placeholders = implode(',', array_fill(0, count($ids), '?'));
        $params = array_merge($ids, [$showYear]);
        $stmt = $pdo->prepare("DELETE FROM gamelist WHERE gamelistid IN ($placeholders) AND showyear = ?");
        $stmt->execute($params);

        echo json_encode(['ok' => true, 'deleted' => $stmt->rowCount()]);
        exit;
    }

    http_response_code(404);
    echo json_encode(['ok' => false, 'error' => 'Unknown endpoint']);
    exit;
}
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.1//EN" "http://www.w3.org/TR/xhtml11/DTD/xhtml11.dtd">
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width,minimum-scale=1">
    <title>SFGE - Gameroom Machines (AG Grid)</title>
    <link href="admin.css" rel="stylesheet" type="text/css">
    <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.7.1/css/all.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/ag-grid-community@32.3.4/styles/ag-grid.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/ag-grid-community@32.3.4/styles/ag-theme-quartz.css">
    <script src="https://kit.fontawesome.com/2a4ace1f1d.js" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/ag-grid-community@32.3.4/dist/ag-grid-community.min.js"></script>
    <style>
        .approved-row { background: #72f795 !important; }
        .emailed-pending { background-color: #ffb8a8; border: 1px solid darkgray; color: black; }
        .toolbar { display: flex; flex-wrap: wrap; align-items: center; gap: 8px; margin: 8px 0; }
        .toolbar input { min-width: 260px; padding: 6px; }
        .toolbar button { padding: 6px 10px; cursor: pointer; }
        #machinesGrid { width: 100%; height: 74vh; }
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
    <a href="machines.php"><i class="fa-solid fa-joystick"></i>Machines</a>
    <a href="machines_new.php" class="selected"><i class="fa-solid fa-table"></i>Machines (New)</a>
    <a href="machines_fullscreen.php"><i class="fa-solid fa-maximize"></i>Machines (Full Screen)</a>
    <a href="machines_prior.php"><i class="fa-light fa-joystick"></i>Machines Prior</a>
    <a href="processapproved.php"><i class="fas fa-users"></i>Send Emails</a>
    <a href="roles.php"><i class="fas fa-list"></i>Roles</a>
    <a href="emailtemplate.php"><i class="fas fa-envelope"></i>Email Templates</a>
    <a href="settings.php"><i class="fas fa-tools"></i>Settings</a>
</aside>
<main class="responsive-width-100">
    <header>
        <a class="responsive-toggle" href="#"><i class="fas fa-bars"></i></a>
        <div class="space-between"></div>
        <a href="about.php" class="right"><i class="fas fa-question-circle"></i></a>
        <a href="account.php?id=<?=$_SESSION['id']?>" class="right"><i class="fas fa-user-circle"></i></a>
        <a href="../logout.php" class="right"><i class="fas fa-sign-out-alt"></i></a>
    </header>

    <div class="toolbar">
        <input id="quickFilter" type="text" placeholder="Search...">
        <button id="refreshBtn">Refresh</button>
        <button id="exportCsvBtn">Export CSV</button>
        <button id="deleteSelectedBtn">Delete Selected</button>
    </div>

    <div id="machinesGrid" class="ag-theme-quartz"></div>
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

const typeOptions = { p: 'Pinball', v: 'Arcade', c: 'Custom' };
const awardsOptions = { '': '', 1: 'EM Pinball', 2: 'Solid State Pinball', 3: 'Modern Pinball', 4: 'Restoration', 5: 'Custom' };
let ownerMap = {};

async function apiGet(url) {
    const res = await fetch(url);
    if (!res.ok) throw new Error('Request failed');
    return res.json();
}

async function apiPost(url, payload) {
    const res = await fetch(url, {
        method: 'POST',
        headers: {'Content-Type': 'application/json'},
        body: JSON.stringify(payload)
    });
    if (!res.ok) throw new Error('Request failed');
    return res.json();
}

function refDataFormatter(map) {
    return params => map[params.value] ?? params.value ?? '';
}

const columnDefs = [
    { headerName: 'Id', field: 'yearlistid', width: 95, editable: true, filter: true },
    { headerName: 'Approved', field: 'approved', width: 110, editable: true, cellEditor: 'agSelectCellEditor', cellEditorParams: { values: ['0', '1'] }, filter: true },
    {
        headerName: 'Game Title', field: 'gametitle', minWidth: 220, editable: true, filter: true,
        cellClass: params => String(params.data?.emailed) === '0' ? 'emailed-pending' : ''
    },
    {
        headerName: 'Type', field: 'gametype', width: 120, editable: true,
        cellEditor: 'agSelectCellEditor', cellEditorParams: { values: Object.keys(typeOptions) },
        valueFormatter: refDataFormatter(typeOptions), filter: true
    },
    { headerName: 'Tournament', field: 'tournamentpin', width: 120, editable: true, cellEditor: 'agSelectCellEditor', cellEditorParams: { values: ['0', '1'] }, filter: true },
    { headerName: 'First Name', field: 'firstname', width: 130, filter: true },
    { headerName: 'Last Name', field: 'lastname', width: 130, filter: true },
    { headerName: 'Email', field: 'email', minWidth: 220, filter: true },
    {
        headerName: 'Owner ID', field: 'ownerid', width: 120, editable: true,
        cellEditor: 'agSelectCellEditor',
        cellEditorParams: () => ({ values: Object.keys(ownerMap) }),
        valueFormatter: params => ownerMap[String(params.value)] ?? params.value ?? '',
        filter: true
    },
    { headerName: 'Year', field: 'builtyear', width: 100, editable: true, filter: true },
    { headerName: 'Manufacturer', field: 'manufacturer', minWidth: 150, editable: true, filter: true },
    {
        headerName: 'Awards', field: 'awards', width: 170, editable: true,
        cellEditor: 'agSelectCellEditor', cellEditorParams: { values: Object.keys(awardsOptions) },
        valueFormatter: refDataFormatter(awardsOptions), filter: true
    },
    { headerName: 'Notes', field: 'HasNotes', width: 100, sortable: true, filter: true },
    { headerName: 'Notes:', field: 'notes', minWidth: 220, editable: true, filter: true }
];

const gridOptions = {
    rowSelection: { mode: 'multiRow' },
    defaultColDef: { sortable: true, resizable: true },
    columnDefs,
    suppressRowClickSelection: false,
    animateRows: true,
    pagination: true,
    paginationPageSize: 100,
    getRowId: params => String(params.data.gamelistid),
    getRowClass: params => String(params.data?.approved) === '1' ? 'approved-row' : '',
    onCellValueChanged: async params => {
        try {
            await apiPost('machines_new.php?api=update', {
                gamelistid: params.data.gamelistid,
                field: params.colDef.field,
                value: params.newValue
            });
        } catch (err) {
            params.node.setDataValue(params.colDef.field, params.oldValue);
            alert('Update failed.');
        }
    }
};

const gridApi = agGrid.createGrid(document.getElementById('machinesGrid'), gridOptions);

async function loadData() {
    const [ownersResp, listResp] = await Promise.all([
        apiGet('machines_new.php?api=owners'),
        apiGet('machines_new.php?api=list')
    ]);

    ownerMap = {};
    ownersResp.rows.forEach(o => ownerMap[String(o.id)] = o.label);

    gridApi.setGridOption('rowData', listResp.rows);
}

loadData();

const quickFilterEl = document.getElementById('quickFilter');
quickFilterEl.addEventListener('input', () => gridApi.setGridOption('quickFilterText', quickFilterEl.value));

document.getElementById('refreshBtn').addEventListener('click', () => loadData());

document.getElementById('exportCsvBtn').addEventListener('click', () => {
    gridApi.exportDataAsCsv({ fileName: 'machines.csv' });
});

document.getElementById('deleteSelectedBtn').addEventListener('click', async () => {
    const selectedRows = gridApi.getSelectedRows();
    if (!selectedRows.length) {
        alert('Please select at least one row to delete.');
        return;
    }
    if (!confirm(`Delete ${selectedRows.length} selected machine(s)?`)) {
        return;
    }
    await apiPost('machines_new.php?api=delete', { ids: selectedRows.map(row => row.gamelistid) });
    await loadData();
});
</script>
</body>
</html>
