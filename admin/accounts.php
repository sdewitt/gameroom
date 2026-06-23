<?php
include 'main.php';

// Update account from modal
if (isset($_POST['action']) && $_POST['action'] === 'update_account' && isset($_POST['id'])) {
    $id = (int)$_POST['id'];
    $stmt = $pdo->prepare('SELECT * FROM accounts WHERE id = ?');
    $stmt->execute([ $id ]);
    $account = $stmt->fetch(PDO::FETCH_ASSOC);
    if ($account) {
        $stmt = $pdo->prepare('UPDATE accounts SET username = ?, firstname = ?, lastname = ?, email = ?, phone = ?, role = ? WHERE id = ?');
        $stmt->execute([
            trim($_POST['username'] ?? ''),
            trim($_POST['firstname'] ?? ''),
            trim($_POST['lastname'] ?? ''),
            trim($_POST['email'] ?? ''),
            trim($_POST['phone'] ?? ''),
            trim($_POST['role'] ?? 'Member'),
            $id
        ]);
        header('Location: accounts.php?success_msg=2');
        exit;
    }
}

// Resend activation email from modal
if (isset($_POST['action']) && $_POST['action'] === 'resend_activation' && isset($_POST['id'])) {
    $id = (int)$_POST['id'];
    $stmt = $pdo->prepare('SELECT email, activation_code FROM accounts WHERE id = ?');
    $stmt->execute([ $id ]);
    $account = $stmt->fetch(PDO::FETCH_ASSOC);
    if ($account && !empty($account['activation_code']) && $account['activation_code'] !== 'activated') {
        send_activation_email($account['email'], $account['activation_code']);
        header('Location: accounts.php?success_msg=4');
        exit;
    }
    header('Location: accounts.php');
    exit;
}

// Delete account
if (isset($_GET['delete'])) {
    $stmt = $pdo->prepare('DELETE FROM accounts WHERE id = ?');
    $stmt->execute([ $_GET['delete'] ]);
    header('Location: accounts.php?success_msg=3');
    exit;
}

// Retrieve accounts
$stmt = $pdo->prepare('SELECT * FROM accounts ORDER BY id ASC');
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
    if ($_GET['success_msg'] == 4) {
        $success_msg = 'Activation email resent successfully!';
    }
}

$accounts_json = array_map(function($account) {
    $registered = strtotime($account['registered']);
    $last_seen = strtotime($account['last_seen']);
    return [
        'id' => (int)$account['id'],
        'username' => $account['username'],
        'firstname' => $account['firstname'],
        'lastname' => $account['lastname'],
        'email' => $account['email'],
        'phone' => $account['phone'],
        'role' => $account['role'],
        'activation_code' => $account['activation_code'],
        'is_activated' => $account['activation_code'] === 'activated',
        'registered' => $registered ? date('m/d/y', $registered) : '--',
        'registered_ts' => $registered ?: 0,
        'last_seen' => time_elapsed_string($account['last_seen']),
        'last_seen_raw' => $account['last_seen'],
        'last_seen_ts' => $last_seen ?: 0
    ];
}, $accounts);
?>
<?=template_admin_header('Accounts', 'accounts', 'view')?>

<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/ag-grid-community@32.3.4/styles/ag-grid.css">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/ag-grid-community@32.3.4/styles/ag-theme-quartz.css">
<script src="https://cdn.jsdelivr.net/npm/ag-grid-community@32.3.4/dist/ag-grid-community.min.js"></script>

<style>
main {
    padding-left: 280px;
    padding-right: 20px;
}
main.full {
    padding-left: 20px;
    padding-right: 20px;
}
#accountsGrid {
    width: 100%;
    height: calc(100vh - 115px);
    min-height: 420px;
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
.accounts-grid-actions button {
    border: 0;
    background: transparent;
    padding: 0;
    cursor: pointer;
    color: #3f4a5a;
    opacity: .15;
    transition: opacity .2s ease, color .2s ease;
}
.ag-row-hover .accounts-grid-actions a,
.accounts-grid-actions a:focus,
.ag-row-hover .accounts-grid-actions button,
.accounts-grid-actions button:focus {
    opacity: 1;
}
.accounts-grid-actions a.edit:hover,
.accounts-grid-actions a.edit:focus,
.accounts-grid-actions button.edit:hover,
.accounts-grid-actions button.edit:focus {
    color: #0d63f3;
}
.accounts-grid-actions a.delete:hover,
.accounts-grid-actions a.delete:focus {
    color: #d7263d;
}
#accountModal {
    position: fixed;
    inset: 0;
    background: rgba(15, 23, 42, .6);
    z-index: 1000;
    display: none;
    align-items: center;
    justify-content: center;
    padding: 15px;
}
#accountModal.open {
    display: flex;
}
#accountModal .modal-content {
    width: 100%;
    max-width: 660px;
    max-height: 92vh;
    overflow: auto;
    background: #fff;
    border-radius: 12px;
    padding: 24px;
}
#accountModal .modal-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 20px;
}
#accountModal .modal-header h3 {
    margin: 0;
}
#accountModal .close-modal {
    border: 0;
    background: transparent;
    font-size: 24px;
    line-height: 1;
    cursor: pointer;
}
#accountModal .modal-grid {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 16px;
}
#accountModal .modal-grid .full {
    grid-column: 1 / -1;
}
#accountModal .modal-grid label {
    display: block;
    margin-bottom: 6px;
    font-weight: 600;
}
#accountModal .modal-grid input,
#accountModal .modal-grid select {
    width: 100%;
}
#accountModal .activation-row {
    margin-top: 12px;
}
#accountModal .modal-actions {
    margin-top: 22px;
    display: flex;
    gap: 10px;
}
#accountModal .modal-actions .button,
#accountModal .activation-row .button {
    border: 0;
    border-radius: 4px;
    color: #fff;
    padding: 10px 14px;
    cursor: pointer;
    font: inherit;
}
#accountModal .modal-actions .button.primary {
    background: #2563eb;
}
#accountModal .modal-actions .button.secondary {
    background: #6b7280;
}
#accountModal .activation-row .button {
    background: #f59e0b;
}
</style>

