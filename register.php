<?php

include "connect.php";

if(isset($_POST['register'])){

    $name = $_POST['name'];
    $email = $_POST['email'];

    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];

    $role = $_POST['role'];

    if($password != $confirm_password){

        echo "<script>
        alert('Passwords do not match');
        </script>";

    }else{

        $hashed_password = password_hash(
            $password,
            PASSWORD_DEFAULT
        );

        $sql = "INSERT INTO users
        (name,email,password,role)

        VALUES

        ('$name','$email','$hashed_password','$role')";

        if(mysqli_query($conn,$sql)){

            echo "<script>
            alert('Registration Successful');
            window.location='login.php';
            </script>";

        }else{

            echo "<script>
            alert('Registration Failed');
            </script>";

        }

    }

}

?>

<!DOCTYPE html>
<html lang="en">

<head>

<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">

<title>User Registration</title>

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

    background:
    linear-gradient(
    135deg,
    #2563eb,
    #7c3aed
    );

}

.form-box{

    width:420px;

    background:white;

    padding:35px;

    border-radius:20px;

    box-shadow:
    0 15px 35px rgba(0,0,0,0.2);

}

h2{

    text-align:center;

    margin-bottom:25px;

    color:#111827;

}

input,
select{

    width:100%;

    padding:13px;

    margin-bottom:15px;

    border:2px solid #e5e7eb;

    border-radius:10px;

    font-size:15px;

    transition:0.3s;

}

input:focus,
select:focus{

    outline:none;

    border-color:#2563eb;

}

button{

    width:100%;

    padding:14px;

    border:none;

    border-radius:10px;

    background:#2563eb;

    color:white;

    font-size:16px;

    font-weight:600;

    cursor:pointer;

    transition:0.3s;

}

button:hover{

    background:#1d4ed8;

}

.login-link{

    text-align:center;

    margin-top:15px;

}

.login-link a{

    text-decoration:none;

    color:#2563eb;

    font-weight:600;

}

</style>

</head>

<body>

<div class="form-box">

<h2>Create Account</h2>

<form method="POST">

<input
type="text"
name="name"
placeholder="Enter Full Name"
required>

<input
type="email"
name="email"
placeholder="Enter Email"
required>

<input
type="password"
name="password"
placeholder="Enter Password"
required>

<input
type="password"
name="confirm_password"
placeholder="Confirm Password"
required>

<select name="role" required>

<option value="">
Select Role
</option>

<option value="user">
User
</option>

<option value="admin">
Admin
</option>

</select>

<button
type="submit"
name="register">

Register

</button>

</form>

<div class="login-link">

Already have an account?

<a href="login.php">
Login
</a>

</div>

</div>

</body>
</html>