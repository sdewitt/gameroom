<?php
include 'main.php';

// Delete account
if (isset($_GET['delete'])) {
    $stmt = $pdo->prepare('DELETE FROM accounts WHERE id = ?');
    $stmt->execute([ $_GET['delete'] ]);
    header('Location: accounts.php?success_msg=3');
    exit;
}

// Retrieve GET request parameters (if specified)
$search = isset($_GET['search']) ? trim($_GET['search']) : '';
$status = isset($_GET['status']) ? $_GET['status'] : '';
$activation = isset($_GET['activation']) ? $_GET['activation'] : '';
$role = isset($_GET['role']) ? $_GET['role'] : '';

// SQL where clause
$where = '';
$where .= $search ? 'WHERE (username LIKE :search OR email LIKE :search) ' : '';
if ($status == 'active') {
    $where .= $where ? 'AND last_seen > date_sub(now(), interval 1 month) ' : 'WHERE last_seen > date_sub(now(), interval 1 month) ';
}
if ($status == 'inactive') {
    $where .= $where ? 'AND last_seen < date_sub(now(), interval 1 month) ' : 'WHERE last_seen < date_sub(now(), interval 1 month) ';
}
if ($activation == 'pending') {
    $where .= $where ? 'AND activation_code != "activated" ' : 'WHERE activation_code != "activated" ';
}
if ($role) {
    $where .= $where ? 'AND role = :role ' : 'WHERE role = :role ';
}

// Retrieve accounts
$stmt = $pdo->prepare('SELECT * FROM accounts ' . $where . ' ORDER BY id ASC');
if ($search) {
    $search_param = '%' . $search . '%';
    $stmt->bindParam('search', $search_param, PDO::PARAM_STR);
}
if ($role) {
    $stmt->bindParam('role', $role, PDO::PARAM_STR);
}
$stmt->execute();
$accounts = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Handle success messages
if (isset($_GET['success_msg'])) {
    if ($_GET['success_msg'] == 1) {
        $success_msg = 'Account created successfully!';
    }
    if ($_GET['success_msg'] == 2) {
        $success_msg = 'Account updated successfully!';
    }
    if ($_GET['success_msg'] == 3) {
        $success_msg = 'Account deleted successfully!';
    }
}

$accounts_json = array_map(function($account) {
    $registered = strtotime($account['registered']);
    return [
        'id' => (int)$account['id'],
        'firstname' => $account['firstname'],
        'lastname' => $account['lastname'],
        'email' => $account['email'],
        'phone' => $account['phone'],
        'registered' => $registered ? date('m/d/y', $registered) : '--',
        'registered_ts' => $registered ?: 0,
        'last_seen' => time_elapsed_string($account['last_seen']),
        'last_seen_raw' => $account['last_seen']
    ];
}, $accounts);
?>
<?=template_admin_header('Accounts', 'accounts', 'view')?>

<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/ag-grid-community@32.3.4/styles/ag-grid.css">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/ag-grid-community@32.3.4/styles/ag-theme-quartz.css">
<script src="https://cdn.jsdelivr.net/npm/ag-grid-community@32.3.4/dist/ag-grid-community.min.js"></script>

<style>
#accountsGrid {
    width: 100%;
    height: 680px;
}
.accounts-grid-actions {
    display: flex;
    gap: 8px;
    align-items: center;
    justify-content: center;
}
.accounts-grid-actions a {
    color: #3f4a5a;
    opacity: .15;
    transition: opacity .2s ease, color .2s ease;
}
.ag-row-hover .accounts-grid-actions a,
.accounts-grid-actions a:focus {
    opacity: 1;
}
.accounts-grid-actions a.edit:hover,
.accounts-grid-actions a.edit:focus {
    color: #0d63f3;
}
.accounts-grid-actions a.delete:hover,
.accounts-grid-actions a.delete:focus {
    color: #d7263d;
}
</style>

<h2>Accounts</h2>

<?php if (isset($success_msg)): ?>
<div class="msg success">
    <i class="fas fa-check-circle"></i>
    <p><?=$success_msg?></p>
    <i class="fas fa-times"></i>
</div>
<?php endif; ?>

<div class="content-header links responsive-flex-column">
    <a href="account.php">Create Account</a>
    <form action="" method="get">
        <div class="filters">
            <a href="#"><i class="fas fa-filter"></i> Filters</a>
            <div class="list">
                <label><input type="checkbox" name="status" value="active"<?=$status=='active'?' checked':''?>>Active</label>
                <label><input type="checkbox" name="status" value="inactive"<?=$status=='inactive'?' checked':''?>>Inactive</label>
                <label><input type="checkbox" name="activation" value="pending"<?=$activation=='pending'?' checked':''?>>Pending Activation</label>
                <?php if ($role): ?>
                <label><input type="checkbox" name="role" value="<?=$role?>" checked><?=$role?></label>
                <?php endif; ?>
                <button type="submit">Apply</button>
            </div>
        </div>
        <div class="search">
            <label for="search">
                <input id="search" type="text" name="search" placeholder="Search username or email..." value="<?=$search?>" class="responsive-width-100">
                <i class="fas fa-search"></i>
            </label>
        </div>
    </form>
</div>

<div class="content-block">
    <div id="accountsGrid" class="ag-theme-quartz"></div>
</div>

<script>
const accountRows = <?=json_encode($accounts_json, JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT)?>;

const actionsRenderer = params => {
    const id = Number(params.data.id);
    return `
        <div class="accounts-grid-actions">
            <a class="edit" href="account.php?id=${id}" title="Edit user ${id}" aria-label="Edit user ${id}">
                <i class="fas fa-pen"></i>
            </a>
            <a class="delete" href="accounts.php?delete=${id}" title="Delete user ${id}" aria-label="Delete user ${id}" onclick="return confirm('Are you sure you want to delete this account?')">
                <i class="fas fa-trash"></i>
            </a>
        </div>
    `;
};

const gridOptions = {
    rowData: accountRows,
    pagination: true,
    paginationPageSize: 20,
    paginationPageSizeSelector: [20, 50, 100, 250],
    animateRows: true,
    defaultColDef: {
        sortable: true,
        filter: true,
        resizable: true,
        floatingFilter: true
    },
    columnDefs: [
        { headerName: 'First Name', field: 'firstname', minWidth: 130 },
        { headerName: 'Last Name', field: 'lastname', minWidth: 130 },
        { headerName: 'Email', field: 'email', minWidth: 220 },
        { headerName: 'Phone', field: 'phone', minWidth: 150 },
        {
            headerName: 'Registered Date',
            field: 'registered',
            minWidth: 150,
            comparator: (a, b, nodeA, nodeB) => Number(nodeA.data.registered_ts) - Number(nodeB.data.registered_ts)
        },
        {
            headerName: 'Last Seen',
            field: 'last_seen',
            minWidth: 130,
            tooltipField: 'last_seen_raw'
        },
        {
            headerName: '',
            field: 'actions',
            width: 72,
            sortable: false,
            filter: false,
            floatingFilter: false,
            suppressHeaderMenuButton: true,
            cellRenderer: actionsRenderer,
            pinned: 'right'
        }
    ]
};

agGrid.createGrid(document.getElementById('accountsGrid'), gridOptions);
</script>

<?=template_admin_footer()?>
