<?php 
$page = 'dashboard';
if (!empty($_GET['page'])) {
    $page = Secure($_GET['page']);
}

$page_loaded = '';
$pages = array(
    'dashboard',
    'users',
    'lessons',
);

if (in_array($page, $pages)) {
    $page_loaded = LoadAdminPage("$page/content");
} 

if (empty($page_loaded)) {
    header("Location: " . UrlLink('admincp'));
    exit();
}

// if ($page == 'dashboard') {
//     if ($db->config->last_admin_collection < (time() - 1800)) {
//         $update_information = UpdateAdminDetails();
//     }
// }

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="Cache-Control" content="no-cache, no-store, must-revalidate" /> 
    <meta http-equiv="Pragma" content="no-cache" /> 
    <meta http-equiv="Expires" content="0" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="<?php echo LoadAdminLink('css/styles.css'); ?>">

    <!-- Latest compiled and minified CSS -->
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css" integrity="sha384-BVYiiSIFeK1dGmJRAkycuHAHRg32OmUcww7on3RYdg4Va+PmSTsz/K68vbdEjh4u" crossorigin="anonymous">
    <script src="https://cdn.tiny.cloud/1/4a22ohtb98axrzoa2pw5jk5dtbg86okl6m0lepla5z2zekno/tinymce/5/tinymce.min.js"></script>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js" integrity="sha512-894YE6QWD5I59HgZOGReFYm4dnWc1Qt5NtvYSaNcOP+u1T9qYdvdihz0PPSiiqn/+/3e7Jo4EaG7TubfWGUrMQ==" crossorigin="anonymous"></script>
    <script src="<?php echo LoadAdminLink('javascript/js.js'); ?>"></script>
    <title>Admin Panel | <?php echo $kd->config->title; ?></title>
</head>
<body>
    <div class="Dashboard-container">
    <div class="dash-board-sidebar">
    <div class="side-bar-top">
        <div class="logo-div"> 
            <img src="<?php echo LoadAdminLink('assets/SFC1.png'); ?>" alt="logo"/>
        </div>
        <div class="names-div">
            <p>The Fate Changer </p>
            <p>Dashboard</p>
        </div> 
    </div>
    <div class="side-bar-bottom">
        <div>
            <a  href="<?php echo LoadAdminLinkSettings('dashboard'); ?>" <?php echo ($page == 'dashboard')? 'class="nav-link active"' : 'class="nav-link"'; ?>  >
                <img src="<?php echo LoadAdminLink('assets/home.png'); ?> " alt="icon" />
                <p>Dashboard</p>
            </a>
            <a href="<?php echo LoadAdminLinkSettings('users'); ?>" <?php echo ($page == 'users')? 'class="nav-link active"' : 'class="nav-link"' ?>>
                <img src="<?php echo LoadAdminLink('assets/student.png'); ?>" alt="icon"/>
                <p>Users</p>
            </a>
            <a   href="<?php echo LoadAdminLinkSettings('lessons'); ?>" <?php echo ($page == 'users')? 'class="nav-link active"' : 'class="nav-link"' ?>>
                <img src="<?php echo LoadAdminLink('assets/subjects.png'); ?>" alt="icon"/>
                <p>Lessons</p>
            </a>
            <a  href="<?php echo LoadAdminLinkSettings('instructor'); ?>" <?php echo ($page == 'instructor')? 'class="nav-link active"' : 'class="nav-link"' ?>>
                <img src="<?php echo LoadAdminLink('assets/profile.png'); ?>" alt="icon"/>
                <p>Instructors</p>
            </a>
            <a   href="<?php echo LoadAdminLinkSettings('quiz'); ?>" <?php echo ($page == 'quiz')? 'class="nav-link active"' : 'class="nav-link"' ?>>
                <img src="<?php echo LoadAdminLink('assets/exam.png'); ?>" alt="icon"/>
                <p>Exams</p>
            </a>
            <a  href="<?php echo LoadAdminLinkSettings('promotions'); ?>" <?php echo ($page == 'promotions')? 'class="nav-link active"' : 'class="nav-link"' ?>>
                <img src="<?php echo LoadAdminLink('assets/calendar.png'); ?>" alt="icon"/>
                <p>Promotions</p>
            </a>
            <a  href="<?php echo LoadAdminLinkSettings('marks'); ?>" <?php echo ($page == 'marks')? 'class="nav-link active"' : 'class="nav-link"' ?>>
                <img src="<?php echo LoadAdminLink('assets/marks.png'); ?>" alt="icon"/>
                <p>Marks</p>
            </a>
            <a   href="<?php echo LoadAdminLinkSettings('rewards'); ?>" <?php echo ($page == 'rewards')? 'class="nav-link active"' : 'class="nav-link"' ?>>
                <img src="<?php echo LoadAdminLink('assets/transcripts.png'); ?>" alt="icon"/>
                <p>Rewards</p>
            </a>
        </div>
        <div>
            <a  href="<?php echo LoadAdminLinkSettings('settings'); ?>" <?php echo ($page == 'settings')? 'class="nav-link active"' : 'class="nav-link"' ?>>
                <img src="<?php echo LoadAdminLink('assets/Settings.png'); ?>" alt="icon"/>
                <p>Settings</p>
            </a>
            <a  href="<?php echo LoadAdminLinkSettings('logout'); ?>" <?php echo ($page == 'users')? 'class="nav-link active"' : 'class="nav-link"' ?>>
                <img src="<?php echo LoadAdminLink('assets/logout.png'); ?>" alt="icon"/>
                <p>Log Out</p>
            </a>
        </div>

        </div>
    </div>
    <div class="content" style="display:flex; flex:1;">
        <?php echo $page_loaded; ?>
    </div>
    </div>
</body>
</html>