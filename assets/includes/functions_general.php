<?php
function lang($string = '') {
    global $lang_array, $db;
    $string = trim($string);
    $stringFromArray = strtolower(preg_replace('/[^a-zA-Z0-9-_\.]/','_', $string));
    if (in_array($stringFromArray, array_keys($lang_array))) {
        return __($stringFromArray); //$lang_array[$stringFromArray];
    }
    $insert = $db->insert(T_LANGS, ['lang_key' => $stringFromArray, 'english' => secure($string)]);
    $lang_array[$stringFromArray] = $string;
    return $string;
}
function LoadPage($page_url = '', $data = array(), $set_lang = true) {
    global $kd, $lang_array, $config, $fl_currpage, $countries_name;
    $page = './themes/' . $config['theme'] . '/layout/' . $page_url . '.html';
    if (!file_exists($page)) {
        die("File not Exists : $page");
    }
    $page_content = '';
    ob_start();
    require($page);
    $page_content = ob_get_contents();
    ob_end_clean();
    if ($set_lang == true) {
        $page_content = preg_replace_callback("/{{LANG (.*?)}}/", function($m) use ($lang_array) {
            return lang($m[1]);
        }, $page_content);
    }
    if (!empty($data) && is_array($data)) {
        foreach ($data as $key => $replace) {
            if ($key == 'USER_DATA') {
                $replace = ToArray($replace);
                $page_content = preg_replace_callback("/{{USER (.*?)}}/", function($m) use ($replace) {
                    return (isset($replace[$m[1]])) ? $replace[$m[1]] : '';
                }, $page_content);
            } else {
                if( is_array($replace) || is_object($replace) ){
                    $arr = explode('_',$key);
                    $k = strtoupper($arr[0]);
                    $replace = ToArray($replace);
                    $page_content = preg_replace_callback("/{{".$k." (.*?)}}/", function($m) use ($replace) {
                        return (isset($replace[$m[1]])) ? $replace[$m[1]] : '';
                    }, $page_content);
                }else{
                    $object_to_replace = "{{" . $key . "}}";
                    $page_content      = str_replace($object_to_replace, $replace, $page_content);
                }
            }
        }
    }
    if (IS_LOGGED == true) {
        $replace = ToArray($kd->user);
        $page_content = preg_replace_callback("/{{ME (.*?)}}/", function($m) use ($replace) {
            return (isset($replace[$m[1]])) ? $replace[$m[1]] : '';
        }, $page_content);
    }
    $page_content = preg_replace("/{{LINK (.*?)}}/", UrlLink("$1"), $page_content);
    $page_content = preg_replace_callback("/{{CONFIG (.*?)}}/", function($m) use ($config) {
        return (isset($config[$m[1]])) ? $config[$m[1]] : '';
    }, $page_content);
    return $page_content;
}
function ToObject($array) {
    $object = new stdClass();
    foreach ($array as $key => $value) {
        if (is_array($value)) {
            $value = ToObject($value);
        }
        if (isset($value)) {
           if(!empty($value)){
            $object->$key = $value;
            }
        }
    }
    return $object;
}

