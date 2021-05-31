<?php
if (IS_LOGGED == false) {
    exit("You ain't logged in!");
}


if ($first == 'upload_video') {
   

    $id = Secure($_POST['to']); 
    if (!empty($_FILES['video']['tmp_name'])) {
    
          if ($_FILES['video']['size'] > $kd->config->max_image_upload_size) {
        $max  = size_format($kd->config->max_image_upload_size);
        $data = array('status' => 401,'message' => ($lang->file_is_too_big .": $max") );
        echo json_encode($data);
        exit();
    } else {
        $data = 
           array ('status' => 400, 'error' => 'empty file name'); 
        
    }

    $allowed           = 'mp4';


    $new_string        = pathinfo($_FILES['video']['name'], PATHINFO_FILENAME) . '.' . strtolower(pathinfo($_FILES['video']['name'], PATHINFO_EXTENSION));
    $extension_allowed = explode(',', $allowed);
    $file_extension    = pathinfo($new_string, PATHINFO_EXTENSION);
    if (!in_array($file_extension, $extension_allowed)) {
      
        $data = array('status' => 400, 'error' => 'file not supported');
        echo json_encode($data);
        exit();
    }

    ChatExists($id);
    $file_info = array(
        'file' => $_FILES['video']['tmp_name'],
        'size' => $_FILES['video']['size'],
        'name' => $_FILES['video']['name'],
        'type' => $_FILES['video']['type'],
        'allowed' => 'mp4'
    );
   
    $file_upload = ShareFile($file_info);

     if (!empty($file_upload['filename'])) {
                    $thumbnail = Secure($file_upload['filename'], 0);
                    $new_message = '[vd]'.$thumbnail.'[/vd]';
                    $insert_message = array(
                        'from_id' => $kd->user->id,
                        'to_id' => $id,
                        'text' => $new_message,
                        'time' => time()
                    );
                    $msg_exits = $db->where("from_id", $kd->user->id)->where("to_id", $id)->where("text", $new_message)->getValue(T_MESSAGES, 'count(*)');
                    if (empty($msg_exits)) {
                        $insert = $db->insert(T_MESSAGES, $insert_message);
                      
                        if ($insert) {
                            $kd->message = GetMessageData($insert);
                            $data = array(
                                'status' => 200,
                                'message_id' => secure($_POST['message_id']),
                                'message' => LoadPage('dashboard/pages/lists/sender', array(
                                    'ID' => $kd->message->id,
                                    'TEXT' => $kd->message->text
                                ))
                            );
                        }
                    }
                }

            else if (!empty($file_upload['error'])) {
                $data = array('status' => 400, 'error' => $file_upload['error']);
            }
    } else {
        $data = array('status' => 400, 'error' => 'failed to send  the video chck the video size is not more than 8mbs ');
    }

}
 if ($first == 'upload_record') {
         if (isset($_POST['audio-filename']) && isset($_FILES['audio-blob']['name'])) {
            $fileInfo       = array(
                'file' => $_FILES["audio-blob"]["tmp_name"],
                'name' => $_FILES['audio-blob']['name'],
                'size' => $_FILES["audio-blob"]["size"],
                'type' => $_FILES["audio-blob"]["type"],
                'allowed' => 'mp3,wav'
            );
       
            $media          = ShareFile($fileInfo);
            $data['status'] = 200;
            $data['url']    = $media['filename'];
            $data['name']   = $media['name'];
        }
        header("Content-type: application/json");
        echo json_encode($data);
        exit();
    }
