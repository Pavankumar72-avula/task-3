<?php

session_start();

include "connect.php";

if(!isset($_SESSION['user_id'])){
    header("Location: login.php");
    exit();
}

$result = mysqli_query($conn,"SELECT * FROM users");

?>

<!DOCTYPE html>
<html lang="en">

<head>

<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">

<title>Manage Users</title>

<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">

<style>

*{
    margin:0;
    padding:0;
    box-sizing:border-box;
    font-family:'Poppins',sans-serif;
}

body{
    background:#f3f4f6;
    padding:40px;
}

.container{
    max-width:1100px;
    margin:auto;
}

.header{
    display:flex;
    justify-content:space-between;
    align-items:center;
    margin-bottom:25px;
}

h1{
    color:#111827;
}

.back-btn{
    text-decoration:none;
    background:#2563eb;
    color:white;
    padding:10px 18px;
    border-radius:8px;
}

table{
    width:100%;
    border-collapse:collapse;
    background:white;
    border-radius:10px;
    overflow:hidden;
    box-shadow:0 5px 20px rgba(0,0,0,0.1);
}

th{
    background:#2563eb;
    color:white;
    padding:15px;
}

td{
    padding:15px;
    text-align:center;
    border-bottom:1px solid #e5e7eb;
}

.edit{
    text-decoration:none;
    background:#f59e0b;
    color:white;
    padding:8px 15px;
    border-radius:6px;
}

.delete{
    text-decoration:none;
    background:#dc2626;
    color:white;
    padding:8px 15px;
    border-radius:6px;
}

</style>

</head>

<body>

<div class="container">

<div class="header">

<h1>User Management</h1>

<a href="dashboard.php" class="back-btn">
Dashboard
</a>

</div>

<table>

<tr>
<th>ID</th>
<th>Name</th>
<th>Email</th>
<th>Role</th>
<th>Action</th>
</tr>

<?php

while($row=mysqli_fetch_assoc($result)){

?>

<tr>

<td><?php echo $row['id']; ?></td>

<td><?php echo $row['name']; ?></td>

<td><?php echo $row['email']; ?></td>

<td><?php echo $row['role']; ?></td>

<td>

<a
class="edit"
href="edit.php?id=<?php echo $row['id']; ?>">
Edit
</a>

<a
class="delete"
href="delete.php?id=<?php echo $row['id']; ?>"
onclick="return confirm('Delete this user?')">
Delete
</a>

</td>

</tr>

<?php

}

?>

</table>

</div>

</body>
</html>