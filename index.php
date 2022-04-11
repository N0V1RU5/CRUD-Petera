<?php
session_start();
require_once "_includes/bootstrap.inc.php";

$query = "SELECT * FROM employee WHERE login=:username";
$stmt = DB::getConnection()->prepare($query);
$stmt->bindParam(':username', $_POST['username']);
$stmt->execute();
$balls = $stmt->fetch();

if($_POST['username'] === $balls->login){
    if(hash("sha256", $_POST['password']) == $balls->password){
        $_SESSION['empId'] = $balls->employee_id;
        $_SESSION['name'] = $balls->name;
        $_SESSION['surname'] = $balls->surname;
        $_SESSION['login'] = $balls->login;
        $_SESSION['admin'] = $balls->admin;
        $_SESSION['logged'] = true;
    } else {
        header('Location: login.php');
    }
} else {
    header('Location: login.php');
}

if($_SESSION["logged"]){
    header('Location: menu.php');
}
?>