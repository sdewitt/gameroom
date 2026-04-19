<?php
include 'main.php';

$templates = [
    'activation' => [
        'label' => 'Activation Email Template',
        'path' => '../activate_email.html'
    ],
    'autologin' => [
        'label' => 'Auto-Login Email Template',
        'path' => '../autologin_email.html'
    ]
];

if (isset($_POST['template_type'], $_POST['email_template']) && isset($templates[$_POST['template_type']])) {
    $selected_template = $_POST['template_type'];
    file_put_contents($templates[$selected_template]['path'], $_POST['email_template']);
    header('Location: emailtemplate.php?success_msg=1&template=' . urlencode($selected_template));
    exit;
}

$template_contents = [];
foreach ($templates as $key => $template) {
    $template_contents[$key] = file_exists($template['path']) ? file_get_contents($template['path']) : '';
}

$active_template = $_GET['template'] ?? array_key_first($templates);
if (!isset($templates[$active_template])) {
    $active_template = array_key_first($templates);
}

if (isset($_GET['success_msg']) && $_GET['success_msg'] == 1) {
    $success_msg = 'Email template updated successfully!';
}

$tinymce_api_key = env_value('TINYMCE_APIKEY', '');
$tinymce_script_url = 'https://cdn.tiny.cloud/1/' . rawurlencode($tinymce_api_key) . '/tinymce/7/tinymce.min.js';
?>
<?=template_admin_header('Email Template', 'emailtemplate')?>

<h2>Email Templates</h2>

<?php if (isset($success_msg)): ?>
<div class="msg success">
    <i class="fas fa-check-circle"></i>
    <p><?=$success_msg?></p>
    <i class="fas fa-times"></i>
</div>
<?php endif; ?>

<div class="content-block">
    <form action="" method="post">
        <label for="template_type">Select Email Template</label>
        <select id="template_type" name="template_type" style="max-width: 350px; margin-bottom: 15px;">
            <?php foreach ($templates as $key => $template): ?>
            <option value="<?=$key?>" <?=$active_template === $key ? 'selected' : ''?>><?=$template['label']?></option>
            <?php endforeach; ?>
        </select>

        <textarea id="email_template" name="email_template" style="height:400px; width:100%; max-width:100%;"><?=htmlspecialchars($template_contents[$active_template] ?? '', ENT_QUOTES)?></textarea>

        <div class="submit-btns">
            <input type="submit" value="Save">
        </div>
    </form>
</div>

<script src="<?=$tinymce_script_url?>" referrerpolicy="origin"></script>
<script>
const templateContents = <?=json_encode($template_contents, JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT)?>;
const templateSelect = document.getElementById('template_type');

if (window.tinymce) {
    tinymce.init({
        selector: '#email_template',
        height: 500,
        menubar: true,
        plugins: 'advlist autolink lists link image charmap preview anchor searchreplace visualblocks code fullscreen insertdatetime media table help wordcount',
        toolbar: 'undo redo | blocks | bold italic underline | alignleft aligncenter alignright alignjustify | bullist numlist outdent indent | link image | code fullscreen preview | removeformat',
        browser_spellcheck: true,
        setup: function(editor) {
            templateSelect.addEventListener('change', function() {
                const selectedTemplate = templateSelect.value;
                const content = templateContents[selectedTemplate] || '';
                editor.setContent(content);
                const nextUrl = new URL(window.location.href);
                nextUrl.searchParams.set('template', selectedTemplate);
                window.history.replaceState({}, '', nextUrl.toString());
            });
        }
    });
}
</script>

<?=template_admin_footer()?>

<?php if (isset($success_msg)): ?>
<div class="msg success">
    <i class="fas fa-check-circle"></i>
    <p><?=$success_msg?></p>
    <i class="fas fa-times"></i>
</div>
<?php endif; ?>

<div class="content-block">

    <form action="" method="post" >

        <?php if (isset($activation_email_template)): ?>
        <label for="activation_email_template">Activation Email Template</label>
        <textarea id="activation_email_template" name="activation_email_template" style="height:300px; width: 100%; max-width: 100%;"><?=$activation_email_template?></textarea>
        <?php endif; ?>
<br><br>
        <?php if (isset($twofactor_email_template)): ?>
        <label for="twofactor_email_template">Auto-Login Email Template</label>
        <textarea id="twofactor_email_template" name="twofactor_email_template" style="height:300px; width: 100%; max-width: 100%;"><?=$twofactor_email_template?></textarea>
        <?php endif; ?>

        <div class="submit-btns">
            <input type="submit" value="Save">
        </div>

    </form>

</div>

<?=template_admin_footer()?>