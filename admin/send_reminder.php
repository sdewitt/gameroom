<?php
include 'main.php';

if (empty($_SESSION['send_reminder_csrf'])) {
    $_SESSION['send_reminder_csrf'] = bin2hex(random_bytes(32));
}

$recipient_options = [
    'prior_year' => [
        'label' => 'Prior Year',
        'description' => 'Game Bringers with at least one game during ' . PRIOR_YEAR,
        'where' => ' AND gamelist.showyear = ?'
    ],
    'all_years' => [
        'label' => 'All Years',
        'description' => 'All Game Bringers, regardless of year',
        'where' => ''
    ]
];

$selected_group = $_POST['recipient_group'] ?? '';
$processing = $_SERVER['REQUEST_METHOD'] === 'POST';
$error = '';
$recipients = [];

if ($processing) {
    if (!hash_equals($_SESSION['send_reminder_csrf'], $_POST['csrf_token'] ?? '')) {
        $error = 'Your session expired. Please reload the page and try again.';
    } elseif (!isset($recipient_options[$selected_group])) {
        $error = 'Select a recipient group before sending the reminder.';
    } else {
        $sql = 'SELECT accounts.* FROM accounts WHERE EXISTS (
                    SELECT 1 FROM gamelist
                    WHERE accounts.id = gamelist.ownerid' . $recipient_options[$selected_group]['where'] . '
                ) ORDER BY firstname, lastname';
        $stmt = $pdo->prepare($sql);
        $stmt->execute($selected_group === 'prior_year' ? [PRIOR_YEAR] : []);
        $recipients = $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}

template_admin_header('Send Reminder Emails', 'dashboard');
?>

<h2>Send Reminder Emails</h2>

<?php if (!$processing || $error): ?>
<div class="content-block">
    <h3>Who should receive this email?</h3>
    <p>No email will be sent until you select a group and click the send button.</p>

    <?php if ($error): ?>
    <div class="msg error">
        <i class="fas fa-exclamation-circle"></i>
        <p><?=htmlspecialchars($error, ENT_QUOTES, 'UTF-8')?></p>
        <i class="fas fa-times"></i>
    </div>
    <?php endif; ?>

    <form action="send_reminder.php" method="post" class="form responsive-width-100" onsubmit="return confirm('Send reminder emails to the selected group?')">
        <input type="hidden" name="csrf_token" value="<?=htmlspecialchars($_SESSION['send_reminder_csrf'], ENT_QUOTES, 'UTF-8')?>">

        <?php foreach ($recipient_options as $value => $option): ?>
        <label class="reminder-recipient-option">
            <input type="radio" name="recipient_group" value="<?=htmlspecialchars($value, ENT_QUOTES, 'UTF-8')?>" <?=$selected_group === $value ? 'checked' : ''?> required>
            <span>
                <strong><?=htmlspecialchars($option['label'], ENT_QUOTES, 'UTF-8')?></strong>
                <small><?=htmlspecialchars($option['description'], ENT_QUOTES, 'UTF-8')?></small>
            </span>
        </label>
        <?php endforeach; ?>

        <button type="submit">Send Reminder Emails</button>
    </form>
</div>

<style>
.reminder-recipient-option {
    align-items: flex-start;
    border: 1px solid #ddd;
    border-radius: 4px;
    cursor: pointer;
    display: flex;
    gap: 12px;
    margin-bottom: 12px;
    max-width: 650px;
    padding: 16px;
}
.reminder-recipient-option input {
    margin-top: 4px;
}
.reminder-recipient-option span,
.reminder-recipient-option small {
    display: block;
}
.reminder-recipient-option small {
    margin-top: 4px;
}
</style>
<?php else: ?>
<div class="content-block">
    <h3>Processing <?=htmlspecialchars($recipient_options[$selected_group]['label'], ENT_QUOTES, 'UTF-8')?> emails</h3>

    <?php if ($recipients): ?>
        <?php foreach ($recipients as $recipient): ?>
            <p>Processed <?=htmlspecialchars($recipient['firstname'] . ' ' . $recipient['email'], ENT_QUOTES, 'UTF-8')?></p>
            <?php send_reminder_email($recipient['firstname'], $recipient['email'], $recipient['guid']); ?>
        <?php endforeach; ?>
        <p><strong>Processing complete. <?=count($recipients)?> reminder email(s) sent.</strong></p>
    <?php else: ?>
        <p>Nothing to process.</p>
    <?php endif; ?>

    <p><a href="send_reminder.php">Send another reminder batch</a></p>
</div>
<?php endif; ?>

<?=template_admin_footer()?>
