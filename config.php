<?php
if (!function_exists('load_env_file')) {
    function load_env_file($path) {
        if (!file_exists($path)) {
            return;
        }
        $content = file_get_contents($path);
        if ($content === false) {
            return;
        }
        $content = str_replace("\r", '', $content);
        $content = str_replace('\n', "\n", $content);
        $lines = explode("\n", $content);
        foreach ($lines as $line) {
            $line = trim($line);
            if ($line === '' || str_starts_with($line, '#') || strpos($line, '=') === false) {
                continue;
            }
            [$name, $value] = explode('=', $line, 2);
            $name = trim($name);
            $value = trim($value);
            if ($name === '') {
                continue;
            }
            $_ENV[$name] = $value;
            putenv("$name=$value");
        }
    }
}

if (!function_exists('env_value')) {
    function env_value($key, $default = null) {
        $value = $_ENV[$key] ?? getenv($key);
        if ($value === false || $value === null || $value === '') {
            return $default;
        }
        return $value;
    }
}

load_env_file(__DIR__ . '/.env');

if (!defined('CURRENT_YEAR')) define('CURRENT_YEAR', (int) env_value('CURRENT_YEAR', date('Y')));
if (!defined('PRIOR_YEAR')) define('PRIOR_YEAR', CURRENT_YEAR - 1);
if (!defined('STARTDATE')) define('STARTDATE', env_value('STARTDATE', '7/31/' . CURRENT_YEAR));
if (!defined('ENDDATE')) define('ENDDATE', env_value('ENDDATE', '8/02/' . CURRENT_YEAR));

if (!defined('db_host')) define('db_host', env_value('DB_SERVER', 'localhost'));
if (!defined('db_user')) define('db_user', env_value('DB_USER', ''));
if (!defined('db_pass')) define('db_pass', env_value('DB_PASS', ''));
if (!defined('db_name')) define('db_name', env_value('DB_NAME', ''));
if (!defined('db_charset')) define('db_charset','utf8');
/* Registration */
define('auto_login_after_register',true);
/* Account Activation */
// Email activation variables
// account activation required?
define('account_activation',true);
// Change "Your Company Name" and "yourdomain.com" - do not remove the < and > characters
define('mail_from','info@southernfriedgameroomexpo.com');
// Link to activation file
define('activation_link','https://gameroom.gameatl.com/activate.php');
define('autologin_link','https://gameroom.gameatl.com/autologin.php');
?>
