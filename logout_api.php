<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    // Nicht eingeloggt
    http_response_code(204); // Kein Inhalt
    exit;
}
session_unset();
session_destroy();
http_response_code(200);
?>