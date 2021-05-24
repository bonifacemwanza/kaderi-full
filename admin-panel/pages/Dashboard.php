<?php 
    include("../php/Navbar.php");
    $tid = $_GET['user'];
    $server = "localhost";
    $name = "root";
    $pass = "root";
    $DBName = "mwabombeni";
    $connected = mysqli_connect($server, $name, $pass, $DBName);
    $query = "SELECT * from users WHERE TID='$tid'";
    $runQuery  = mysqli_query($connected, $query);
    while($rows = mysqli_fetch_array($runQuery)){
        $name = $rows['Firstname'];
        $access = $rows['Access'];
    }

    $query = "SELECT * from students";
    $runQuery  = mysqli_query($connected, $query);
    $students = mysqli_num_rows($runQuery);

    $query = "SELECT * from subjects";
    $runQuery  = mysqli_query($connected, $query);
    $subjects = mysqli_num_rows($runQuery);

    $query = "SELECT * from users";
    $runQuery  = mysqli_query($connected, $query);
    $users = mysqli_num_rows($runQuery);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../css/styles.css">
    <title>Document</title>
</head>
<body>
    <div class="Dashboard-container">
        <div class="dash-board-sidebar">
            <div class="side-bar-top">
                <div class="logo-div"> 
                    <img src="../assets/SFC1.png" alt="logo"/>
                </div>
                <div class="names-div">
                    <p>Mwabombeni</p>
                    <p>Combined School</p>
                </div> 
            </div>
            <div class="side-bar-bottom">
                <?php Navbar($tid) ?>
            </div>
        </div>
        <div class="dash-board-main">
            <div class="top-bar">
                <h3>Dashboard</h3>
                <div class="top-bar-profile">
                    <div class="top-bar-profile-pic">
                        <img src="../assets/profile.png" alt="profile" />
                    </div>
                    <p><?php echo $name; ?></p>
                </div>
            </div>
            <div class="main-area">
                <div class="main-area-cards">
                    <div class="cards-icon">
                        <img src="../assets/student.png" alt="student" />
                    </div>
                    <div class="cards-detail">
                        <p>Students</p>
                        <div class="cards-detail-count">
                            <?php echo $students ?>
                        </div>
                    </div>
                </div>
                <div class="main-area-cards">
                    <div class="cards-icon">
                        <img src="../assets/subjects.png" alt="student" />
                    </div>
                    <div class="cards-detail">
                        <p>Subjects</p>
                        <div class="cards-detail-count">
                            <?php echo $subjects ?>
                        </div>
                    </div>
                </div>
                <div class="main-area-cards">
                    <div class="cards-icon">
                        <img src="../assets/exam.png" alt="student" />
                    </div>
                    <div class="cards-detail">
                        <p>Exams</p>
                        <div class="cards-detail-count">
                            12/34
                        </div>
                    </div>
                </div>
                <div class="main-area-cards">
                    <div class="cards-icon">
                        <img src="../assets/profile.png" alt="student" />
                    </div>
                    <div class="cards-detail">
                        <p>Teachers</p>
                        <div class="cards-detail-count">
                            <?php echo $users ?>
                        </div>
                    </div>
                </div>
                <div class="main-area-cards">
                    <div class="cards-icon">
                        <img src="../assets/calendar.png" alt="student" />
                    </div>
                    <div class="cards-detail">
                        <p>Academic Year</p>
                        <div class="cards-detail-count">
                           2021
                        </div>
                    </div>
                </div>
                <div class="main-area-cards">
                    <div class="cards-icon">
                        <img src="../assets/marks.png" alt="student" />
                    </div>
                    <div class="cards-detail">
                        <p>Marks</p>
                        <div class="cards-detail-count">
                            70.3%
                        </div>
                    </div>
                </div>
                <div class="main-area-cards">
                    <div class="cards-icon">
                        <img src="../assets/transcripts.png" alt="student" />
                    </div>
                    <div class="cards-detail">
                        <p>Transcripts</p>
                        <div class="cards-detail-count">
                            23
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>