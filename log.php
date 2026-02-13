$userAgent = $_SERVER['HTTP_USER_AGENT'] ?? 'UNKNOWN';

$stmt = $conn->prepare("
INSERT INTO logs(user_id, action, ip, user_agent)
VALUES (?, ?, ?, ?)
");

$stmt->bind_param("isss", $user_id, $action, $ip, $userAgent);
