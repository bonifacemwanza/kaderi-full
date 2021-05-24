<?php 
    include("../php/Navbar.php");
    $num =0;
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
    $query = "SELECT * from subjects";
    $runQuery  = mysqli_query($connected, $query);

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
                <h3>All Subjects</h3>
                <div class="top-bar-profile">
                    <div class="top-bar-profile-pic">
                        <img src="../assets/profile.png" alt="profile" />
                    </div>
                    <p><?php echo $name ?></p>
                </div>
            </div>
            <div class="main">
               <div class="marks-search-container">
                   <input type="search" placeholder="search..." />
                   <button>Search</botton>
               </div>
               <div class="marks-table">
                    <table style="width:90%">
                        <tr class="table-header">
                            <th>#</th>
                            <th>Subject</th>
                            <th>Grade</th>
                            <th>Teacher</th>
                        </tr>
                        <?php
                            while($rows = mysqli_fetch_array($runQuery)){
                                $name = $rows['Name'];
                                $grade = $rows['Grade'];
                                $teacher = $rows['Teacher'];
                                $num ++;

                                echo "
                                    <tr>
                                        <td>$num</td>
                                        <td>$name</td>
                                        <td>$grade</td>
                                        <td>$teacher</td>
                                    </tr>
                                ";
                            }
                        
                        ?>
                        
                        
                    </table>
               </div>
            </div>
        </div>
    </div>
</body>
</html>