function ToArray($obj) {
    if (is_object($obj))
        $obj = (array) $obj;
    if (is_array($obj)) {
        $new = array();
        foreach ($obj as $key => $val) {
            $new[$key] = ToArray($val);
        }
    } else {
        $new = $obj;
    }
    return $new;
}
function UrlLink($string) {
    global $site_url;
    return rtrim($site_url ,'/') . str_replace('//','/','/' . $string);
}
function db_langs() {
    global $db;
    $data   = array();
    $t_lang = T_LANGS;
    try {
        $query  = $db->rawQuery("DESCRIBE `$t_lang`");
    } catch (Exception $e) {

    }
    foreach ($query as $column) {
        $data[] = $column->Field;
    }
    unset($data[0]);
    unset($data[1]);
    return $data;
}
function Secure($string, $censored_words = 1, $br = true) {
    global $mysqli;
    if(!is_array($string)) {

        $string = trim($string);
        $string = mysqli_real_escape_string($mysqli, $string);
        $string = htmlspecialchars($string, ENT_QUOTES);

        if ($br == true) {
            $string = str_replace('\r\n', " <br>", $string);
            $string = str_replace('\n\r', " <br>", $string);
            $string = str_replace('\r', " <br>", $string);
            $string = str_replace('\n', " <br>", $string);
        } else {
            $string = str_replace('\r\n', "", $string);
            $string = str_replace('\n\r', "", $string);
            $string = str_replace('\r', "", $string);
            $string = str_replace('\n', "", $string);
        }
        $string = stripslashes($string);
        $string = str_replace('&amp;#', '&#', $string);
        $string = preg_replace("/{{(.*?)}}/", '', $string);

    }
    return $string;
}
function GetUserFromSessionID($session_id, $platform = 'web') {
    global $db;
    if (empty($session_id)) {
        return false;
    }
    $platform   = Secure($platform);
    $session_id = Secure($session_id);
    $return     = $db->where('session_id', $session_id);
    $return     = $db->where('platform', $platform);
    return $db->getValue(T_SESSIONS, 'user_id');
}
function IsLogged() {
   if (isset($_SESSION['user_id']) && !empty($_SESSION['user_id'])) {
        $id = GetUserFromSessionID($_SESSION['user_id']);
        if (is_numeric($id) && !empty($id)) {
            return true;
        }
    }
    else if (!empty($_COOKIE['user_id']) && !empty($_COOKIE['user_id'])) {
        $id = GetUserFromSessionID($_COOKIE['user_id']);
        if (is_numeric($id) && !empty($id)) {
            return true;
        }
    }
    else {
        return false;
    }
}
function verify_api_auth($user_id,$session_id, $platform = 'phone') {
    global $db;
    if (empty($session_id) || empty($user_id)) {
        return false;
    }
    $platform   = Secure($platform);
    $session_id = Secure($session_id);
    $user_id    = Secure($user_id);

    $db->where('session_id', $session_id);
    $db->where('user_id', $user_id);
    $db->where('platform', $platform);
    return ($db->getValue(T_SESSIONS, 'COUNT(*)') == 1);
}
function get_langs($lang = 'english') {
    global $db;
    $data   = array();
    $t_lang = T_LANGS;
    try {
        $query  = $db->rawQuery("SELECT `lang_key`, `$lang` FROM `$t_lang`");
        foreach ($query as $item) {
            $data[$item->lang_key] = $item->$lang;
        }
    } catch (Exception $e) {}
    return $data;
}
function CheckIfUserCanRegister($num = 10) {
    global $kd, $sqlConnect;
    if (IS_LOGGED == true) {
        return false;
    }
    $ip = get_ip_address();
    if (empty($ip)) {
        return true;
    }
    $time      = time() - 3200;
    $query     = mysqli_query($sqlConnect, "SELECT COUNT(`id`) as count FROM " . T_USERS . " WHERE `ip_address` = '{$ip}' AND `last_active` > {$time}");
    $sql_query = mysqli_fetch_assoc($query);
    if ($sql_query['count'] > $num) {
        return false;
    }
}

