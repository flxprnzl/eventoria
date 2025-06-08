<?php
session_start();
require_once 'dbconnect.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.html");
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $user_id = $_SESSION['user_id'];
    $old_password = $_POST['old_password'];
    $new_password = $_POST['new_password'];
    $new_password2 = $_POST['new_password2'];

    // Neues Passwort prüfen
    if ($new_password !== $new_password2) {
        echo '<script>alert("Die neuen Passwörter stimmen nicht überein!"); window.history.back();</script>';
        exit;
    }

    // Altes Passwort prüfen
    $sql = "SELECT password FROM user WHERE id = ?";
    $stmt = $dbuser->prepare($sql);
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $stmt->bind_result($hash);
    $stmt->fetch();
    $stmt->close();

    if (!password_verify($old_password, $hash)) {
        echo '<script>alert("Das alte Passwort ist falsch!"); window.history.back();</script>';
        exit;
    }

    // Neues Passwort speichern
    $new_hash = password_hash($new_password, PASSWORD_DEFAULT);
    $sql = "UPDATE user SET password = ? WHERE id = ?";
    $stmt = $dbuser->prepare($sql);
    $stmt->bind_param("si", $new_hash, $user_id);

    if ($stmt->execute()) {
        echo '<script>alert("Passwort erfolgreich geändert!"); window.location.href="account.php";</script>';
    } else {
        echo '<script>alert("Fehler beim Ändern des Passworts!"); window.history.back();</script>';
    }
    $stmt->close();
}
?>