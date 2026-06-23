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
        toolbar: 'undo redo | blocks | bold italic underline | alignleft aligncenter alignright alignjustify | bullist numlist outdent indent | link image | rewriteEnergize | code fullscreen preview | removeformat',
        browser_spellcheck: true,
        setup: function(editor) {
            const energizedOpeners = [
                'Thanks for being part of the arcade and pinball magic this year.',
                'Your game is officially in the mix, and we could not be more excited.',
                'The lineup just got stronger thanks to your submission.',
                'We are thrilled to have your game joining the show floor.',
                'Your contribution helps make the game room bigger, brighter, and more fun.',
                'Huge thanks for sharing your machine with the community.',
                'Your submission brings even more replay-worthy fun to the event.',
                'We are excited to showcase your game alongside an amazing lineup.',
                'Players are going to love seeing your machine on the floor.',
                'Thanks for helping us build another unforgettable weekend of games.'
            ];

            function energizeText(text) {
                const trimmed = text.trim();
                const opener = energizedOpeners[Math.floor(Math.random() * energizedOpeners.length)];
                if (!trimmed) {
                    return opener;
                }

                let rewritten = trimmed
                    .replace(/get ready to\s*/gi, '')
                    .replace(/Southern-Fried Gaming Expo/gi, 'the event')
                    .replace(/thank you for submitting/gi, 'thanks for sending in')
                    .replace(/we simply could not host this incredible event without you/gi, 'your support keeps the games, lights, and excitement going')
                    .replace(/we hope (this email finds you well|you'?re doing well)!?/gi, opener)
                    .replace(/we['’]d love to invite you to participate again/gi, 'we would be excited to have you join us again')
                    .replace(/help make it our biggest and best event yet/gi, 'help fill the floor with more games and more reasons to play');

                if (rewritten === trimmed) {
                    rewritten = `${opener} ${rewritten}`;
                }

                return rewritten.replace(/\s{2,}/g, ' ');
            }

            editor.ui.registry.addButton('rewriteEnergize', {
                text: 'Rewrite & Energize',
                tooltip: 'Rewrite selected copy with more varied, upbeat wording',
                onAction: function() {
                    const selectedText = editor.selection.getContent({ format: 'text' });
                    const sourceText = selectedText || editor.getContent({ format: 'text' });
                    const rewrittenText = energizeText(sourceText);
                    if (selectedText) {
                        editor.selection.setContent(editor.dom.encode(rewrittenText));
                    } else {
                        editor.insertContent(editor.dom.encode(rewrittenText));
                    }
                }
            });

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