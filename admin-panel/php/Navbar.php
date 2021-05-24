<?php
ini_set('display_erros', 1);
error_reporting(E_ALL);
    function Navbar($sid){
        echo '
            <div>
                <a href="Dashboard.php?user='.$sid.'" class="nav-link">
                    <img src="../assets/home.png" alt="icon"/>
                    <p>Dashboard</p>
                </a>
                <a href="AllStudents.php?user='.$sid.'" class="nav-link">
                    <img src="../assets/student.png" alt="icon"/>
                    <p>Students</p>
                </a>
                <a href="Subjects.php?user='.$sid.'" class="nav-link">
                    <img src="../assets/subjects.png" alt="icon"/>
                    <p>Subjects</p>
                </a>
                <a href="Teachers.php?user='.$sid.'" class="nav-link">
                    <img src="../assets/profile.png" alt="icon"/>
                    <p>Teachers</p>
                </a>
                <a href="Exams.php?user='.$sid.'" class="nav-link">
                    <img src="../assets/exam.png" alt="icon"/>
                    <p>Exams</p>
                </a>
                <a href="Academicyear.php?user='.$sid.'" class="nav-link">
                    <img src="../assets/calendar.png" alt="icon"/>
                    <p>Academic year</p>
                </a>
                <a href="Marks.php?user='.$sid.'" class="nav-link">
                    <img src="../assets/marks.png" alt="icon"/>
                    <p>Marks</p>
                </a>
                <a href="Transcripts.php?user='.$sid.'" class="nav-link">
                    <img src="../assets/transcripts.png" alt="icon"/>
                    <p>Transcripts</p>
                </a>
            </div>
            <div>
                <a href="Settings.php?user='.$sid.'" class="nav-link">
                    <img src="../assets/Settings.png" alt="icon"/>
                    <p>Settings</p>
                </a>
                <a href="../index.php" class="nav-link">
                    <img src="../assets/logout.png" alt="icon"/>
                    <p>Log Out</p>
                </a>
            </div>
        
        ';
    }
?>