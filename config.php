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

if (!defined('db_host')) define('db_host', env_value('DB_SERVER', 'localhost'));
if (!defined('db_user')) define('db_user', env_value('DB_USER', ''));
if (!defined('db_pass')) define('db_pass', env_value('DB_PASS', ''));
if (!defined('db_name')) define('db_name', env_value('DB_NAME', ''));
if (!defined('db_charset')) define('db_charset','utf8');

if (!function_exists('load_settings_from_db')) {
    function load_settings_from_db() {
        static $settings = null;
        if ($settings !== null) {
            return $settings;
        }
        $settings = [];
        if (!defined('db_host') || !defined('db_user') || !defined('db_name')) {
            return $settings;
        }

        try {
            $mysqli = @new mysqli(db_host, db_user, db_pass, db_name);
            if ($mysqli->connect_errno) {
                return $settings;
            }

            $table_exists_result = $mysqli->query("SHOW TABLES LIKE 'settings'");
            if (!$table_exists_result || $table_exists_result->num_rows === 0) {
                $mysqli->close();
                return $settings;
            }

            $result = $mysqli->query('SELECT setting_key, setting_value FROM settings');
            if ($result) {
                while ($row = $result->fetch_assoc()) {
                    $settings[$row['setting_key']] = $row['setting_value'];
                }
            }

            $mysqli->close();
        } catch (Throwable $e) {
            return [];
        }

        return $settings;
    }
}

if (!function_exists('app_setting')) {
    function app_setting($key, $default = null) {
        $settings = load_settings_from_db();
        if (array_key_exists($key, $settings) && $settings[$key] !== null && $settings[$key] !== '') {
            return $settings[$key];
        }
        return env_value($key, $default);
    }
}

if (!defined('CURRENT_YEAR')) define('CURRENT_YEAR', (int) app_setting('CURRENT_YEAR', date('Y')));
if (!defined('PRIOR_YEAR')) define('PRIOR_YEAR', (int) app_setting('PRIOR_YEAR', CURRENT_YEAR - 1));
if (!defined('STARTDATE')) define('STARTDATE', app_setting('STARTDATE', '7/31/' . CURRENT_YEAR));
if (!defined('ENDDATE')) define('ENDDATE', app_setting('ENDDATE', '8/02/' . CURRENT_YEAR));

/* Registration */
define('auto_login_after_register', filter_var(app_setting('AUTO_LOGIN_AFTER_REGISTER', 'true'), FILTER_VALIDATE_BOOLEAN));
/* Account Activation */
// Email activation variables
// account activation required?
define('account_activation', filter_var(app_setting('ACCOUNT_ACTIVATION', 'true'), FILTER_VALIDATE_BOOLEAN));
// Change "Your Company Name" and "yourdomain.com" - do not remove the < and > characters
define('mail_from', app_setting('MAIL_FROM', 'info@southernfriedgameroomexpo.com'));
// Link to activation file
define('activation_link', app_setting('ACTIVATION_LINK', 'https://gameroom.gameatl.com/activate.php'));
define('autologin_link', app_setting('AUTOLOGIN_LINK', 'https://gameroom.gameatl.com/autologin.php'));
?>
