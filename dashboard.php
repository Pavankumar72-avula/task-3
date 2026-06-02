<?php

session_start();

if(!isset($_SESSION['user_id'])){
    header("Location: login.php");
    exit();
}

?>

<!DOCTYPE html>
<html lang="en">

<head>

<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">

<title>Dashboard</title>

<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">

<style>

*{
    margin:0;
    padding:0;
    box-sizing:border-box;
    font-family:'Poppins',sans-serif;
}

body{
    min-height:100vh;
    background:linear-gradient(135deg,#2563eb,#7c3aed);
    display:flex;
    justify-content:center;
    align-items:center;
}

.dashboard{

    width:500px;

    background:white;

    padding:40px;

    border-radius:20px;

    box-shadow:0 15px 35px rgba(0,0,0,0.2);

    text-align:center;
}

.avatar{

    width:100px;

    height:100px;

    border-radius:50%;

    background:#2563eb;

    color:white;

    display:flex;

    align-items:center;

    justify-content:center;

    font-size:40px;

    margin:0 auto 20px;

}

h1{
    color:#111827;
    margin-bottom:15px;
}

.info{
    margin:15px 0;
    color:#374151;
    font-size:18px;
}

.admin-panel{

    margin-top:20px;

    padding:15px;

    background:#eff6ff;

    border-radius:12px;
}

.btn{

    display:inline-block;

    margin-top:20px;

    padding:12px 25px;

    background:#dc2626;

    color:white;

    text-decoration:none;

    border-radius:10px;

    transition:.3s;
}

.btn:hover{
    background:#b91c1c;
}

.manage{

    display:inline-block;

    margin-top:15px;

    padding:10px 20px;

    background:#2563eb;

    color:white;

    text-decoration:none;

    border-radius:10px;
}

</style>

</head>

<body>

<div class="dashboard">

    <div class="avatar">
        👤
    </div>

    <h1>
        Welcome
        <?php echo $_SESSION['user_name']; ?>
    </h1>

    <div class="info">
        <strong>Email:</strong>
        <?php echo $_SESSION['user_email']; ?>
    </div>

    <div class="info">
        <strong>Role:</strong>
        <?php echo $_SESSION['role']; ?>
    </div>

    <?php

    if($_SESSION['role']=="admin"){

        echo "
        <div class='admin-panel'>
            <h3>Admin Panel</h3>
            <a href='users.php' class='manage'>
            Manage Users
            </a>
        </div>
        ";

    }

    ?>

    <a href="logout.php" class="btn">
        Logout
    </a>

</div>

</body>
</html>