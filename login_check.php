<?php
session_start();
require_once 'dbconnect.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = $dbuser->real_escape_string($_POST['email']);
    $password = $_POST['password'];

    $sql = "SELECT id, password FROM user WHERE email = ?";
    $stmt = $dbuser->prepare($sql);
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $stmt->store_result();

    if ($stmt->num_rows === 1) {
        $stmt->bind_result($user_id, $hash);
        $stmt->fetch();

        if (password_verify($password, $hash)) {
            $_SESSION['user_id'] = $user_id;
            // Erfolgreich eingeloggt, JS-Alert und Weiterleitung
            echo '<script>alert("Login erfolgreich!"); window.location.href="index.html";</script>';
            exit;
        }
    }
    // Bei Fehler
    echo '<script>alert("E-Mail oder Passwort falsch!"); window.history.back();</script>';
    $stmt->close();
}
?>