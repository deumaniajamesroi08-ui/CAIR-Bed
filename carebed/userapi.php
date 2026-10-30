<?php

include 'db_connection.php';

$sql = "SELECT * FROM tbl_user";

$result = mysqli_query($conn,$sql);

$users = [];

while($row = mysqli_fetch_assoc($result)){
    $users[] = $row;
}

echo json_encode($users);

mysqli_close($conn);

?>