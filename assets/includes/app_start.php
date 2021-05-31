<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', true);
error_reporting(0);
@ini_set('max_execution_time', 0);

require 'config.php';
require 'phpMailer_config.php';
require 'assets/import/DB/vendor/autoload.php';
//require 'assets/import/getID3-master/getid3/getid3.php';
require 'assets/import/s3/aws-autoloader.php';
require 'assets/import/ftp/vendor/autoload.php';
require 'assets/import/imagethumbnail.php';


$kd     = ToObject(array());

$kd->directory_separator = DIRECTORY_SEPARATOR;
$kd->base_path = realpath(dirname(dirname(dirname(__FILE__)))) . DIRECTORY_SEPARATOR;

// Connect to MySQL Server
$mysqli     = new mysqli($sql_db_host, $sql_db_user, $sql_db_pass, $sql_db_name);
$sqlConnect = $mysqli;

$kd->script_version = '1.0.0.0';

// Handling Server Errors
$ServerErrors = array();
if (mysqli_connect_errno()) {
    $ServerErrors[] = "Failed to connect to MySQL: " . mysqli_connect_error();
}
if (!function_exists('curl_init')) {
    $ServerErrors[] = "PHP CURL is NOT installed on your web server !";
}
if (!extension_loaded('gd') && !function_exists('gd_info')) {
    $ServerErrors[] = "PHP GD library is NOT installed on your web server !";
}
if (!extension_loaded('zip')) {
    $ServerErrors[] = "ZipArchive extension is NOT installed on your web server !";
}

if (isset($ServerErrors) && !empty($ServerErrors)) {
    foreach ($ServerErrors as $Error) {
        echo "<h3>" . $Error . "</h3>";
    }
    die();
}
$query = $mysqli->query("SET NAMES utf8mb4");
// Connecting to DB after verfication

$db = new MysqliDb($mysqli);

$http_header = 'http://';
if (!empty($_SERVER['HTTPS'])) {
    $http_header = 'https://';
}