if ($first == 'upload_media') {
    if ( !empty($_POST['to']) && !empty($_POST['message_id']) && !empty($_FILES['media']) ) {
        $id = Secure($_POST['to']);
        if ($id !== $kd->user->id) {

             ChatExists($id);
            if (!empty($_FILES['media']['tmp_name'])) {
                $file_info   = array(
                    'file' => $_FILES['media']['tmp_name'],
                    'size' => $_FILES['media']['size'],
                    'name' => $_FILES['media']['name'],
                    'type' => $_FILES['media']['type'],
                    'crop' => array(
                        'width' => 600,
                        'height' => 600
                    )
                );
             
                $file_upload = ShareFile($file_info);
                if (!empty($file_upload['filename'])) {
                    $thumbnail = Secure($file_upload['filename'], 0);
                    $new_message = '[img]'.$thumbnail.'[/img]';
                    $insert_message = array(
                        'from_id' => $kd->user->id,
                        'to_id' => $id,
                        'text' => $new_message,                       
                        'time' => time()
                    );
                   
                    $msg_exits = $db->where("from_id", $kd->user->id)->where("to_id", $id)->where("text", $new_message)->getValue(T_MESSAGES, 'count(*)');
                    if (empty($msg_exits)) {
                        $insert = $db->insert(T_MESSAGES, $insert_message);
                        if ($insert) {
                            $kd->message = GetMessageData($insert);
                            $data = array(
                                'status' => 200,
                                'message_id' => $_POST['message_id'],
                                'message' => LoadPage('dashboard/pages/lists/sender', array(
                                    'ID' => $kd->message->id,
                                    'TEXT' => $kd->message->text
                                ))
                            );
                        }
                    }
                }
            }else{
                $data = array(
                    'status' => 400,
                    'message' => 'error',
                    'm' => $_FILES['media']['tmp_name']
                );
            }

        }

    }
}
if ($first == 'new') {

    if (($_POST['id']) == 0) {
        exit();
    }
    if (!empty($_POST['id'])) {
         $mediaFilename = '';
         $mediaName     = '';
         $mime_types = explode(',', str_replace(' ', '', $kd->config->mime_types . ',application/json,application/octet-stream'));

        $link_regex = '/(http\:\/\/|https\:\/\/|www\.)([^\ ]+)/i';
        $i          = 0;
        preg_match_all($link_regex, Secure($_POST['new-message']), $matches);
        foreach ($matches[0] as $match) {
            $match_url           = strip_tags($match);
            $syntax              = '[a]' . urlencode($match_url) . '[/a]';
            $_POST['new-message'] = str_replace($match, $syntax, $_POST['new-message']);
        }
        if (isset($_FILES['sendMessageFile']['name'])) {
            if ($_FILES['sendMessageFile']['size'] >  $kd->config->max_image_upload_size) {
                   $data = array('status' => 400,'message' => ($lang->file_is_too_big .": $max") );
                header("Content-type: application/json");
                echo json_encode($data);
                 exit();
            } else {
                $fileInfo      = array(
                    'file' => $_FILES["sendMessageFile"]["tmp_name"],
                    'name' => $_FILES['sendMessageFile']['name'],
                    'size' => $_FILES["sendMessageFile"]["size"],
                    'type' => $_FILES["sendMessageFile"]["type"],
                    'allowed' => 'text/plain'


                );
                
                $media         = ShareFile($fileInfo);
              
                $mediaFilename = $media['filename'];
                $mediaName     = $media['name'];
            }
        } else if
         (!empty($_POST['record_file']) && !empty($_POST['record_name'])) {
                        $mediaFilename = $_POST['record_file'];
                        $mediaName     = $_POST['record_name'];
                    }

        if (empty($_POST['new-message'])  && empty($mediaFilename)) {
                        exit();
                    }
        $new_message = Secure($_POST['new-message']);
        $id = Secure($_POST['id']);
        if ($id != $kd->user->id) {
   
            ChatExists($id);
            $insert_message = array(
                'from_id' => $kd->user->id,
                'to_id' => $id,
                'text' => $new_message,
                'media' => Secure($mediaFilename),
                'mediaFileName' => Secure($mediaName), 
                'time' => time()
            );
             $insert = $db->insert(T_MESSAGES, $insert_message);
            if ($insert) {
                $kd->message = GetMessageData($insert);
                $data = array(
                    'status' => 200,
                    'message_id' => $_POST['message_id'],
                    'message' => LoadPage('dashboard/pages/lists/sender', array(
                        'ID' => $kd->message->id,
                        'TEXT' => $kd->message->text
                    ))
                );
            }
        }
    }
}

