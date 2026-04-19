<?php
include 'main.php';
// Default input product values
$account = [
    'username' => '',
    'password' => '',
    'email' => '',
    'activation_code' => '',
    'rememberme' => '',
    'role' => 'Member',
    'registered' => date('Y-m-d\TH:i:s'),
    'last_seen' => date('Y-m-d\TH:i:s')
];
// If editing an account
if (isset($_GET['id'])) {
    // Get the account from the database
    $stmt = $pdo->prepare('SELECT * FROM accounts WHERE id = ?');
    $stmt->execute([ $_GET['id'] ]);
    $account = $stmt->fetch(PDO::FETCH_ASSOC);
    // ID param exists, edit an existing account
    $page = 'Edit';
    if (isset($_POST['submit'])) {
        // Update the account
        $password = !empty($_POST['password']) ? password_hash($_POST['password'], PASSWORD_DEFAULT) : $account['password'];
        $stmt = $pdo->prepare('UPDATE accounts SET username = ?, password = ?, email = ?, activation_code = ?, rememberme = ?, role = ?, registered = ?, last_seen = ? WHERE id = ?');
        $stmt->execute([ $_POST['username'], $password, $_POST['email'], $_POST['activation_code'], $_POST['rememberme'], $_POST['role'], $_POST['registered'], $_POST['last_seen'], $_GET['id'] ]);
        header('Location: accounts.php?success_msg=2');
        exit;
    }
    if (isset($_POST['delete'])) {
        // Redirect and delete the account
        header('Location: accounts.php?delete=' . $_GET['id']);
        exit;
    }
} else {
    // Create a new account
    $page = 'Create';
    if (isset($_POST['submit'])) {
        $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
        $stmt = $pdo->prepare('INSERT IGNORE INTO accounts (username,password,email,activation_code,rememberme,role,registered,last_seen) VALUES (?,?,?,?,?,?,?,?)');
        $stmt->execute([ $_POST['username'], $password, $_POST['email'], $_POST['activation_code'], $_POST['rememberme'], $_POST['role'], $_POST['registered'], $_POST['last_seen'] ]);
        header('Location: accounts.php?success_msg=1');
        exit;
    }
}
?>
<?=template_admin_header($page . ' Account', 'accounts', 'manage')?>

<h2><?=$page?> Account</h2>

<div class="content-block">

    <form action="" method="post" class="form responsive-width-100">

        <label for="username">Username</label>
        <input type="text" id="username" name="username" placeholder="Username" value="<?=$account['username']?>" required>

        <label for="password"><?=$page == 'Edit' ? 'New ' : ''?>Password</label>
        <input type="text" id="password" name="password" placeholder="<?=$page == 'Edit' ? 'New ' : ''?>Password" value=""<?=$page == 'Edit' ? '' : ' required'?>>

        <label for="email">Email</label>
        <input type="text" id="email" name="email" placeholder="Email" value="<?=$account['email']?>" required>

        <label for="activation_code">Activation Code</label>
        <input type="text" id="activation_code" name="activation_code" placeholder="Activation Code" value="<?=$account['activation_code']?>">

        <label for="rememberme">Remember Me Code</label>
        <input type="text" id="rememberme" name="rememberme" placeholder="Remember Me Code" value="<?=$account['rememberme']?>">

        <label for="role">Role</label>
        <select id="role" name="role" style="margin-bottom: 30px;">
            <?php foreach ($roles_list as $role): ?>
            <option value="<?=$role?>"<?=$role==$account['role']?' selected':''?>><?=$role?></option>
            <?php endforeach; ?>
        </select>

        <label for="registered">Registered Date</label>
        <input id="registered" type="datetime-local" name="registered" value="<?=date('Y-m-d\TH:i:s', strtotime($account['registered']))?>" required>
    
        <label for="last_seen">Last Seen Date</label>
        <input id="last_seen" type="datetime-local" name="last_seen" value="<?=date('Y-m-d\TH:i:s', strtotime($account['last_seen']))?>" required>

        <div class="submit-btns">
            <input type="submit" name="submit" value="Submit">
            <?php if ($page == 'Edit'): ?>
            <input type="submit" name="delete" value="Delete" class="delete" onclick="return confirm('Are you sure you want to delete this account?')">
            <?php endif; ?>
        </div>

    </form>

</div>

<?=template_admin_footer()?>