<?php if (isset($success_msg)): ?>
<div class="msg success">
    <i class="fas fa-check-circle"></i>
    <p><?=$success_msg?></p>
    <i class="fas fa-times"></i>
</div>
<?php endif; ?>

<div id="accountsGrid" class="ag-theme-quartz"></div>

<div id="accountModal" aria-hidden="true">
    <div class="modal-content" role="dialog" aria-modal="true" aria-labelledby="editAccountTitle">
        <div class="modal-header">
            <h3 id="editAccountTitle">Edit Account</h3>
            <button type="button" class="close-modal" id="closeAccountModal" aria-label="Close">&times;</button>
        </div>
        <form method="post" id="accountEditForm" class="form">
            <input type="hidden" name="action" value="update_account">
            <input type="hidden" name="id" id="modal_id">
            <div class="modal-grid">
                <div class="full">
                    <label for="modal_username">Username</label>
                    <input type="text" id="modal_username" name="username" required>
                </div>
                <div>
                    <label for="modal_firstname">First Name</label>
                    <input type="text" id="modal_firstname" name="firstname" required>
                </div>
                <div>
                    <label for="modal_lastname">Last Name</label>
                    <input type="text" id="modal_lastname" name="lastname" required>
                </div>
                <div class="full">
                    <label for="modal_email">Email</label>
                    <input type="email" id="modal_email" name="email" required>
                </div>
                <div>
                    <label for="modal_phone">Phone</label>
                    <input type="text" id="modal_phone" name="phone" maxlength="12" placeholder="XXX-XXX-XXXX">
                </div>
                <div>
                    <label for="modal_role">Role</label>
                    <select id="modal_role" name="role">
                        <?php foreach ($roles_list as $role_item): ?>
                        <option value="<?=$role_item?>"><?=$role_item?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </div>
            <div class="activation-row" id="activationRow" hidden>
                <button type="submit" class="button" id="resendActivationButton" formaction="accounts.php" name="action" value="resend_activation">Resend activation email</button>
            </div>
            <div class="modal-actions">
                <button type="submit" class="button primary">Save Changes</button>
                <button type="button" class="button secondary" id="cancelAccountModal">Cancel</button>
            </div>
        </form>
    </div>
</div>

<script>
const accountRows = <?=json_encode($accounts_json, JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT)?>;
const roleValues = <?=json_encode($roles_list, JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT)?>;

const formatPhone = value => {
    const digits = (value || '').replace(/\D/g, '').slice(0, 10);
    if (digits.length <= 3) return digits;
    if (digits.length <= 6) return `${digits.slice(0, 3)}-${digits.slice(3)}`;
    return `${digits.slice(0, 3)}-${digits.slice(3, 6)}-${digits.slice(6)}`;
};

const actionsRenderer = params => {
    const id = Number(params.data.id);
    return `
        <div class="accounts-grid-actions">
            <button type="button" class="edit" title="Edit user ${id}" aria-label="Edit user ${id}" data-edit-account="${id}">
                <i class="fas fa-pen"></i>
            </button>
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
        {
            headerName: 'Phone',
            field: 'phone',
            minWidth: 150,
            valueFormatter: params => formatPhone(params.value)
        },
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
            tooltipField: 'last_seen_raw',
            comparator: (a, b, nodeA, nodeB) => Number(nodeA.data.last_seen_ts) - Number(nodeB.data.last_seen_ts)
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

const modalEl = document.getElementById('accountModal');
const closeModalBtn = document.getElementById('closeAccountModal');
const cancelModalBtn = document.getElementById('cancelAccountModal');
const editForm = document.getElementById('accountEditForm');
const activationRow = document.getElementById('activationRow');
const phoneInput = document.getElementById('modal_phone');

const openModal = account => {
    if (!account) return;
    document.getElementById('modal_id').value = account.id;
    document.getElementById('modal_username').value = account.username || '';
    document.getElementById('modal_firstname').value = account.firstname || '';
    document.getElementById('modal_lastname').value = account.lastname || '';
    document.getElementById('modal_email').value = account.email || '';
    document.getElementById('modal_phone').value = formatPhone(account.phone || '');
    document.getElementById('modal_role').value = roleValues.includes(account.role) ? account.role : roleValues[0];
    activationRow.hidden = account.is_activated;
    modalEl.classList.add('open');
    modalEl.setAttribute('aria-hidden', 'false');
};

const closeModal = () => {
    modalEl.classList.remove('open');
    modalEl.setAttribute('aria-hidden', 'true');
};

document.addEventListener('click', event => {
    const trigger = event.target.closest('[data-edit-account]');
    if (trigger) {
        const accountId = Number(trigger.getAttribute('data-edit-account'));
        const account = accountRows.find(item => Number(item.id) === accountId);
        openModal(account);
    }
    if (event.target === modalEl) {
        closeModal();
    }
});

closeModalBtn.addEventListener('click', closeModal);
cancelModalBtn.addEventListener('click', closeModal);
document.addEventListener('keydown', event => {
    if (event.key === 'Escape' && modalEl.classList.contains('open')) {
        closeModal();
    }
});

phoneInput.addEventListener('input', event => {
    event.target.value = formatPhone(event.target.value);
});
</script>

<?=template_admin_footer()?>
