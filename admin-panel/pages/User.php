<?php 
    include("../php/Navbar.php");
    $tid = $_GET['user'];
    $target = $_GET['target'];
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

    $query = "SELECT * from users WHERE TID='$target'";
    $runQuery  = mysqli_query($connected, $query);
    while($rows = mysqli_fetch_array($runQuery)){
        $firstname = $rows['Firstname'];
        $lastname = $rows['Lastname'];
        $subject = $rows['Subject'];
        $password = $rows['Password'];
        $grade = $rows['Grade'];
        $class = $rows['Class'];
    }
    if(isset($_POST['updateSubmit'])){
        $tid = $_GET['user'];
        $target = $_GET['target'];
        $firstname = $_POST["Firstname"];
        $lastname  = $_POST["Lastname"];
        $password    = $_POST["Password"];
        $subject     = $_POST["Subject"];
        $grade     = $_POST["Grade"];
        $class     = $_POST["Class"];
        $query     = "UPDATE users SET Firstname = '$firstname', Lastname = '$lastname', Grade = '$grade', Password = '$password', Subject = '$subject', Class = '$class' WHERE TID = '$target'";
        $runQuery  = mysqli_query($connected, $query);
        header("Location: Teachers.php?user=$tid");
    }
    if(isset($_POST['deleteStudent'])){
        $tid = $_GET['user'];
        $target = $_GET['target'];
        $query     = "DELETE FROM users WHERE TID = '$target'";
        $runQuery  = mysqli_query($connected, $query);
        header("Location: Teachers.php?user=$tid");
    }
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../css/styles.css">
    <title>Student</title>
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
                <h3><?php echo "$firstname $lastname"?></h3>
                <div class="top-bar-profile">
                    <div class="top-bar-profile-pic">
                        <img src="../assets/profile.png" alt="profile" />
                    </div>
                    <p><?php echo $name ?></p>
                </div>
            </div>
            <div class="main">
               <div class="student-container">
                    <div class="student-card">
                        <div class="student-thumbnail">
                            <img src="../assets/student.png" alt="student"/>
                        </div>
                        <div class="student-details">
                            <h2>Teacher Details</h2>
                            <div class="student-personal">
                                <p><span>First Name: </span> <?php echo $firstname ?></p>
                                <p><span>Last Name: </span> <?php echo $lastname ?></p>
                                <p><span>Password: </span> <?php echo $password ?></p>
                            </div>
                            <div class="student-school">
                                <p><span>Subject: </span> <?php echo $subject?></p>
                                <p><span>Grade: </span> <?php echo $grade?></p>
                                <p><span>Class: </span> <?php echo $class ?></p>
                            </div>
                            <button class="student-btn-update" onclick="updateStudent()">Update</button>
                            <button class="student-btn-delete" onclick="deleteStudent()">Delete</button>
                        </div>
                        <div class="student-update">
                            <h2>Update Details</h2>
                            <form class="student-input-fields" action="User.php?target=<?php echo $target?>&user=<?php echo $tid?>" method="POST">
                                <input type="text" name="Firstname" value="<?php echo $firstname?>" placeholder="Firstname">
                                <input type="text" name="Lastname" value="<?php echo $lastname?>" placeholder="Lastname">
                                <input type="text" name="Password" value="<?php echo $password?>" placeholder="Password">
                                <input type="text" name="Subject" value="<?php echo $subject?>" placeholder="Subject">
                                <input type="number" name="Grade" value="<?php echo $grade?>" placeholder="Grade">
                                <input type="text" name="class" value="<?php echo $class?>" placeholder="Class">
                                <input type="submit" class="student-btn-update" name="updateSubmit" value="Update"/>
                            </form>
                            <div class="student-input-btns">
                                <button class="student-btn-delete" onclick="updateStudentHide()">Cancel</button>
                            </div>
                        </div>
                        <form class="student-delete" action="User.php?target=<?php echo $target?>&user=<?php echo $tid?>" method="POST">
                            <p>Are you sure?</p>
                            <button type="submit" class="student-btn-update" name="deleteStudent">Yes</button>
                            <button class="student-btn-delete" onclick="deleteStudentHide()">No</button>
                        </form>
                    </div>
               </div>
            </div>
        </div>
    </div>
    <script>
        
        function updateStudent(){
            var element1 = document.querySelector('.student-delete');
            element1.style.display = 'none';
            var element = document.querySelector('.student-update');
            element.style.display = 'flex';
            
        }
        function updateStudentHide(){
            var element = document.querySelector('.student-update');
            element.style.display = 'none';
        }
        function deleteStudent(){
            var elementq = document.querySelector('.student-update');
            elementq.style.display = 'none';
            var element = document.querySelector('.student-delete');
            element.style.display = 'flex';
        }
        function deleteStudentHide(){
            var element = document.querySelector('.student-delete');
            element.style.display = 'none';
        }
    </script>
</body>
</html>