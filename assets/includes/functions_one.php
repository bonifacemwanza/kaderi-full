<?php
require_once('app_start.php');
use Aws\S3\S3Client;

function __($key){
    global $kd;
    if(property_exists($kd->lang,$key)){
        return $kd->lang->$key;
    }else{
        return $kd->default_lang->$key;
    }
}
function GetConfig() {
    global $db;
    $data  = array();
    $configs = $db->get(T_CONFIG);
    foreach ($configs as $key => $config) {
        $data[$config->name] = $config->value;
    }
    return $data;
}
function UserData($user_id = 0, $options = array()) {
    global $db, $kd, $lang, $countries_name;

    if (!empty($options['data'])) {
        $fetched_data   = $user_id;
    }

    else {
        $fetched_data   = $db->where('id', $user_id)->getOne(T_USERS);
    }

    if (empty($fetched_data)) {
        return false;
    }

    $fetched_data->name   = $fetched_data->username;
    $fetched_data->avatar_path = $fetched_data->avatar;
    $fetched_data->cover_path  = $fetched_data->cover;
    $fetched_data->avatar = GetMedia($fetched_data->avatar);
    $fetched_data->cover  = GetMedia($fetched_data->cover);
    $fetched_data->url    = UrlLink('@' . $fetched_data->username);
    $fetched_data->about_decoded = br2nl($fetched_data->about);

    if (!empty($fetched_data->first_name)) {
        $fetched_data->name = $fetched_data->first_name . ' ' . $fetched_data->last_name;
    }

    if (empty($fetched_data->about)) {
        $fetched_data->about = '';
    }
    //$fetched_data->balance  = number_format($fetched_data->balance, 2);
    $fetched_data->name_v   = $fetched_data->name;
    if ($fetched_data->verified == 1 ) {
        $fetched_data->name_v = $fetched_data->name . ' <i class="fa fa-check-circle fa-fw verified"></i>';
    }

   if (!empty($countries_name[$fetched_data->country_id])) {
    $fetched_data->country_name  = $countries_name[$fetched_data->country_id];
   }
     @$fetched_data->gender_text  = ($fetched_data->gender == 'male') ? __('male') : __('female');
    return $fetched_data;

}
function UserActive($user_id = 0) {
    global $db;
    $db->where('active', '1');
    $db->where('id', Secure($user_id));
    return ($db->getValue(T_USERS, 'count(*)') > 0) ? true : false;
}
function CreateMainSession() {
    $hash = substr(sha1(rand(1111, 9999)), 0, 70);
    if (!empty($_SESSION['main_hash_id'])) {
        $_SESSION['main_hash_id'] = $_SESSION['main_hash_id'];
        return $_SESSION['main_hash_id'];
    }
    $_SESSION['main_hash_id'] = $hash;
    return $hash;
}

