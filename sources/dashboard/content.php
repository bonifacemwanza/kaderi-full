<?php

if (IS_LOGGED == false) {
    header("Location: " . UrlLink('login'));
    exit();
}
$user_id                = $kd->user->id;
$kd->is_admin          = IsAdmin();
$final_page = '';
if (isset($_GET['user']) && !empty($_GET['user']) && ($kd->is_admin === true)) {
    if (empty($db->where('username', Secure($_GET['user']))->getValue(T_USERS, 'count(*)'))) {
        header("Location: " . UrlLink(''));
        exit();
    }
    $user_id               = $db->where('username', Secure($_GET['user']))->getValue(T_USERS, 'id');
    $kd->is_settings_admin = true;
}
$kd->settings     = UserData($user_id);

$kd->isowner = false;

if (IS_LOGGED == true) {
    if ($kd->settings->id == $user->id) {
        $kd->isowner = true;
    }
}
$countries = '';
foreach ($countries_name as $key => $value) {
    $selected = ($key == $kd->settings->country_id) ? 'selected' : '';
    $countries .= '<option value="' . $key . '" ' . $selected . '>' . $value . '</option>';
}



$pages_array = [
	'dashboard',
    'profile',
    'messages',
    'lessons',
    'achievements',
    'complete_quiz',
    'bookstore',
    'reward_points',
    'admin',
   
  
];
$bookstore = '';
$posted_bookstore = $db->get(T_BOOK_STORE);
if(!empty($posted_bookstore)){
    foreach ($posted_bookstore as $key => $bvalue) {
         $bookstore .= LoadPage('dashboard/pages/lists/bookstore_list', array(
             'ID' => $bvalue->id,
         	'BOOK_TITLE' => $bvalue->book_name,
         	'BOOK_DESCRIPTION' => htmlspecialchars_decode($bvalue->book_description),
         	'BOOK_COVER' => GetMedia($bvalue->book_cover),
            'BOOK_AMOUNT' => $bvalue->price,
            'BOOK_POINTS' => $bvalue->points
          
         ));
    }
} else {
    $bookstore .= '<h1> No books Added</h1>';
}
$get_my_lesson = $db->where('user_id', $kd->user->id)->get(T_USER_LESSONS,5, array('lesson_id'));
$my_lesson_html = '';
$completed_quiz = '';
$user_lessons_total = 0;
$user_quiz_total = 0;

$achievement_html = '';
if(!empty($get_my_lesson)){
    foreach ($get_my_lesson as $valueQ) {
        $book_total_lessons = '';
        $get_book_progress = '';
        $progress_ratio = '';
        $value = GetBookById($valueQ->lesson_id);
        if($value){
            $book_total_lessons = GetBookTotalLessons($value->id);
            $get_book_progress = GetBookProgress($user_id, $value->id);
        }
        $user_lessons_total += $book_total_lessons;
        $user_quiz_total += $get_book_progress;
        $book_points = $db->where('user_id', $user_id)->where('book_number', $value->id)->get(T_QUIZ_DATA);
        if($book_points){
            $book_point_sum = 0;
            foreach($book_points as $b_points){
                  $book_point_sum += $b_points->score; 
            }
        }
       
        $progress_ratio = ''.$get_book_progress .'/'. $book_total_lessons;
        
     $completed_quiz .= LoadPage('dashboard/pages/completed_quiz_list', array(
        'BOOK_TITLE' => $value->book_title,
        'BOOK_COVER' => GetMedia($value->book_cover),
        'BOOK_UNIQID' =>$value->uniqid,
        'BOOK_PROGRESS' => ($book_total_lessons =! $get_book_progress)? '<span class="card-target-undone">'. $progress_ratio .'</span>' : '<span class="card-target">'. $progress_ratio .'</span>', 
        'BOOK_STATUS' => ($book_total_lessons == $get_book_progress)?  '<span class="card-target">'. __('completed') .'</span>' : '<span class="card-target-undone">'. __('pending') .'</span>'
     ));
     $achievement_html .= LoadPage('dashboard/pages/lists/achievement_list', array(
        'BOOK_TITLE' => $value->book_title,
        'BOOK_COVER' => GetMedia($value->book_cover),
        'BOOK_UNIQID' =>$value->uniqid,
        'BOOK_PROGRESS' => ($book_total_lessons =! $get_book_progress)? '<span class="card-target-undone">'. $progress_ratio .'</span>' : '<span class="card-target">'. $progress_ratio .'</span>', 
        'BOOK_STATUS' => ($book_total_lessons == $get_book_progress)?  '<span class="card-target">'. __('completed') .'</span>' : '<span class="card-target-undone">'. __('pending') .'</span>',
        'BOOK_POINT' => $book_point_sum
     ));

     $my_lesson_html .= LoadPage('dashboard/pages/lesson_part', array(
        'BOOK_TITLE' => $value->book_title,
        'BOOK_DESCRIPTION' => htmlspecialchars_decode($value->book_description),
        'BOOK_COVER' => GetMedia($value->book_cover),
        'BOOK_UNIQID' =>$value->uniqid ));
                      
            
                
                 
     }
    
} else {

    $my_lesson_html = __('you_have_not_taken_any_lessons');

}




$kd->page_url_   = $kd->config->site_url.'/dashboard';

$kd->dashboard_page = 'dashboard';
$kd->admin_page = 'book';

if (!empty($_GET['page'])) {
    if (in_array($_GET['page'], $pages_array)) {
        $kd->dashboard_page = $_GET['page'];
        $kd->page_url_ = $kd->config->site_url.'/dashboard/'.$kd->dashboard_page.'/'.$kd->settings->username;
    }
} 

$final_page =  LoadPage("dashboard/pages/$kd->dashboard_page", [
         'USER_DATA' => $user,
         'TAKEN_LESSONS' => $my_lesson_html,
         'COMPLETED_QUIZ' => $completed_quiz,
         'ACHIEVEMENT_LIST' => $achievement_html,
         'QUIZ_COUNT' => $user_quiz_total,
         'LESSON_COUNT' => $user_lessons_total,
         'BOOK_COUNT' => count($get_my_lesson),
         'BOOKSTORE_LIST' => $bookstore        
]);



$kd->page        = 'dashboard';
$kd->title       = __('dashboard') . ' | ' . $kd->config->title;
$kd->description = $kd->config->description;
$kd->keyword     = $kd->config->keyword;


$kd->content     = LoadPage('dashboard/content', [
        'USER_DATA' => $user,
        'PROFILE_PAGE' => $final_page,

       
    
]);