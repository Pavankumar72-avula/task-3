<?php

include "connect.php";

$id = $_GET['id'];

$result = mysqli_query(
$conn,
"SELECT * FROM users WHERE id=$id"
);

$user = mysqli_fetch_assoc($result);

if(isset($_POST['update'])){

    $name = $_POST['name'];
    $email = $_POST['email'];
    $role = $_POST['role'];

    mysqli_query(
    $conn,
    "UPDATE users
    SET
    name='$name',
    email='$email',
    role='$role'
    WHERE id=$id"
    );

    header("Location: users.php");
    exit();
}

?>

<!DOCTYPE html>
<html lang="en">

<head>

<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">

<title>Edit User</title>

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

.edit-card{

    width:450px;

    background:white;

    padding:35px;

    border-radius:20px;

    box-shadow:0 15px 35px rgba(0,0,0,0.2);

}

h2{
    text-align:center;
    margin-bottom:25px;
    color:#111827;
}

.input-group{
    margin-bottom:18px;
}

label{
    display:block;
    margin-bottom:8px;
    font-weight:500;
    color:#374151;
}

input,
select{

    width:100%;

    padding:12px;

    border:2px solid #e5e7eb;

    border-radius:10px;

    font-size:15px;

}

input:focus,
select:focus{

    outline:none;

    border-color:#2563eb;

}

.btn{

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

.btn:hover{

    background:#1d4ed8;

}

.back{

    display:block;

    text-align:center;

    margin-top:15px;

    text-decoration:none;

    color:#2563eb;

    font-weight:600;

}

</style>

</head>

<body>

<div class="edit-card">

<h2>Edit User</h2>

<form method="POST">

<div class="input-group">

<label>Name</label>

<input
type="text"
name="name"
value="<?php echo $user['name']; ?>"
required>

</div>

<div class="input-group">

<label>Email</label>

<input
type="email"
name="email"
value="<?php echo $user['email']; ?>"
required>

</div>

<div class="input-group">

<label>Role</label>

<select name="role">

<option value="admin"
<?php if($user['role']=="admin") echo "selected"; ?>>
Admin
</option>

<option value="user"
<?php if($user['role']=="user") echo "selected"; ?>>
User
</option>

</select>

</div>

<button
type="submit"
name="update"
class="btn">

Update User

</button>

</form>

<a href="users.php" class="back">
← Back to Users
</a>

</div>

</body>
</html>