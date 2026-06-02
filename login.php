<?php

session_start();

include "connect.php";

if(isset($_POST['login'])){

    $email = $_POST['email'];
    $password = $_POST['password'];

    $sql = "SELECT * FROM users WHERE email='$email'";

    $result = mysqli_query($conn,$sql);

    if(mysqli_num_rows($result) > 0){

        $user = mysqli_fetch_assoc($result);

        if(password_verify($password,$user['password'])){

            $_SESSION['user_id'] = $user['id'];
            $_SESSION['user_name'] = $user['name'];
            $_SESSION['user_email'] = $user['email'];
            $_SESSION['role'] = $user['role'];

            header("Location: dashboard.php");
            exit();

        }else{

            echo "Wrong Password";

        }

    }else{

        echo "User Not Found";

    }

}

?>

<!DOCTYPE html>
<html lang="en">

<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">

<title>Login</title>

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
    display:flex;
    justify-content:center;
    align-items:center;
    background:linear-gradient(135deg,#2563eb,#7c3aed);
}

.login-container{

    width:400px;

    background:white;

    padding:40px;

    border-radius:20px;

    box-shadow:0 15px 35px rgba(0,0,0,0.2);

}

.logo{
    text-align:center;
    margin-bottom:10px;
    font-size:32px;
}

h2{
    text-align:center;
    margin-bottom:30px;
    color:#111827;
}

.input-group{
    margin-bottom:20px;
}

.input-group label{
    display:block;
    margin-bottom:8px;
    font-weight:500;
    color:#374151;
}

.input-group input{

    width:100%;

    padding:14px;

    border:2px solid #e5e7eb;

    border-radius:12px;

    font-size:15px;

    transition:0.3s;
}

.input-group input:focus{

    outline:none;

    border-color:#2563eb;

    box-shadow:0 0 10px rgba(37,99,235,0.2);

}

.login-btn{

    width:100%;

    padding:14px;

    border:none;

    border-radius:12px;

    background:#2563eb;

    color:white;

    font-size:16px;

    font-weight:600;

    cursor:pointer;

    transition:0.3s;

}

.login-btn:hover{

    background:#1d4ed8;

    transform:translateY(-2px);

}

.register-link{

    text-align:center;

    margin-top:20px;

}

.register-link a{

    text-decoration:none;

    color:#2563eb;

    font-weight:600;

}

.register-link a:hover{

    text-decoration:underline;

}

</style>

</head>

<body>

<div class="login-container">

    <div class="logo">🔐</div>

    <h2>User Login</h2>

    <form method="POST">

        <div class="input-group">

            <label>Email</label>

            <input
            type="email"
            name="email"
            placeholder="Enter your email"
            required>

        </div>

        <div class="input-group">

            <label>Password</label>

            <input
            type="password"
            name="password"
            placeholder="Enter your password"
            required>

        </div>

        <button
        type="submit"
        name="login"
        class="login-btn">

        Login

        </button>

    </form>

    <div class="register-link">

        Don't have an account?
        <a href="register.php">Register</a>

    </div>

</div>

</body>
</html>