if ($first == 'fetch') {
    if (empty($_POST['last_id'])) {
        $_POST['last_id'] = 0;
    }
    if (empty($_POST['id'])) {
        $_POST['id'] = 0;
    }
    if (empty($_POST['first_id'])) {
        $_POST['first_id'] = 0;
    }
    $messages_html = GetMessages($_POST['id'], array('last_id' => $_POST['last_id'], 'first_id' => $_POST['first_id'], 'return_method' => 'html'));
    if (!empty($messages_html)) {
        $html = LoadPage("dashboard/pages/lists/messages", array('MESSAGES' => $messages_html));
    } else {
        $html = LoadPage("dashboard/pages/lists/no_messages");
    }

    $users_html = GetMessagesUserList(array('return_method' => 'html'));

    if (!empty($messages_html) || !empty($users_html)) {
        $data = array('status' => 200, 'message' => $messages_html, 'users' => $users_html);
    }
}

if ($first == 'search') {
    $keyword = '';
    $users_html = '<p class="text-center">{{LANG No Users Found}}</p>';
    if (isset($_POST['keyword'])) {
        $users_html = GetMessagesUserList(array('return_method' => 'html', 'keyword' => $_POST['keyword']));
    }
    $data = array('status' => 200, 'users' => $users_html);
}

if ($first == 'delete_chat') {
    if (!empty($_POST['id'])) {
        $id = Secure($_POST['id']);
        $messages = $db->where("(from_id = {$kd->user->id} AND to_id = {$id}) OR (from_id = {$id} AND to_id = {$kd->user->id})")->get(T_MESSAGES);
        $update1 = array();
        $update2 = array();
        $erase = array();
        $images = array();
        foreach ($messages as $key => $message) {
            if ($message->from_deleted == 1 || $message->to_deleted == 1) {
                $erase[] = $message->id;

                $img = $message->text;
                if( substr($img, 0, 5) == '[img]' && substr($img, -6) == '[/img]'){
                    $img = str_replace(array('[img]','[/img]'), '', $img);
                    if( @file_exists( $img ) ){
                        $images[] = $img;
                    }
                }

            } else {
                if ($message->to_id == $kd->user->id) {
                    $update2[] = $message->id;
                } else {
                    $update1[] = $message->id;
                }
            }
        }
        if (!empty($erase)) {
            $erase = implode(',', $erase);
            $final_query = "DELETE FROM " . T_MESSAGES . " WHERE id IN ($erase)";
            $db->rawQuery($final_query);
            if(!empty($images)){
                foreach ($images as $image){
                    @unlink($image);
                }
            }
        }
        if (!empty($update1)) {
            $update1 = implode(',', $update1);
            $final_query = "UPDATE " . T_MESSAGES . " set `from_deleted` = '1' WHERE `id` IN({$update1}) ";
            $db->rawQuery($final_query);
        }
        if (!empty($update2)) {
            $update2 = implode(',', $update2);
            $final_query = "UPDATE " . T_MESSAGES . " set `to_deleted` = '1' WHERE `id` IN({$update2}) ";
            $db->rawQuery($final_query);
        }
        $delete_chats = $db->rawQuery("DELETE FROM " . T_CHATS . " WHERE user_one = {$kd->user->id} AND user_two = $id");
    }
}
    if ($first == 'delete_message') {

  
            $message_id = Secure($_POST['message_id']);
            $message = $db->where('id',$message_id)->getOne(T_MESSAGES);
            
            if (!empty($message_id) || is_numeric($message_id) || $message_id > 0) {
      
                if (DeleteMessage($message_id) === true) {
                  
                    if (!empty($message)) {
                        $user_id = $message->to_id;
                        if ($message->to_id == $kd->user->id) {
                            $user_id = $message->from_id;
                        }
                        $recipient    = UserData($user_id);
                        $data = array(
                            'status' => 200




                        );
                       // $data['messages_count'] = CountMessages(array('new' => false,'user_id' => $user_id));
                       // $data['posts_count'] = $recipient['details']['post_count'];
                    }
                    
                }
            }
        
        header("Content-type: application/json");
        echo json_encode($data);
        exit();
    }
     if ($first == 'get_last_message_seen_status') {
        if (isset($_GET['last_id'])) {
            $message_id = Secure($_GET['last_id']);
            if (!empty($message_id) || is_numeric($message_id) || $message_id > 0) {
                $seen = SeenMessage($message_id);
                if ($seen > 0) {
                    $data = array(
                        'status' => 200,
                        'time' => $seen['time'],
                        'seen' => $seen['seen']
                    );
                }
            }
        }
        header("Content-type: application/json");
        echo json_encode($data);
        exit();
    }
?>