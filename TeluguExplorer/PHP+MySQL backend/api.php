<?php
/**
 * ═══════════════════════════════════════════════════════════
 *  Mana Telugu Pune — API Backend
 *  File   : api.php
 *  Place  : public_html/api.php  (same folder as index.html)
 *  Requires: PHP 7.4+ · MySQL 5.7+
 * ═══════════════════════════════════════════════════════════
 */

session_start();
header('Content-Type: application/json');

// ════════════════════════════════════════
//  ⚙️  DATABASE CONFIG
//  ── Update these 4 lines with your
//     Hostinger MySQL credentials ──
// ════════════════════════════════════════
define('DB_HOST', 'localhost');
define('DB_NAME', 'your_database_name');   // hPanel → MySQL Databases
define('DB_USER', 'your_database_user');   // hPanel → MySQL Databases
define('DB_PASS', 'your_database_password');

// ════════════════════════════════════════
//  👑  ADMIN CREDENTIALS
//  ── Change before going live ──
// ════════════════════════════════════════
define('ADMIN_EMAIL', 'admin@mtp.com');
define('ADMIN_PASS',  'Admin@2024');

// ════════════════════════════════════════
//  DATABASE CONNECTION
// ════════════════════════════════════════
function getDB() {
    static $pdo = null;
    if ($pdo) return $pdo;
    try {
        $pdo = new PDO(
            'mysql:host=' . DB_HOST . ';dbname=' . DB_NAME . ';charset=utf8mb4',
            DB_USER,
            DB_PASS,
            [
                PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES   => false,
            ]
        );
    } catch (PDOException $e) {
        http_response_code(500);
        echo json_encode(['ok' => false, 'msg' => 'Database connection failed. Check your DB config in api.php.']);
        exit;
    }
    return $pdo;
}

// ════════════════════════════════════════
//  RESPONSE HELPERS
// ════════════════════════════════════════
function ok(array $data = []) {
    echo json_encode(array_merge(['ok' => true], $data));
    exit;
}
function err(string $msg, int $code = 400) {
    http_response_code($code);
    echo json_encode(['ok' => false, 'msg' => $msg]);
    exit;
}

// ════════════════════════════════════════
//  PLACE ROW MAPPER
//  Maps DB column names → JS property names
// ════════════════════════════════════════
function mapPlace(array $row): array {
    return [
        'id'        => (int)   $row['id'],
        'placeCode' =>         $row['place_code']  ?? '',
        'name'      =>         $row['name'],
        'category'  =>         $row['category']    ?? '',
        'subcat'    =>         $row['subcat']       ?? '',
        'location'  =>         $row['location']    ?? '',
        'district'  =>         $row['district']    ?? '',
        'desc'      =>         $row['description'] ?? '',   // desc in JS
        'rating'    => (float) $row['rating']      ?? 0,
        'reviews'   => (int)   $row['reviews']     ?? 0,
        'distance'  =>         $row['distance']    ?? '',
        'bestTime'  =>         $row['best_time']   ?? '',   // bestTime in JS
        'img'       =>         $row['img']         ?? '',
        'status'    =>         $row['status']      ?? 'Active',
    ];
}

// ════════════════════════════════════════
//  READ JSON BODY
// ════════════════════════════════════════
$body   = json_decode(file_get_contents('php://input'), true) ?? [];
$action = trim($body['action'] ?? '');

