<?php

include "connect.php";

if(isset($_POST['register'])){

    $name = $_POST['name'];
    $email = $_POST['email'];

    $password = password_hash(
        $_POST['password'],
        PASSWORD_DEFAULT
    );

    $role = $_POST['role'];

    $image = $_FILES['image']['name'];
    $tmp_name = $_FILES['image']['tmp_name'];

    move_uploaded_file(
        $tmp_name,
        "uploads/".$image
    );

    $sql = "INSERT INTO users
    (name,email,password,role,image)

    VALUES

    ('$name','$email','$password','$role','$image')";

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

?>

<!DOCTYPE html>
<html>

<head>

    <title>User Registration</title>

    <style>

        *{
            margin:0;
            padding:0;
            box-sizing:border-box;
            font-family:Arial;
        }

        body{

            background:
            linear-gradient(
            135deg,
            #1e3a8a,
            #7c3aed
            );

            min-height:100vh;

            display:flex;
            justify-content:center;
            align-items:center;
        }

        .form-box{

            background:white;

            width:400px;

            padding:30px;

            border-radius:15px;

            box-shadow:
            0px 5px 20px rgba(0,0,0,0.2);

        }

        h2{

            text-align:center;

            margin-bottom:20px;

        }

        input,
        select{

            width:100%;

            padding:12px;

            margin-bottom:15px;

            border:1px solid #ccc;

            border-radius:8px;

        }

        button{

            width:100%;

            padding:12px;

            background:#1e3a8a;

            color:white;

            border:none;

            border-radius:8px;

            cursor:pointer;

            font-size:16px;

        }

        button:hover{

            background:#4338ca;

        }

    </style>

</head>

<body>

<div class="form-box">

    <h2>User Registration</h2>

    <form
    method="POST"
    enctype="multipart/form-data">

        <input
        type="text"
        name="name"
        placeholder="Enter Name"
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

        <select name="role">

            <option>User</option>

            <option>Admin</option>

        </select>

        <input
        type="file"
        name="image"
        required>

        <button
        type="submit"
        name="register">

        Register

        </button>

    </form>

</div>

</body>
</html>