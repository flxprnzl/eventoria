<?php
session_start();
header('Content-Type: application/json');

// Sicherstellen, dass der User eingeloggt ist!
if (!isset($_SESSION['user_id'])) {
    http_response_code(401);
    echo json_encode(['error' => 'Nicht eingeloggt']);
    exit;
}

$user_id = $_SESSION['user_id'];
// Initialisieren des userbezogenen Warenkorbs
if (!isset($_SESSION['cart'])) {
    $_SESSION['cart'] = [];
}
if (!isset($_SESSION['cart'][$user_id])) {
    $_SESSION['cart'][$user_id] = [];
}

// Tickets hinzufügen/entfernen
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data = json_decode(file_get_contents('php://input'), true);
    if (isset($data['event_id']) && isset($data['ticket_id'])) {
        $eventId = $data['event_id'];
        $ticketId = $data['ticket_id'];
        $qty = intval($data['qty']);
        if ($qty > 0) {
            // Hinzufügen oder aktualisieren
            $_SESSION['cart'][$user_id][$eventId][$ticketId] = $qty;
        } else {
            // Entfernen
            unset($_SESSION['cart'][$user_id][$eventId][$ticketId]);
            if (empty($_SESSION['cart'][$user_id][$eventId])) {
                unset($_SESSION['cart'][$user_id][$eventId]);
            }
        }
    }
    echo json_encode(['ok'=>true, 'cart'=>$_SESSION['cart'][$user_id]]);
    exit;
}

// Nur Warenkorb zurückgeben
if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['fetch'])) {
    echo json_encode(['cart'=>$_SESSION['cart'][$user_id]]);
    exit;
}

echo json_encode(['error'=>'Ungültige Anfrage']);
exit;
?>
