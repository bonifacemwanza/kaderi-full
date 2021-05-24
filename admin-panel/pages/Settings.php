<?php include("../php/Navbar.php");
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
    if(!$connected) echo "database not connected";

    if(isset($_POST['student-submit'])){
        $tid = $_GET['user'];
        $firstname = $_POST["Firstname"];
        $lastname  = $_POST["Lastname"];
        $gender    = $_POST["Gender"];
        $age       = $_POST["Age"];
        $grade     = $_POST["Grade"];
        $class     = $_POST["Class"];
        $query     = "INSERT INTO students (Firstname, Lastname, Gender, Age, Grade, Class) 
                        VALUES ('$firstname','$lastname','$gender','$age','$grade','$class')";
        $runQuery  = mysqli_query($connected, $query);
        header("Location: AllStudents.php?user=$tid");
    }

    if(isset($_POST['teacher-submit'])){
        $tid = $_GET['user'];
        $firstname = $_POST["Firstname"];
        $lastname  = $_POST["Lastname"];
        $grade     = $_POST["Grade"];
        $subject   = $_POST["Subject"];
        $class     = $_POST["Class"];
        $password  = $_POST["Password"];
        $query     = "INSERT INTO users(Firstname, Lastname, Grade, Subject, Class, Password, Access) 
                    VALUES ('$firstname','$lastname',$grade,'$subject','$class','$password',1)";
        $runQuery  = mysqli_query($connected, $query);
        header("Location: Teachers.php?user=$tid");

    }

    if(isset($_POST['subject-submit'])){
        $tid = $_GET['user'];
        $name    = $_POST["Name"];
        $grade   = $_POST["Grade"];
        $teacher = $_POST["Teacher"];
        $query   = "INSERT INTO subjects(Name, Grade, Teacher) 
                    VALUES ('$name','$grade','$teacher')";
        $runQuery  = mysqli_query($connected, $query);
        header("Location: Subject.php?user=$tid");

    }

    if(isset($_POST['class-submit'])){
        $tid = $_GET['user'];
        $grade    = $_POST["Grade"];
        $letter   = $_POST["Letter"];
        $teacher = $_POST["Teacher"];
        $query   = "INSERT INTO classes(Grade, Letter, Teacher) 
                    VALUES ('$grade','$letter','$teacher')";
        $runQuery  = mysqli_query($connected, $query);
        header("Location: Settings.php?user=$tid");

    }

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
                <h3>Settings</h3>
                <div class="top-bar-profile">
                    <div class="top-bar-profile-pic">
                        <img src="../assets/profile.png" alt="profile" />
                    </div>
                    <p><?php echo $name ?></p>
                </div>
            </div>
            <div class="main">
               <div class="settings-container">
                   <div class="terms" onclick="addStudent()">
                        <p>Add Student</p>
                   </div>
                   <div class="terms" onclick="addTeacher()">
                        <p>Add Teacher</p>
                   </div>
                   <div class="terms" onclick="addSubject()">
                        <p>Add Subject</p>
                   </div>
                   <div class="terms" onclick="addClass()">
                        <p>Add Class</p>
                   </div>

                   <div class="add-student-modal">
                       <div class="settings-add-details">
                           <h2>Add Student</h2>
                           <form class="settings-add-inputs" action="Settings.php?user=<?php echo $tid?>" method="POST">
                                <input type="text" placeholder="Firstname" name="Firstname"/>
                                <input type="text" placeholder="Lastname" name="Lastname"/>
                                <input type="text" placeholder="Gender" name="Gender"/>
                                <input type="number" placeholder="Age" name="Age"/>
                                <input type="number" placeholder="Grade" name="Grade"/>
                                <input type="text" placeholder="Class" name="Class"/>
                                <input type="submit" class="student-btn-update" value="Add" name="student-submit"/>
                                <input type="button" class="student-btn-delete" value="Cancel" onclick="cancelAddStudent()"/>
                           </form>
                       </div>
                   </div>

                   <div class="add-teacher-modal">
                       <div class="settings-add-details">
                           <h2>Add Teacher</h2>
                           <form class="settings-add-inputs" action="Settings.php?user=<?php echo $tid?>" method="POST">
                                <input type="text" placeholder="Firstname" name="Firstname"/>
                                <input type="text" placeholder="Lastname" name="Lastname"/>
                                <input type="text" placeholder="Grade" name="Grade"/>
                                <input type="text" placeholder="Subject" name="Subject"/>
                                <input type="text" placeholder="Class" name="Class"/>
                                <input type="text" placeholder="Password" name="Password"/>
                                <input type="submit" class="student-btn-update" value="Add" name="teacher-submit"/>
                                <input type="button" class="student-btn-delete" value="Cancel" onclick="cancelAddTeacher()"/>
                           </form>
                       </div>
                   </div>

                   <div class="add-subject-modal">
                       <div class="settings-add-details">
                           <h2>Add Subject</h2>
                           <form class="settings-add-inputs" action="Settings.php?user=<?php echo $tid?>" method="POST">
                                <input type="text" placeholder="Name" name="Name"/>
                                <input type="text" placeholder="Grade" name="Grade"/>
                                <input type="text" placeholder="Teacher" name="Teacher"/>
                                <input type="submit" class="student-btn-update" value="Add" name="subject-submit"/>
                                <input type="button" class="student-btn-delete" value="Cancel" onclick="cancelAddSubject()"/>
                           </form>
                       </div>
                   </div>

                   <div class="add-class-modal">
                       <div class="settings-add-details">
                           <h2>Add Class</h2>
                           <form class="settings-add-inputs" action="Settings.php?user=<?php echo $tid?>" method="POST">
                                <input type="text" placeholder="Grade" name="Grade" />
                                <input type="text" placeholder="Letter" name="Letter"/>
                                <input type="text" placeholder="Teacher" name="Teacher"/>
                                <input type="submit" class="student-btn-update" value="Add" name="class-submit"/>
                                <input type="button" class="student-btn-delete" value="Cancel" onclick="cancelAddClass()"/>
                           </form>
                       </div>
                   </div>
                   
                   
               </div>
            </div>
        </div>
    </div>
    <script src="../javascript/js.js" ></script>
</body>
</html>