function CheckMainSession($hash = '') {
    if (!isset($_SESSION['main_hash_id']) || empty($_SESSION['main_hash_id'])) {
        return false;
    }
    if (empty($hash)) {
        return false;
    }
    if ($hash == $_SESSION['main_hash_id']) {
        return true;
    }
    return false;
}
function GetMedia($media = '', $is_upload = false){
    global $kd;
     if (empty($media)) {
        return '';
    }

    $media_url     = $kd->config->site_url . '/' . $media;
    if ($kd->config->s3_upload == 'on' && $is_upload == false) {
        $media_url = "https://" . $kd->config->s3_bucket_name . ".s3.amazonaws.com/" . $media;
    } else if ($kd->config->ftp_upload == "on") {
        return addhttp($kd->config->ftp_endpoint) . '/' . $media;
    }

    return $media_url;

    
}
function IsAdmin() {
    global $kd;
    if (IS_LOGGED == false) {
        return false;
    }
    if ($kd->user->admin == 1) {
        return true;
    }
    return false;
}
function UploadToS3($filename, $config = array()) {
    global $kd;

    if ($kd->config->s3_upload != 'on' && $kd->config->ftp_upload != 'on') {
        return false;
    }
    if ($kd->config->ftp_upload == "on") {
        $ftp = new \FtpClient\FtpClient();
        $ftp->connect($kd->config->ftp_host, false, $kd->config->ftp_port);
        $login = $ftp->login($kd->config->ftp_username, $kd->config->ftp_password);
        if ($login) {
            if (!empty($kd->config->ftp_path)) {
                if ($kd->config->ftp_path != "./") {
                    $ftp->chdir($kd->config->ftp_path);
                }
            }
            $file_path = substr($filename, 0, strrpos( $filename, '/'));
            $file_path_info = explode('/', $file_path);
            $path = '';
            if (!$ftp->isDir($file_path)) {
                foreach ($file_path_info as $key => $value) {
                    if (!empty($path)) {
                        $path .= '/' . $value . '/' ;
                    } else {
                        $path .= $value . '/' ;
                    }
                    if (!$ftp->isDir($path)) {
                        $mkdir = $ftp->mkdir($path);
                    }
                }
            }
            $ftp->chdir($file_path);
            $ftp->pasv(true);
            if ($ftp->putFromPath($filename)) {
                if (empty($config['delete'])) {
                    if (empty($config['amazon'])) {
                        @unlink($filename);
                    }
                }
                $ftp->close();
                return true;
            }
            $ftp->close();
        }
    } else {
        $s3Config = (
            empty($kd->config->amazone_s3_key) ||
            empty($kd->config->amazone_s3_s_key) ||
            empty($kd->config->region) ||
            empty($kd->config->s3_bucket_name)
        );

        if ($s3Config){
            return false;
        }

        $s3 = new S3Client([
            'version'     => 'latest',
            'region'      => $kd->config->region,
            'credentials' => [
                'key'    => $kd->config->amazone_s3_key,
                'secret' => $kd->config->amazone_s3_s_key,
            ]
        ]);

        $s3->putObject([
            'Bucket' => $kd->config->s3_bucket_name,
            'Key'    => $filename,
            'Body'   => fopen($filename, 'r+'),
            'ACL'    => 'public-read',
        ]);

        if (empty($config['delete'])) {
            if ($s3->doesObjectExist($kd->config->s3_bucket_name, $filename)) {
                if (empty($config['amazon'])) {
                    @unlink($filename);
                }
                return true;
            }
        }

        else {
            return true;
        }
    }
}
function ShareFile($data = array(), $type = 0) {
    global $kd, $mysqli;
    $allowed = '';
    if (!file_exists('upload/files/' . date('Y'))) {
        @mkdir('upload/files/' . date('Y'), 0777, true);
    }
    if (!file_exists('upload/files/' . date('Y') . '/' . date('m'))) {
        @mkdir('upload/files/' . date('Y') . '/' . date('m'), 0777, true);
    }
    if (!file_exists('upload/photos/' . date('Y'))) {
        @mkdir('upload/photos/' . date('Y'), 0777, true);
    }
    if (!file_exists('upload/photos/' . date('Y') . '/' . date('m'))) {
        @mkdir('upload/photos/' . date('Y') . '/' . date('m'), 0777, true);
    }
     if (!file_exists('upload/sounds/' . date('Y'))) {
        @mkdir('upload/sounds/' . date('Y'), 0777, true);
    }
    if (!file_exists('upload/sounds/' . date('Y') . '/' . date('m'))) {
        @mkdir('upload/sounds/' . date('Y') . '/' . date('m'), 0777, true);
    }
    if (isset($data['file']) && !empty($data['file'])) {
        $data['file'] = $data['file'];
    }
    if (isset($data['name']) && !empty($data['name'])) {
        $data['name'] = Secure($data['name']);
    }
    if (isset($data['name']) && !empty($data['name'])) {
        $data['name'] = Secure($data['name']);
    }
    if (empty($data)) {
        return false;
    }
     if ($kd->config->fileSharing == 'on') {
        if (isset($data['types'])) {
            $allowed = $data['types'];
        } else {
            $allowed = $kd->config->allowedExtenstion;
        }
    } else {
        $allowed = 'jpg,png,jpeg,gif,mp4,m4v,webm,flv,mov,mpeg,mp3,wav';
    }

    $new_string        = pathinfo($data['name'], PATHINFO_FILENAME) . '.' . strtolower(pathinfo($data['name'], PATHINFO_EXTENSION));
    $extension_allowed = explode(',', $allowed);
    $file_extension    = pathinfo($new_string, PATHINFO_EXTENSION);
    if (!in_array($file_extension, $extension_allowed)) {
        return array(
            'error' => 'File format not supported'
        );
    }
   if ($file_extension == 'jpg' || $file_extension == 'jpeg' || $file_extension == 'png' || $file_extension == 'gif') {
        $folder   = 'photos';
        $fileType = 'image';
    } 
    else if ($file_extension == 'mp3' || $file_extension == 'wav') {
            $folder   = 'sounds';
            $fileType = 'soundFile';
        } 
    else if($file_extension == 'mp4' || $file_extension == 'mov' || $file_extension == 'webm' || $file_extension == 'flv'){
        $folder   = 'videos';
        $fileType = 'video';
    } else {
        $folder   = 'files';
        $fileType = 'file';
    }

   if (empty($folder) || empty($fileType)) {
        return false;
    }
    $mime_types = explode(',', str_replace(' ', '', $kd->config->mime_types . ',application/json,application/octet-stream'));


    if (!in_array($data['type'], $mime_types)) {
        return array(
            'error' => 'File format not supported'
        );
    }

    $dir         = "upload/{$folder}/" . date('Y') . '/' . date('m');
    $filename    = $dir . '/' . GenerateKey() . '_' . date('d') . '_' . md5(time()) . "_{$fileType}.{$file_extension}";
    $second_file = pathinfo($filename, PATHINFO_EXTENSION);
    if (move_uploaded_file($data['file'], $filename)) {

        if(isset($data['mode'])) {
            if ($data['mode'] == 'avatar') {

                $thumbfile = str_replace('_image', '_avatar', $filename);
                $thumbnail = new ImageThumbnail($filename);
                $thumbnail->setSize(200, 200);
                $thumbnail->save($thumbfile);
                //@unlink($filename);
                if (is_file($thumbfile)) {
                    UploadToS3($thumbfile, array(
                        'amazon' => 0
                    ));
                }

                $filename = $thumbfile;
            }
        }else{

            if ($second_file == 'jpg' || $second_file == 'jpeg' || $second_file == 'png' || $second_file == 'gif') {
                if ($type == 1) {
                    @CompressImage($filename, $filename, 50);
                    $explode2 = @end(explode('.', $filename));
                    $explode3 = @explode('.', $filename);
                    $last_file = $explode3[0] . '_small.' . $explode2;
                    @Resize_Crop_Image(400, 400, $filename, $last_file, 60);

                    if (($kd->config->s3_upload == 'on' || $kd->config->ftp_upload == 'on') && !empty($last_file)) {
                        $upload_s3 = UploadToS3($last_file);
                    }
                } else {
                    @CompressImage($filename, $filename, 50);

                    if (($kd->config->s3_upload == 'on' || $kd->config->ftp_upload == 'on') && !empty($filename)) {
                        $upload_s3 = UploadToS3($filename);
                    }
                }
            } else {
                if (($kd->config->s3_upload == 'on' || $kd->config->ftp_upload == 'on') && !empty($filename)) {
                    $upload_s3 = UploadToS3($filename);
                }
            }

        }

        $last_data             = array();
        $last_data['filename'] = $filename;
        $last_data['name']     = $data['name'];
        return $last_data;
    }
}
function IsModerator($user_id = '') {
    global $kd, $sqlConnect;
    if (IS_LOGGED == false) {
        return false;
    }
    if ($kd->user->admin == 2) {
        return true;
    }
    return false;
}
function IsJobPostOwner($job_id = 0, $user_id = 0) {
    global $sqlConnect, $kd;
    if (empty($user_id)) {
        $user_id = $kd->user->id;
    }
    $user_id = Secure($user_id);
    $job_id  = Secure($job_id);
    $result  = false;
    $query   = mysqli_query($sqlConnect, "SELECT * FROM
            " . T_JOBS . " WHERE `id` = '$job_id'");
    if (!empty($query)) {
        $fetched_data = mysqli_fetch_assoc($query);
        if ($fetched_data['user_id'] == $kd->user->id || $kd->user->admin == 1 || IsModerator() == true) {
            $result = true;
        }
    }
    return $result;
}
function GetUserJobs($id) {
    global $sqlConnect;
    if (IS_LOGGED == false || !$id || !is_numeric($id) || $id < 1) {
        return false;
    }

    $table        = T_JOBS;
    $query        = mysqli_query($sqlConnect, "SELECT * FROM  `$table` WHERE `id` = '$id' ");
    $fetched_data = mysqli_fetch_assoc($query);
    $data         = false;

    if (!empty($fetched_data)) {
        $fetched_data['user_data']   = UserData($fetched_data['user_id']);
        $fetched_data['is_owner']    = IsJobPostOwner($fetched_data['id']);
 

        $data                        = $fetched_data;
    }

    return $data;
}
function GetMyJobs($args = array()) {
    global $sqlConnect, $kd, $job_availability, $job_category, $salary;
    if (IS_LOGGED == false) {
        return false;
    }
    $options   = array(
        "id" => false,
        "offset" => 0
    );
    $args      = array_merge($options, $args);
    $offset    = Secure($args['offset']);
    $id        = Secure($args['id']);
    $user_id   = $kd->user->id;
    $query_one = '';
    $data      = array();
    if ($offset > 0) {
        $query_one .= " AND `id` < {$offset} AND `id` <> {$offset} ";
    }
    if ($id && $id > 0 && is_numeric($id)) {
        $query_one .= " AND `id` = '$id' ";
    }
    $sql   = "SELECT `id` FROM  
                " . T_JOBS . " 
                    WHERE `user_id` = '$user_id' 
                        {$query_one} ORDER BY `id` 
                            DESC LIMIT 10";
    $query = mysqli_query($sqlConnect, $sql);

    while ($fetched_data = mysqli_fetch_assoc($query)) {
        $ad = GetUserJobs($fetched_data['id']);
        if ($ad && !empty($ad)) {
            $ad['name']     = GetShortTitle($ad['name']);
            $ad['edit-url'] = UrlLink('index.php?link1=edit-ads&id=' . $ad['id']);
            $ad['chart-url'] = UrlLink('index.php?link1=cdashboard/jobs&id=' . $ad['id']);
            $data[]         = $ad;
        }
    }

    return $data;
}
function GetShortTitle($text = false, $preview = false, $len = 40) {
    if (!$text) {
        return false;
    }
    if (strlen($text) > $len && !$preview) {
        $text = mb_substr($text, 0, $len, "UTF-8") . "..";
    }
    return $text;
}
function GetUsersTotalPostedJobs($id){
    global $kd,$db;
    $data = $db->where('user_id',$id)->getOne(T_JOBS,array('count(*) as total'));
    return $data->total;
}
function DeleteUserJob($id = false) {
    global $sqlConnect,$kd;
    if (IS_LOGGED == false || !$id || !IsJobPostOwner($id)) {
        return false;
    }
    $ad = GetUserJobs($id);

    // $ad_media_path = $kd->base_path . str_replace('/' , $kd->directory_separator ,  $ad['ad_media_path'] );

    // @unlink($ad_media_path);
    // if (($kd->config->s3_upload == 'on' || $kd->config->ftp_upload == 'on')) {
    //     DeleteFromToS3($ad['ad_media']);
    // }
    $query     = false;
    $query     .= mysqli_query($sqlConnect, "DELETE FROM " . T_JOBS . "  WHERE `id` = {$id} ");
    $query     .= mysqli_query($sqlConnect, "DELETE FROM " . T_JOB_DATA . "  WHERE `job_id` = {$id} ");
    return $query;
}
function GetBookIdWithUniqid($uniqid) {
  global $sqlConnect;
    if (empty($uniqid)) {
        return false;
    }
    $uniqid = Secure($uniqid);
    $query    = mysqli_query($sqlConnect, "SELECT `id` FROM " . T_BOOK . " WHERE `uniqid` = '{$uniqid}'");
    return Sql_Result($query, 0, 'id');

}
function GetLessonIdWithUniqid($uniqid) {
  global $sqlConnect;
    if (empty($uniqid)) {
        return false;
    }
    $uniqid = Secure($uniqid);
    $query    = mysqli_query($sqlConnect, "SELECT `id` FROM " . T_LESSONS . " WHERE `lesson_uniqid` = '{$uniqid}'");
    return Sql_Result($query, 0, 'id');

}
function Sql_Result($res, $row = 0, $col = 0) {
    $numrows = mysqli_num_rows($res);
    if ($numrows && $row <= ($numrows - 1) && $row >= 0) {
        mysqli_data_seek($res, $row);
        $resrow = (is_numeric($col)) ? mysqli_fetch_row($res) : mysqli_fetch_assoc($res);
        if (isset($resrow[$col])) {
            return $resrow[$col];
        }
    }
    return false;
}
function GetRelatedJobCategory($id) {
  global $sqlConnect;
    if (empty($id)) {
        return false;
    }
    $id = Secure($id);
    $query    = mysqli_query($sqlConnect, "SELECT `category` FROM " . T_JOBS . " WHERE `id` = '{$id}'");
    return Sql_Result($query, 0, 'category');

}
function GetJobPosterUserId($id) {
  global $sqlConnect;
    if (empty($id)) {
        return false;
    }
    $id = Secure($id);
    $query    = mysqli_query($sqlConnect, "SELECT `user_id` FROM " . T_JOBS . " WHERE `id` = '{$id}'");
    return Sql_Result($query, 0, 'user_id');

}
function GetBookById($id){
    global $kd,$db;
   $query =  $db->where('book_number', $id)->getOne(T_BOOK);
   
 
    return $query;
}
function GetBookTotalLessons($id){
    global $kd, $db;
    $lessons_count = $db->where('book_id',$id)->get(T_LESSONS);
    return count($lessons_count);
}
function GetBookProgress($user_id, $book_id) {
    global $kd, $db;
    $get_progress = $db->where('user_id', $user_id)->where('book_number', $book_id)->get(T_QUIZ_DATA);

    return count($get_progress);
}
function GetMessagesUserList($data = array('offset' => 0 ,'limit' => 20 , 'api' => false)) {
    global $kd, $db;
    if (IS_LOGGED == false) {
        return false;
    }
    $limit = 20;

    // if($data['api'] === true) {
    //     $limit = (int)$data['limit'];
    //     $offset = (int)$data['offset'];
    // }

    $db->where("user_one", $kd->user->id);

    if (isset($data['keyword'])) {
        $keyword = Secure($data['keyword']);
        $db->where("user_two IN (SELECT id FROM users WHERE username LIKE '%$keyword%' OR `first_name` LIKE '%$keyword%')");
    }

    if (isset($data['offset']) && $data['api'] === true) {
        $db->where("id > " . $offset);
    }

    $users = $db->orderBy('time', 'DESC')->get(T_CHATS, $limit);

    $return_methods = array('obj', 'html');

    $return_method = 'obj';
    if (!empty($data['return_method'])) {
        if (in_array($data['return_method'], $return_methods)) {
            $return_method = $data['return_method'];
        }
    }

    $users_html = '';
    $data_array = array();
    foreach ($users as $key => $user) {
        $user = UserData($user->user_two);
        if (!empty($user)) {
            $get_last_message = $db->where("((from_id = {$kd->user->id} AND to_id = $user->id AND `from_deleted` = '0') OR (from_id = $user->id AND to_id = {$kd->user->id} AND `to_deleted` = '0'))")->orderBy('id', 'DESC')->getOne(T_MESSAGES);
            $get_count_seen = $db->where("to_id = {$kd->user->id} AND from_id = $user->id AND `from_deleted` = '0' AND seen = 0")->orderBy('id', 'DESC')->getValue(T_MESSAGES, 'COUNT(*)');
            if ($return_method == 'html') {
                $users_html .= LoadPage("dashboard/pages/lists/choose_receiver", array(
                    'ID' => $user->id,
                    'AVATAR' => $user->avatar,
                    'NAME' => $user->name,
                    'LAST_MESSAGE' => (!empty($get_last_message->text)) ? markUp( strip_tags($get_last_message->text) ) : '',
                    'COUNT' => (!empty($get_count_seen)) ? $get_count_seen : '',
                    'USERNAME' => $user->username,
                    'LOGGED_USER' => $kd->user->username
                ));
            } else {
                if ($data['api'] === false) {
                    $data_array[$key]['user'] = $user;
                    $data_array[$key]['get_count_seen'] = $get_count_seen;
                    $data_array[$key]['get_last_message'] = $get_last_message;
                }else{
                    $data_array[] = array(
                        'user' => $user,
                        'get_count_seen' => $get_count_seen,
                        'get_last_message' => $get_last_message
                    );
                }
            }
        }
    }
    $users_obj = (!empty($data_array)) ? ToObject($data_array) : array();
    return (!empty($users_html)) ? $users_html : $users_obj;
}

function GetMessageData($id = 0) {
    global $kd, $db;
    if (empty($id) || !IS_LOGGED) {
        return false;
    }
    $fetched_data = $db->where('id', Secure($id))->getOne(T_MESSAGES);
    if (!empty($fetched_data)) {
        $fetched_data->text = Markup($fetched_data->text);
        return $fetched_data;
    }
    return false;
}
function DeleteMessage($message_id, $media = '', $deleter_id = 0) {
    global $kd, $sqlConnect,$db;
    if (empty($deleter_id)) {
        if (IS_LOGGED == false) {
            return false;
        }
    }
    if (empty($message_id) || !is_numeric($message_id) || $message_id < 0) {
        return false;
    }
    $user_id = $deleter_id;
    if (empty($user_id) && IS_LOGGED == true) {
        $user_id = $kd->user->id;
    }
    $message_id    = Secure($message_id);
    $query_one     = "SELECT * FROM " . T_MESSAGES . " WHERE `id` = {$message_id}";

    $sql_query_one = mysqli_query($sqlConnect, $query_one);

    if (mysqli_num_rows($sql_query_one) == 1) {
        $sql_fetch_one = mysqli_fetch_assoc($sql_query_one);
           if ($sql_fetch_one['to_id'] != $user_id && $sql_fetch_one['from_id'] != $user_id) {
            return false;
        }
            $query = mysqli_query($sqlConnect, "DELETE FROM " . T_MESSAGES . " WHERE `id` = {$message_id}");
          
            if ($query) {
                if (isset($sql_fetch_one['media']) AND !empty($sql_fetch_one['media'])) {
                    @unlink($sql_fetch_one['media']);
                    $delete_from_s3 = DeleteFromToS3($sql_fetch_one['media']);
                }
                return true;
            } else {
                return false;
            }
       
        return false;
    }
}
function GetMessages($id, $data = array(),$limit = 50) {
    global $kd, $db;
    if (IS_LOGGED == false) {
        return false;
    }

    $chat_id = Secure($id);

    if (!empty($data['chat_user'])) {
        $chat_user = $data['chat_user'];
    } else {
        $chat_user = UserData($chat_id);
    }


    $where = "((`from_id` = {$chat_id} AND `to_id` = {$kd->user->id} AND `to_deleted` = '0') OR (`from_id` = {$kd->user->id} AND `to_id` = {$chat_id} AND `from_deleted` = '0'))";

    // count messages
    $db->where($where);
    if (!empty($data['last_id'])) {
        $data['last_id'] = Secure($data['last_id']);
        $db->where('id', $data['last_id'], '>');
    }

    if (!empty($data['first_id'])) {
        $data['first_id'] = Secure($data['first_id']);
        $db->where('id', $data['first_id'], '<');
    }

    $count_user_messages = $db->getValue(T_MESSAGES, "count(*)");
    $count_user_messages = $count_user_messages - $limit;
    if ($count_user_messages < 1) {
        $count_user_messages = 0;
    }

    // get messages
    $db->where($where);
    if (!empty($data['last_id'])) {
        $db->where('id', $data['last_id'], '>');
    }

    if (!empty($data['first_id'])) {
        $db->where('id', $data['first_id'], '<');
    }

    $get_user_messages = $db->orderBy('id', 'ASC')->get(T_MESSAGES, array($count_user_messages, $limit));

    $messages_html = '';

    $return_methods = array('obj', 'html');

    $return_method = 'obj';
    if (!empty($data['return_method'])) {
        if (in_array($data['return_method'], $return_methods)) {
            $return_method = $data['return_method'];
        }
    }
 
    $update_seen = array();
   
      
    $last_file_view = '';
          
    foreach ($get_user_messages as $key => $message) {
         

    

           $filename    = (!empty( $message->media)) ? GetMedia( $message->media) : '';
           $fname       = Secure($message->mediaFileName);
     
         if (isset($filename) && !empty($filename)) {
            $file_extension = pathinfo($filename, PATHINFO_EXTENSION);
            $file           = '';
            $media_file     = '';
            $icon_size                  = 'fa-2x';
            $start_link     = "<a href=" . $filename . ">";
            $end_link       = '</a>';
            $file_extension = strtolower($file_extension);
          

            if ($file_extension == 'jpg' || $file_extension == 'jpeg' || $file_extension == 'png' || $file_extension == 'gif') {
                $media_file .= "<a href='" . $filename  . "' target='_blank'><img src='" .$filename . "' alt='image' class='image-file pointer'></a>";
                
            }
            if ($file_extension == 'pdf') {
                $file .= '<i class="fa ' . $icon_size . ' fa-file-pdf-o"></i> ' . $fname; 
            }
            if ($file_extension == 'txt') {
                $file .= '<i class="fa ' . $icon_size . ' fa-file-text-o"></i> ' . $fname; 
            }
            if ($file_extension == 'zip' || $file_extension == 'rar' || $file_extension == 'tar') {
                $file .= '<i class="fa ' . $icon_size . ' fa-file-archive-o"></i> ' . $fname; 
            }
            if ($file_extension == 'doc' || $file_extension == 'docx') {
                $file .= '<i class="fa ' . $icon_size . ' fa-file-word-o"></i> ' . $fname;
            }
            if ($file_extension == 'mp3' || $file_extension == 'wav') {
               
                    $media_file .= LoadPage('dashboard/pages/lists/audio',array('MEDIASOUND' =>$filename));
                
            }
            if (empty($file)) {
                $file .= '<i class="fa ' . $icon_size . ' fa-file-o"></i> ' . $fname;
            } 
            if ($file_extension == 'mp4' || $file_extension == 'mkv' || $file_extension == 'avi' || $file_extension == 'webm' || $file_extension == 'mov') {
                $media_file .= LoadPage('dashboard/pages/lists/video',array('MEDIAVIDEO' =>$filename));;
            }  
  
                if (isset($media_file) && !empty($media_file)) {
                $last_file_view = $media_file;
            } else {
                $last_file_view = $start_link . $file . $end_link;
            }
           //return (!empty($last_file_view)) ? $last_file_view ;
        }else{
             $last_file_view = '';
        }
       
        

                  
     if ($return_method == 'html') {
                $message_type = 'receiver';
                $messgefromsender = 'notAplicable';
                if ($message->from_id == $kd->user->id) {
                    $message_type = 'sender';
                   

                }
               
                $messages_html .= LoadPage("dashboard/pages/lists/$message_type", array(
                'ID' => $message->id,
                'AVATAR' => $chat_user->avatar,
                'NAME' => $chat_user->name,
                'TIME' =>  Time_Elapsed_String($message->time),
                'TEXT' => Markup($message->text),
                'MEDIA'=> (!empty($last_file_view)) ? $last_file_view : ''
                
              
                
                

            ));

        }
        if ($message->seen == 0 && $message->to_id == $kd->user->id) {
            $update_seen[] = $message->id;
        }
    }

    if (!empty($update_seen)) {
        $update_seen = implode(',', $update_seen);
        $update_seen = $db->where("id IN ($update_seen)")->update(T_MESSAGES, array('seen' => time()));
    }

    return (!empty($messages_html)) ? $messages_html : $get_user_messages;
}
function SeenMessage($message_id) {
    global $sqlConnect,$db,$kd;
    $message_id   = Secure($message_id);
    $query        = mysqli_query($sqlConnect, " SELECT `seen` FROM " . T_MESSAGES . " WHERE `id` = {$message_id}");
    $fetched_data = mysqli_fetch_assoc($query);
    if ($fetched_data['seen'] > 0) {
        $data         = array();
        $data['time'] = date('c', $fetched_data['seen']);
        $data['seen'] = Time_Elapsed_String($fetched_data['seen']);
        return $data;
    } else {
        return false;
    }
}
function GetMessageButton($username = '') {
    global $kd, $db, $lang;
    if (empty($username)) {
        return false;
    }
    if (IS_LOGGED == false) {
        return false;
    }

    $button_text  = $lang->message;

    return LoadPage('buttons/message', array(
        'TEXT' => $button_text,
        'USERNAME' => $username,
    ));
}
function Markup($text, $link = true) {
    if ($link == true) {
        $link_search = '/\[a\](.*?)\[\/a\]/i';
        if (preg_match_all($link_search, $text, $matches)) {
            foreach ($matches[1] as $match) {
                $match_decode     = urldecode($match);
                $match_decode_url = $match_decode;
                $count_url        = mb_strlen($match_decode);
                if ($count_url > 50) {
                    $match_decode_url = mb_substr($match_decode_url, 0, 30) . '....' . mb_substr($match_decode_url, 30, 20);
                }
                $match_url = $match_decode;
                if (!preg_match("/http(|s)\:\/\//", $match_decode)) {
                    $match_url = 'http://' . $match_url;
                }
                $text = str_replace('[a]' . $match . '[/a]', '<a href="' . strip_tags($match_url) . '" target="_blank" class="hash" rel="nofollow">' . $match_decode_url . '</a>', $text);
            }
        }
    }
    $link_search = '/\[img\](.*?)\[\/img\]/i';
    if (preg_match_all($link_search, $text, $matches)) {
        foreach ($matches[1] as $match) {
            $match_decode     = urldecode($match);
            $text = str_replace('[img]' . $match . '[/img]', '<a href="' . getMedia(strip_tags($match_decode)) . '" target="_blank"><img style="width:300px;border-radius: 20px;" src="' . getMedia(strip_tags($match_decode)) . '"></a>', $text);
        }
    }

    $link_search = '/\[vd\](.*?)\[\/vd\]/i';

    if (preg_match_all($link_search, $text, $matches)){
        foreach ($matches[1] as $match) {
            $match_decode = urldecode($match);
            $text         = str_replace('[vd]'.$match. '[/vd]', '<video controls width="250"><source src="'. GetMedia(strip_tags($match_decode)) .'"type="video/mp4"/></video>', $text);
            // $text         = str_replace('[vd]'.$match. '[/vd]', '<video controls width="250"><source src="'. getMedia(strip_tags($match_decode)) .'"type="video/mov"/></video>', $text);
             
        }
    }


    return $text;
}
function Time_Elapsed_String($ptime) {
    global $kd, $lang;
    $etime = time() - $ptime;
    if ($etime < 1) {
        return '0 seconds';
    }
    $a        = array(
        365 * 24 * 60 * 60 => __('year'),
        30 * 24 * 60 * 60 => __('month'),
        24 * 60 * 60 => __('day'),
        60 * 60 => __('hour'),
        60 => __('minute'),
        1 => __('second')
    );
    $a_plural = array(
        __('year') => __('years'),
        __('month') => __('months'),
        __('day') => __('days'),
        __('hour') => __('hours'),
        __('minute') => __('minutes'),
        __('second') => __('seconds')
    );
    foreach ($a as $secs => $str) {
        $d = $etime / $secs;
        if ($d >= 1) {
            $r = round($d);
            if ($kd->language_type == 'rtl') {
                $time_ago = __('time_ago') . ' ' . $r . ' ' . ($r > 1 ? $a_plural[$str] : $str);
            } else {
                $time_ago = $r . ' ' . ($r > 1 ? $a_plural[$str] : $str) . ' ' . __('time_ago');
            }
            return $time_ago;
        }
    }
}