// ════════════════════════════════════════
//  ROUTE
// ════════════════════════════════════════
switch ($action) {

    // ────────────────────────────────────
    //  SIGN UP
    // ────────────────────────────────────
    case 'signup':
        $name   = trim($body['name']     ?? '');
        $email  = strtolower(trim($body['email']    ?? ''));
        $mobile = trim($body['mobile']   ?? '');
        $pass   = $body['password'] ?? '';

        if (!$name)                                     err('Full name is required');
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) err('Enter a valid email address');
        if (!preg_match('/^\d{10}$/', $mobile))          err('Enter a valid 10-digit mobile number');
        if (strlen($pass) < 6)                          err('Password must be at least 6 characters');

        $db   = getDB();
        $chk  = $db->prepare('SELECT id FROM users WHERE email = ?');
        $chk->execute([$email]);
        if ($chk->fetch()) err('Email already registered — please login');

        $hash = password_hash($pass, PASSWORD_DEFAULT);
        $ins  = $db->prepare('INSERT INTO users (name, email, mobile, password_hash) VALUES (?, ?, ?, ?)');
        $ins->execute([$name, $email, $mobile, $hash]);

        $_SESSION['email']    = $email;
        $_SESSION['name']     = $name;
        $_SESSION['is_admin'] = false;
        $_SESSION['is_paid']  = false;

        ok(['name' => $name, 'email' => $email, 'isAdmin' => false, 'isPaid' => false]);

    // ────────────────────────────────────
    //  LOGIN
    // ────────────────────────────────────
    case 'login':
        $email = strtolower(trim($body['email']    ?? ''));
        $pass  = $body['password'] ?? '';

        if (!$email || !$pass) err('Email and password are required');

        // Admin shortcut (no DB needed)
        if ($email === ADMIN_EMAIL && $pass === ADMIN_PASS) {
            $_SESSION['email']    = ADMIN_EMAIL;
            $_SESSION['name']     = 'Admin';
            $_SESSION['is_admin'] = true;
            $_SESSION['is_paid']  = true;
            ok(['name' => 'Admin', 'email' => ADMIN_EMAIL, 'isAdmin' => true, 'isPaid' => true]);
        }

        $db   = getDB();
        $stmt = $db->prepare('SELECT id, name, password_hash, is_paid FROM users WHERE email = ?');
        $stmt->execute([$email]);
        $user = $stmt->fetch();

        if (!$user || !password_verify($pass, $user['password_hash'])) {
            err('Incorrect email or password');
        }

        $_SESSION['user_id']  = $user['id'];
        $_SESSION['email']    = $email;
        $_SESSION['name']     = $user['name'];
        $_SESSION['is_admin'] = false;
        $_SESSION['is_paid']  = (bool) $user['is_paid'];

        ok([
            'name'    => $user['name'],
            'email'   => $email,
            'isAdmin' => false,
            'isPaid'  => (bool) $user['is_paid'],
        ]);

    // ────────────────────────────────────
    //  CHECK SESSION
    // ────────────────────────────────────
    case 'check_session':
        if (empty($_SESSION['email'])) err('No active session', 401);
        ok([
            'name'    => $_SESSION['name'],
            'email'   => $_SESSION['email'],
            'isAdmin' => (bool) ($_SESSION['is_admin'] ?? false),
            'isPaid'  => (bool) ($_SESSION['is_paid']  ?? false),
        ]);

    // ────────────────────────────────────
    //  LOGOUT
    // ────────────────────────────────────
    case 'logout':
        session_unset();
        session_destroy();
        ok(['msg' => 'Logged out successfully']);

    // ────────────────────────────────────
    //  CONFIRM PAYMENT
    // ────────────────────────────────────
    case 'confirm_payment':
        if (empty($_SESSION['email'])) err('Not logged in', 401);

        $txn    = trim($body['txn']    ?? '');
        $method = trim($body['method'] ?? 'upi');

        $db   = getDB();
        $stmt = $db->prepare('UPDATE users SET is_paid = 1, paid_at = NOW(), payment_ref = ?, payment_method = ? WHERE email = ?');
        $stmt->execute([$txn, $method, $_SESSION['email']]);

        $_SESSION['is_paid'] = true;
        ok(['msg' => 'Payment confirmed! Welcome to Mana Telugu Pune!']);

    // ────────────────────────────────────
    //  GET PLACES
    //  Admin → all records
    //  Member → Active only
    // ────────────────────────────────────
    case 'get_places':
        if (empty($_SESSION['email'])) err('Not logged in', 401);

        $db      = getDB();
        $isAdmin = (bool) ($_SESSION['is_admin'] ?? false);

        if ($isAdmin) {
            $stmt = $db->query('SELECT * FROM places ORDER BY id ASC');
        } else {
            $stmt = $db->prepare('SELECT * FROM places WHERE status = ? ORDER BY id ASC');
            $stmt->execute(['Active']);
        }

        $rows   = $stmt->fetchAll();
        $places = array_map('mapPlace', $rows);
        ok(['places' => $places]);

    // ────────────────────────────────────
    //  ADD PLACE  (Admin only)
    // ────────────────────────────────────
    case 'add_place':
        if (empty($_SESSION['is_admin'])) err('Admin access required', 403);
        if (empty(trim($body['name'] ?? '')))     err('Place name is required');
        if (empty(trim($body['location'] ?? ''))) err('Location is required');

        $db    = getDB();
        $count = (int) $db->query('SELECT COUNT(*) FROM places')->fetchColumn();
        $code  = 'PLC-' . str_pad($count + 1, 3, '0', STR_PAD_LEFT);

        $stmt = $db->prepare('
            INSERT INTO places
              (place_code, name, category, subcat, location, district,
               description, rating, reviews, distance, best_time, img, status)
            VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?)
        ');
        $stmt->execute([
            $code,
            trim($body['name']     ?? ''),
            trim($body['category'] ?? 'Devotional'),
            trim($body['subcat']   ?? ''),
            trim($body['location'] ?? ''),
            trim($body['district'] ?? 'Pune'),
            trim($body['desc']     ?? ''),
            (float) ($body['rating']  ?? 0),
            (int)   ($body['reviews'] ?? 0),
            trim($body['distance'] ?? ''),
            trim($body['bestTime'] ?? ''),
            trim($body['img']      ?? ''),
            trim($body['status']   ?? 'Active'),
        ]);

        $newId = (int) $db->lastInsertId();
        $row   = $db->prepare('SELECT * FROM places WHERE id = ?');
        $row->execute([$newId]);
        ok(['place' => mapPlace($row->fetch())]);

    // ────────────────────────────────────
    //  EDIT PLACE  (Admin only)
    // ────────────────────────────────────
    case 'edit_place':
        if (empty($_SESSION['is_admin'])) err('Admin access required', 403);

        $id = (int) ($body['id'] ?? 0);
        if (!$id) err('Invalid place ID');

        $db   = getDB();
        $stmt = $db->prepare('
            UPDATE places SET
              name        = ?,
              category    = ?,
              subcat      = ?,
              location    = ?,
              district    = ?,
              description = ?,
              rating      = ?,
              reviews     = ?,
              distance    = ?,
              best_time   = ?,
              img         = ?,
              status      = ?
            WHERE id = ?
        ');
        $stmt->execute([
            trim($body['name']     ?? ''),
            trim($body['category'] ?? ''),
            trim($body['subcat']   ?? ''),
            trim($body['location'] ?? ''),
            trim($body['district'] ?? ''),
            trim($body['desc']     ?? ''),
            (float) ($body['rating']  ?? 0),
            (int)   ($body['reviews'] ?? 0),
            trim($body['distance'] ?? ''),
            trim($body['bestTime'] ?? ''),
            trim($body['img']      ?? ''),
            trim($body['status']   ?? 'Active'),
            $id,
        ]);

        $row = $db->prepare('SELECT * FROM places WHERE id = ?');
        $row->execute([$id]);
        ok(['place' => mapPlace($row->fetch())]);

    // ────────────────────────────────────
    //  DELETE PLACE  (Admin only)
    // ────────────────────────────────────
    case 'delete_place':
        if (empty($_SESSION['is_admin'])) err('Admin access required', 403);

        $id = (int) ($body['id'] ?? 0);
        if (!$id) err('Invalid place ID');

        $db = getDB();
        $db->prepare('DELETE FROM places WHERE id = ?')->execute([$id]);
        ok(['msg' => 'Place deleted successfully']);

    // ────────────────────────────────────
    //  UNKNOWN ACTION
    // ────────────────────────────────────
    default:
        err('Unknown action: ' . htmlspecialchars($action));
}