$kd->site_pages          = array('home');
$kd->actual_link         = $http_header . $_SERVER['HTTP_HOST'] . urlencode($_SERVER['REQUEST_URI']);
$config                   = GetConfig();
$kd->loggedin            = false;
// $config['user_statics']   = stripslashes(htmlspecialchars_decode($config['user_statics']));
// $config['questions_statics'] = stripslashes(htmlspecialchars_decode($config['questions_statics']));
$config['theme_url']      = $site_url . '/themes/' . $config['theme'];
$config['site_url']       = $site_url;
$config['script_version'] = $kd->script_version;
$kd->extra_config = array();
$config['hostname'] = '';
$config['server_port'] = '';
$site = parse_url($site_url);
if (empty($site['host'])) {
    $config['hostname'] = $site['scheme'] . '://' .  $site['host'];
}
$kd->config              = ToObject($config);
$langs                    = db_langs();
$kd->langs               = $langs;
if (IsLogged() == true) {
    $session_id        = (!empty($_SESSION['user_id'])) ? $_SESSION['user_id'] : $_COOKIE['user_id'];
    $kd->user_session  = GetUserFromSessionID($session_id);
    $user = $kd->user  = UserData($kd->user_session);

    //$user->wallet      = number_format($user->wallet,2);

    if (!empty($user->language) && in_array($user->language, $langs)) {
        $_SESSION['lang'] = $user->language;
    }

    if ($user->id < 0 || empty($user->id) || !is_numeric($user->id) || UserActive($user->id) === false) {
        header("Location: " . UrlLink('logout'));
    }
    $kd->loggedin   = true;
}
else if (!empty($_POST['user_id']) && !empty($_POST['s'])) {
    $platform       = ((!empty($_POST['platform'])) ? $_POST['platform'] : 'phone');
    $s              = Secure($_POST['s']);
    $user_id        = Secure($_POST['user_id']);
    $verify_session = verify_api_auth($user_id, $s, $platform);
    if ($verify_session === true) {
        $user = $kd->user  = UserData($user_id);
        if (empty($user) || UserActive($user->id) === false) {
            $json_error_data = array(
                'api_status' => '400',
                'api_text' => 'authentication_failed',
                'errors' => array(
                    'error_id' => '1',
                    'error_text' => 'Error 400 - The user does not exist'
                )
            );
            echo json_encode($json_error_data, JSON_PRETTY_PRINT);
            exit();
        }
        $kd->loggedin = true;
    }
    else {
        $json_error_data = array(
            'api_status' => '400',
            'api_text' => 'authentication_failed',
            'errors' => array(
                'error_id' => '1',
                'error_text' => 'Error 400 - Session does not exist'
            )
        );
        echo json_encode($json_error_data, JSON_PRETTY_PRINT);
        exit();
    }
}
else if (!empty($_GET['user_id']) && !empty($_GET['s'])) {
    $platform       = ((!empty($_GET['platform'])) ? $_GET['platform'] : 'phone');
    $s              = Secure($_GET['s']);
    $user_id        = Secure($_GET['user_id']);
    $verify_session = verify_api_auth($user_id, $s, $platform);
    if ($verify_session === true) {
        $user = $kd->user  = UserData($user_id);
        if (empty($user) || UserActive($user->id) === false) {
            $json_error_data = array(
                'api_status' => '400',
                'api_text' => 'authentication_failed',
                'errors' => array(
                    'error_id' => '1',
                    'error_text' => 'Error 400 - The user does not exist'
                )
            );

            echo json_encode($json_error_data, JSON_PRETTY_PRINT);
            exit();
        }

        $kd->loggedin = true;
    }
    else {
        $json_error_data = array(
            'api_status' => '400',
            'api_text' => 'authentication_failed',
            'errors' => array(
                'error_id' => '1',
                'error_text' => 'Error 400 - Session does not exist'
            )
        );
        echo json_encode($json_error_data, JSON_PRETTY_PRINT);
        exit();
    }
}
elseif (!empty($_GET['cookie']) && $kd->loggedin != true) {
    $session_id            = $_GET['cookie'];
    $kd->user_session     = GetUserFromSessionID($session_id);
    if (!empty($kd->user_session) && is_numeric($kd->user_session)) {
        $user = $kd->user = UserData($kd->user_session);
        $kd->loggedin     = true;

        if (!empty($user->language)) {
            if (file_exists(__DIR__ . '/../langs/' . $user->language . '.php')) {
                $_SESSION['lang'] = $user->language;
            }
        }
        setcookie("user_id", $session_id, time() + (10 * 365 * 24 * 60 * 60), "/");
    }
}

if (isset($_GET['lang']) AND !empty($_GET['lang'])) {
    $lang_name = Secure(strtolower($_GET['lang']));
    if (in_array($lang_name, $langs)) {
        $_SESSION['lang'] = $lang_name;
        if ($kd->loggedin == true) {
            $db->where('id', $user->id)->update(T_USERS, array('language' => $lang_name));
        }
    }
}
if (empty($_SESSION['lang'])) {
    $_SESSION['lang'] = $kd->config->language;
}

if (isset($_SESSION['user_id'])) {
    if (empty($_COOKIE['user_id'])) {
        setcookie("user_id", $_SESSION['user_id'], time() + (10 * 365 * 24 * 60 * 60), "/");
    }
}

$kd->language      = $_SESSION['lang'];
$kd->language_type = 'ltr';

// Add rtl languages here.
$rtl_langs           = array(
    'arabic'
);

// checking if corrent language is rtl.
foreach ($rtl_langs as $lang) {
    if ($kd->language == strtolower($lang)) {
        $kd->language_type = 'rtl';
    }
}

// Include Language File
$lang_file = 'assets/langs/' . $kd->language . '.php';
if (file_exists($lang_file)) {
    require($lang_file);
}

$lang_array = get_langs($kd->language);

if (empty($lang_array)) {
    $lang_array = get_langs();
}

$lang       = ToObject($lang_array);

$kd->lang    = $lang;

$kd->default_lang    = ToObject(get_langs());

$kd->exp_feed    = false;
$kd->userDefaultAvatar = 'upload/photos/d-avatar.jpg';
$kd->categories  = ToObject($categories);

$error_icon   = '<i class="fa fa-exclamation-circle"></i> ';
$success_icon = '<i class="fa fa-check"></i> ';
define('IS_LOGGED', $kd->loggedin);
define('none', null);

$kd->continents = array('Asia','Australia','Africa','Europe','America','Atlantic','Pacific','Indian');
require 'context_data.php';