<?php
include 'main.php';

$settings_definition = [
    'General' => [
        'CURRENT_YEAR' => ['type' => 'number'],
        'PRIOR_YEAR' => ['type' => 'number'],
        'STARTDATE' => ['type' => 'text'],
        'ENDDATE' => ['type' => 'text']
    ],
    'Registration' => [
        'AUTO_LOGIN_AFTER_REGISTER' => ['type' => 'checkbox']
    ],
    'Account Activation' => [
        'ACCOUNT_ACTIVATION' => ['type' => 'checkbox'],
        'MAIL_FROM' => ['type' => 'text'],
        'ACTIVATION_LINK' => ['type' => 'text'],
        'AUTOLOGIN_LINK' => ['type' => 'text']
    ]
];

$label_overrides = [];

$settings_defaults = [
    'CURRENT_YEAR' => (string)CURRENT_YEAR,
    'PRIOR_YEAR' => (string)PRIOR_YEAR,
    'STARTDATE' => STARTDATE,
    'ENDDATE' => ENDDATE,
    'AUTO_LOGIN_AFTER_REGISTER' => auto_login_after_register ? 'true' : 'false',
    'ACCOUNT_ACTIVATION' => account_activation ? 'true' : 'false',
    'MAIL_FROM' => mail_from,
    'ACTIVATION_LINK' => activation_link,
    'AUTOLOGIN_LINK' => autologin_link
];

function format_settings_label($key, $label_overrides) {
    if (isset($label_overrides[$key])) {
        return $label_overrides[$key];
    }
    return ucwords(strtolower(str_replace('_', ' ', $key)));
}

function get_db_settings($pdo) {
    try {
        $pdo->query('SELECT 1 FROM settings LIMIT 1');
    } catch (PDOException $e) {
        return [];
    }

    $stmt = $pdo->query('SELECT setting_key, setting_value FROM settings');
    $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
    $settings = [];
    foreach ($rows as $row) {
        $settings[$row['setting_key']] = $row['setting_value'];
    }
    return $settings;
}

function save_db_settings($pdo, $posted_settings) {
    $pdo->exec('CREATE TABLE IF NOT EXISTS settings (
        id INT NOT NULL AUTO_INCREMENT,
        setting_key VARCHAR(191) NOT NULL,
        setting_value TEXT NULL,
        updated_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
        PRIMARY KEY (id),
        UNIQUE KEY setting_key_unique (setting_key)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4');

    $stmt = $pdo->prepare('INSERT INTO settings (setting_key, setting_value) VALUES (?, ?) ON DUPLICATE KEY UPDATE setting_value = VALUES(setting_value)');
    foreach ($posted_settings as $key => $value) {
        $stmt->execute([$key, $value]);
    }
}

if (!empty($_POST)) {
    $posted = [];
    foreach ($settings_definition as $group => $settings) {
        foreach ($settings as $key => $meta) {
            if ($meta['type'] === 'checkbox') {
                $posted[$key] = isset($_POST[$key]) && $_POST[$key] === 'true' ? 'true' : 'false';
                continue;
            }
            $posted[$key] = trim($_POST[$key] ?? '');
        }
    }

    save_db_settings($pdo, $posted);
    header('Location: settings.php?success_msg=1');
    exit;
}

$current_settings = array_merge($settings_defaults, get_db_settings($pdo));

if (isset($_GET['success_msg']) && $_GET['success_msg'] == 1) {
    $success_msg = 'Settings updated successfully!';
}
?>
<?=template_admin_header('Settings', 'settings')?>

<h2>Settings</h2>

<?php if (isset($success_msg)): ?>
<div class="msg success">
    <i class="fas fa-check-circle"></i>
    <p><?=$success_msg?></p>
    <i class="fas fa-times"></i>
</div>
<?php endif; ?>

<div class="tabs">
    <?php $tab_index = 0; ?>
    <?php foreach ($settings_definition as $tab_name => $_): ?>
    <a href="#" class="<?=$tab_index === 0 ? 'active' : ''?>"><?=$tab_name?></a>
    <?php $tab_index++; ?>
    <?php endforeach; ?>
</div>

<div class="content-block">
    <form action="" method="post" class="form responsive-width-100">
        <?php $tab_index = 0; ?>
        <?php foreach ($settings_definition as $group_name => $settings): ?>
        <div class="tab-content" style="display: <?=$tab_index === 0 ? 'block' : 'none'?>;">
            <?php foreach ($settings as $key => $meta): ?>
            <?php
                $value = $current_settings[$key] ?? '';
                $label = format_settings_label($key, $label_overrides);
                $safe_value = htmlspecialchars($value, ENT_QUOTES);
            ?>
            <label for="<?=$key?>"><?=$label?></label>
            <?php if ($meta['type'] === 'checkbox'): ?>
            <input type="hidden" name="<?=$key?>" value="false">
            <input type="checkbox" name="<?=$key?>" id="<?=$key?>" value="true" <?=$value === 'true' ? 'checked' : ''?>>
            <?php else: ?>
            <input type="<?=$meta['type']?>" name="<?=$key?>" id="<?=$key?>" value="<?=$safe_value?>" placeholder="<?=$label?>">
            <?php endif; ?>
            <?php endforeach; ?>
        </div>
        <?php $tab_index++; ?>
        <?php endforeach; ?>

        <div class="submit-btns">
            <input type="submit" value="Save">
        </div>
    </form>
</div>

<script>
document.querySelectorAll("input[type='checkbox']").forEach(checkbox => {
    checkbox.addEventListener('change', () => {
        checkbox.value = checkbox.checked ? 'true' : 'false';
    });
});
</script>

<?=template_admin_footer()?>