function UsernameExists($username = '') {
    global $db;
    return ($db->where('username', Secure($username))->getValue(T_USERS, 'count(*)') > 0) ? true : false;
}
function UserEmailExists($email = '') {
    global $db;
    return ($db->where('email', Secure($email))->getValue(T_USERS, 'count(*)') > 0) ? true : false;
}
function get_ip_address() {
    if (!empty($_SERVER['HTTP_CLIENT_IP']) && filter_var($_SERVER['HTTP_CLIENT_IP'], FILTER_VALIDATE_IP)) {
        return $_SERVER['HTTP_CLIENT_IP'];
    }
    if (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
        if (strpos($_SERVER['HTTP_X_FORWARDED_FOR'], ',') !== false) {
            $iplist = explode(',', $_SERVER['HTTP_X_FORWARDED_FOR']);
            foreach ($iplist as $ip) {
                if (filter_var($ip, FILTER_VALIDATE_IP))
                    return $ip;
            }
        } else {
            if (filter_var($_SERVER['HTTP_X_FORWARDED_FOR'], FILTER_VALIDATE_IP))
                return $_SERVER['HTTP_X_FORWARDED_FOR'];
        }
    }
    if (!empty($_SERVER['HTTP_X_FORWARDED']) && filter_var($_SERVER['HTTP_X_FORWARDED'], FILTER_VALIDATE_IP))
        return $_SERVER['HTTP_X_FORWARDED'];
    if (!empty($_SERVER['HTTP_X_CLUSTER_CLIENT_IP']) && filter_var($_SERVER['HTTP_X_CLUSTER_CLIENT_IP'], FILTER_VALIDATE_IP))
        return $_SERVER['HTTP_X_CLUSTER_CLIENT_IP'];
    if (!empty($_SERVER['HTTP_FORWARDED_FOR']) && filter_var($_SERVER['HTTP_FORWARDED_FOR'], FILTER_VALIDATE_IP))
        return $_SERVER['HTTP_FORWARDED_FOR'];
    if (!empty($_SERVER['HTTP_FORWARDED']) && filter_var($_SERVER['HTTP_FORWARDED'], FILTER_VALIDATE_IP))
        return $_SERVER['HTTP_FORWARDED'];
    return $_SERVER['REMOTE_ADDR'];
}
function GenerateKey($minlength = 20, $maxlength = 20, $uselower = true, $useupper = true, $usenumbers = true, $usespecial = false) {
    $charset = '';
    if ($uselower) {
        $charset .= "abcdefghijklmnopqrstuvwxyz";
    }
    if ($useupper) {
        $charset .= "ABCDEFGHIJKLMNOPQRSTUVWXYZ";
    }
    if ($usenumbers) {
        $charset .= "123456789";
    }
    if ($usespecial) {
        $charset .= "~@#$%^*()_+-={}|][";
    }
    if ($minlength > $maxlength) {
        $length = mt_rand($maxlength, $minlength);
    } else {
        $length = mt_rand($minlength, $maxlength);
    }
    $key = '';
    for ($i = 0; $i < $length; $i++) {
        $key .= $charset[(mt_rand(0, strlen($charset) - 1))];
    }
    return $key;
}
function GetBrowser() {
    $u_agent = $_SERVER['HTTP_USER_AGENT'];
    $bname = 'Unknown';
    $platform = 'Unknown';
    $version= "";
    // First get the platform?
    if (preg_match('/macintosh|mac os x/i', $u_agent)) {
        $platform = 'mac';
    } elseif (preg_match('/windows|win32/i', $u_agent)) {
        $platform = 'windows';
    } elseif (preg_match('/iphone|IPhone/i', $u_agent)) {
        $platform = 'IPhone Web';
    } elseif (preg_match('/android|Android/i', $u_agent)) {
        $platform = 'Android Web';
    } else if (preg_match("/(android|avantgo|blackberry|bolt|boost|cricket|docomo|fone|hiptop|mini|mobi|palm|phone|pie|tablet|up\.browser|up\.link|webos|wos)/i", $u_agent)) {
        $platform = 'Mobile';
    } else if (preg_match('/linux/i', $u_agent)) {
        $platform = 'linux';
    }
    // Next get the name of the useragent yes seperately and for good reason
    if(preg_match('/MSIE/i',$u_agent) && !preg_match('/Opera/i',$u_agent)) {
        $bname = 'Internet Explorer';
        $ub = "MSIE";
    } elseif(preg_match('/Firefox/i',$u_agent)) {
        $bname = 'Mozilla Firefox';
        $ub = "Firefox";
    } elseif(preg_match('/Chrome/i',$u_agent)) {
        $bname = 'Google Chrome';
        $ub = "Chrome";
    } elseif(preg_match('/Safari/i',$u_agent)) {
        $bname = 'Apple Safari';
        $ub = "Safari";
    } elseif(preg_match('/Opera/i',$u_agent)) {
        $bname = 'Opera';
        $ub = "Opera";
    } elseif(preg_match('/Netscape/i',$u_agent)) {
        $bname = 'Netscape';
        $ub = "Netscape";
    }
    // finally get the correct version number
    $known = array('Version', $ub, 'other');
    $pattern = '#(?<browser>' . join('|', $known) . ')[/ ]+(?<version>[0-9.|a-zA-Z.]*)#';
    if (!preg_match_all($pattern, $u_agent, $matches)) {
        // we have no matching number just continue
    }
    // see how many we have
    $i = count($matches['browser']);
    if ($i != 1) {
        //we will have two since we are not using 'other' argument yet
        //see if version is before or after the name
        if (strripos($u_agent,"Version") < strripos($u_agent,$ub)){
            $version= $matches['version'][0];
        } else {
            $version= $matches['version'][1];
        }
    } else {
        $version= $matches['version'][0];
    }
    // check if we have a number
    if ($version==null || $version=="") {$version="?";}
    return array(
        'userAgent' => $u_agent,
        'name'      => $bname,
        'version'   => $version,
        'platform'  => $platform,
        'pattern'    => $pattern,
        'ip_address' => get_ip_address()
    );
}
function getPageFromPath($path = '') {
    if (empty($path)) {
        return false;
    }
    $path = explode("/", $path);
    $data = array();
    $data['link1'] = array();
    if (!empty($path[0])) {
        $data['page'] = $path[0];
    }
    if (!empty($path[1])) {
        unset($path[0]);
        $data['link1'] = $path;
    }
    return $data;
}
function CreateSession() {
    $hash = sha1(rand(1111, 9999));
    if (!empty($_SESSION['hash'])) {
        $_SESSION['hash'] = $_SESSION['hash'];
        return $_SESSION['hash'];
    }
    $_SESSION['hash'] = $hash;
    return $hash;
}
function GetThemes() {
    global $kd;
    $themes = glob('themes/*', GLOB_ONLYDIR);
    return $themes;
}
function createUserSession($user_id = 0,$platform = 'web') {
    global $db,$sqlConnect, $kd;
    if (empty($user_id)) {
        return false;
    }
    $session_id          = sha1(rand(11111, 99999)) . time() . md5(microtime() . $user_id);
    $insert_data         = array(
        'user_id' => $user_id,
        'session_id' => $session_id,
        'platform' => $platform,
        'time' => time()
    );

    $insert              = $db->insert(T_SESSIONS, $insert_data);
   
    $_SESSION['user_id'] = $session_id;
    setcookie("user_id", $session_id, time() + (10 * 365 * 24 * 60 * 60), "/");
    $kd->loggedin = true;

  //  $query_two = mysqli_query($sqlConnect, "DELETE FROM " . T_APP_SESSIONS . " WHERE `session_id` = '{$session_id}'");
    // if ($query_two) {
    //     $ua = serialize(GetBrowser());
    //     $delete_same_session = $db->where('user_id', $user_id)->where('platform_details', $ua)->delete(T_APP_SESSIONS);
    //     $query_three = mysqli_query($sqlConnect, "INSERT INTO " . T_APP_SESSIONS . " (`user_id`, `session_id`, `platform`, `platform_details`, `time`) VALUES('{$user_id}', '{$session_id}', 'web', '$ua'," . time() . ")");
    //     if ($query_three) {
    //         return $session_id;
    //     }
    // }
}
function br2nl($st) {
    $breaks = array(
        "<br />",
        "<br>",
        "<br/>"
    );
    return str_ireplace($breaks, "\r\n", $st);
}
function Resize_Crop_Image($max_width, $max_height, $source_file, $dst_dir, $quality = 80) {
    $imgsize = @getimagesize($source_file);
    $width   = $imgsize[0];
    $height  = $imgsize[1];
    $mime    = $imgsize['mime'];
    switch ($mime) {
        case 'image/gif':
            $image_create = "imagecreatefromgif";
            $image        = "imagegif";
            break;
        case 'image/png':
            $image_create = "imagecreatefrompng";
            $image        = "imagepng";
            break;
        case 'image/jpeg':
            $image_create = "imagecreatefromjpeg";
            $image        = "imagejpeg";
            break;
        default:
            return false;
            break;
    }
    $dst_img    = @imagecreatetruecolor($max_width, $max_height);
    $src_img    = $image_create($source_file);
    $width_new  = $height * $max_width / $max_height;
    $height_new = $width * $max_height / $max_width;
    if ($width_new > $width) {
        $h_point = (($height - $height_new) / 2);
        @imagecopyresampled($dst_img, $src_img, 0, 0, 0, $h_point, $max_width, $max_height, $width, $height_new);
    } else {
        $w_point = (($width - $width_new) / 2);
        @imagecopyresampled($dst_img, $src_img, 0, 0, $w_point, 0, $max_width, $max_height, $width_new, $height);
    }
    @$image($dst_img, $dst_dir, $quality);


    if ($dst_img)
        @imagedestroy($dst_img);
    if ($src_img)
        @imagedestroy($src_img);
}
function CompressImage($source_url, $destination_url, $quality) {
    $info = getimagesize($source_url);
    if ($info['mime'] == 'image/jpeg') {
        $image = @imagecreatefromjpeg($source_url);
        @imagejpeg($image, $destination_url, $quality);
    } elseif ($info['mime'] == 'image/gif') {
        $image = @imagecreatefromgif($source_url);
        @imagegif($image, $destination_url, $quality);
    } elseif ($info['mime'] == 'image/png') {
        $image = @imagecreatefrompng($source_url);
        @imagepng($image, $destination_url);
    }
}
function random_str(
    int $length = 64,
    string $keyspace = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ'
): string {
    if ($length < 1) {
        throw new \RangeException("Length must be a positive integer");
    }
    $pieces = [];
    $max = mb_strlen($keyspace, '8bit') - 1;
    for ($i = 0; $i < $length; ++$i) {
        $pieces []= $keyspace[random_int(0, $max)];
    }
    return implode('', $pieces);
}
function LoadAdminLinkSettings($link = '') {
    global $site_url;
    return $site_url . '/admin-cp/' . $link;
}
function LoadAdminLink($link = '') {
    global $site_url;
    return $site_url . '/admin-panel/' . $link;
}
function LoadAdminPage($page_url = '', $data = array(), $set_lang = true) {
    global $kd, $lang_array, $config, $db;
    $page = './admin-panel/pages/' . $page_url . '.html';
    if (!file_exists($page)) {
        return false;
    }
    $page_content = '';
    ob_start();
    require($page);
    $page_content = ob_get_contents();
    ob_end_clean();
    if ($set_lang == true) {
        $page_content = preg_replace_callback("/{{LANG (.*?)}}/", function($m) use ($lang_array) {
            return (isset($lang_array[$m[1]])) ? $lang_array[$m[1]] : '';
        }, $page_content);
    }
    if (!empty($data) && is_array($data)) {
        foreach ($data as $key => $replace) {
            if ($key == 'USER_DATA') {
                $replace = ToArray($replace);
                $page_content = preg_replace_callback("/{{USER (.*?)}}/", function($m) use ($replace) {
                    return (isset($replace[$m[1]])) ? $replace[$m[1]] : '';
                }, $page_content);
            } else {
                if( is_array($replace) || is_object($replace) ){
                    $arr = explode('_',$key);
                    $k = strtoupper($arr[0]);
                    $replace = ToArray($replace);
                    $page_content = preg_replace_callback("/{{".$k." (.*?)}}/", function($m) use ($replace) {
                        return (isset($replace[$m[1]])) ? $replace[$m[1]] : '';
                    }, $page_content);
                }else{
                    $object_to_replace = "{{" . $key . "}}";
                    $page_content      = str_replace($object_to_replace, $replace, $page_content);
                }
            }
        }
    }
    if (IS_LOGGED == true) {
        $replace = ToArray($kd->user);
        $page_content = preg_replace_callback("/{{ME (.*?)}}/", function($m) use ($replace) {
            return (isset($replace[$m[1]])) ? $replace[$m[1]] : '';
        }, $page_content);
    }
    $page_content = preg_replace("/{{LINK (.*?)}}/", UrlLink("$1"), $page_content);
    $page_content = preg_replace_callback("/{{CONFIG (.*?)}}/", function($m) use ($config) {
        return (isset($config[$m[1]])) ? $config[$m[1]] : '';
    }, $page_content);
    return $page_content;
}
function ChatExists($id) {
    global $db, $kd;

  if (!empty($id)){
     $chat_exits = $db->where("user_one", $kd->user->id)->where("user_two", $id)->getValue(T_CHATS, 'count(*)');
            if (!empty($chat_exits)) {
                $db->where("user_two", $kd->user->id)->where("user_one", $id)->update(T_CHATS, array('time' => time()));
                $db->where("user_one", $kd->user->id)->where("user_two", $id)->update(T_CHATS, array('time' => time()));
                if ($db->where("user_two", $kd->user->id)->where("user_one", $id)->getValue(T_CHATS, 'count(*)') == 0) {
                    $db->insert(T_CHATS, array('user_two' => $kd->user->id, 'user_one' => $id,'time' => time()));
                }
            } else {
                $db->insert(T_CHATS, array('user_one' => $kd->user->id, 'user_two' => $id,'time' => time()));
                if (empty($db->where("user_two", $kd->user->id)->where("user_one", $id)->getValue(T_CHATS, 'count(*)'))) {
                    $db->insert(T_CHATS, array('user_two' => $kd->user->id, 'user_one' => $id,'time' => time()));
                }
            }

        }

            return $chat_exits;
  
}