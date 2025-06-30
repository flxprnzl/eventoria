<?php
session_start();
require_once 'dbconnect.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // ... [Dein vorhandener Code zur Validierung usw.] ...

    $hash = password_hash($password, PASSWORD_DEFAULT);

    $sql = "INSERT INTO user (vorname, nachname, email, password, organizer) VALUES (?, ?, ?, ?, ?)";
    $stmt = $dbuser->prepare($sql);
    $stmt->bind_param("sssss", $vorname, $nachname, $email, $hash, $organizer);

    if ($stmt->execute()) {
        // Automatisch einloggen:
        $user_id = $stmt->insert_id;
        $_SESSION['user_id'] = $user_id;
        $_SESSION['organizer'] = $organizer;
        echo '<script>alert("Registrierung erfolgreich!"); window.location.href="index.html";</script>';
        exit;
    } else {
        echo "Fehler: " . $stmt->error;
    }
    $stmt->close();
}
?>
