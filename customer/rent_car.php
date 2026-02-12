<?php
/**
 * Rent Car Handler (Customer Only)
 *
 * Processes the rent form submission:
 * - Validates inputs
 * - Calculates end_date and total_cost
 * - Inserts booking record
 * - Updates car availability to false
 */
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../includes/functions.php';
startSession();

// Access control: must be logged in
if (!isLoggedIn()) {
    header('Location: /auth/login.php');
    exit;
}

// Access control: must be a customer
if (getUserType() !== 'customer') {
    // Agencies cannot rent cars
    $pageTitle = 'Error';
    require_once __DIR__ . '/../includes/header.php';
    echo '<div class="alert alert-danger">Agencies cannot rent cars.</div>';
    echo '<a href="/customer/available_cars.php" class="btn btn-primary">Back to Available Cars</a>';
    require_once __DIR__ . '/../includes/footer.php';
    exit;
}

// Only process POST requests
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: /customer/available_cars.php');
    exit;
}

$customerId   = getUserId();
$carId        = isset($_POST['car_id']) ? (int)$_POST['car_id'] : 0;
$startDate    = trim($_POST['start_date'] ?? '');
$numberOfDays = isset($_POST['number_of_days']) ? (int)$_POST['number_of_days'] : 0;

$errors = [];

// --- Server-side Validation ---

if ($carId <= 0) {
    $errors[] = 'Invalid car selected.';
}

if ($startDate === '') {
    $errors[] = 'Start date is required.';
} else {
    $today = date('Y-m-d');
    if ($startDate < $today) {
        $errors[] = 'Start date cannot be in the past.';
    }
}

if ($numberOfDays < 1 || $numberOfDays > 30) {
    $errors[] = 'Number of days must be between 1 and 30.';
}

// Verify car exists and is still available
$rentPerDay = 0;
if (empty($errors)) {
    try {
        $stmt = $pdo->prepare('SELECT id, rent_per_day, is_available FROM cars WHERE id = ?');
        $stmt->execute([$carId]);
        $carData = $stmt->fetch();

        if (!$carData) {
            $errors[] = 'Car not found.';
        } else {
            if (!$carData['is_available']) {
                $errors[] = 'This car is no longer available.';
            }
            $rentPerDay = (float)$carData['rent_per_day'];
        }
    } catch (PDOException $e) {
        $errors[] = 'Database error: ' . $e->getMessage();
    }
}

// If there are errors, show them
if (!empty($errors)) {
    $pageTitle = 'Booking Error';
    require_once __DIR__ . '/../includes/header.php';
    ?>
    <div class="alert alert-danger">
        <h5>Booking Failed</h5>
        <ul class="mb-0">
            <?php foreach ($errors as $error): ?>
                <li><?php echo escape($error); ?></li>
            <?php endforeach; ?>
        </ul>
    </div>
    <a href="/customer/available_cars.php" class="btn btn-primary">Back to Available Cars</a>
    <?php
    require_once __DIR__ . '/../includes/footer.php';
    exit;
}

// Calculate end_date and total_cost
$endDate   = date('Y-m-d', strtotime($startDate . ' + ' . $numberOfDays . ' days'));
$totalCost = $rentPerDay * $numberOfDays;

// Insert booking record and update car availability (Transaction)
try {
    $pdo->beginTransaction();

    // 1. Insert booking
    $stmt = $pdo->prepare(
        'INSERT INTO bookings (car_id, customer_id, start_date, number_of_days, end_date, total_cost)
         VALUES (?, ?, ?, ?, ?, ?)'
    );
    $stmt->execute([$carId, $customerId, $startDate, $numberOfDays, $endDate, $totalCost]);

    // 2. Update car availability
    $stmt = $pdo->prepare('UPDATE cars SET is_available = 0 WHERE id = ?');
    $stmt->execute([$carId]);

    $pdo->commit();

    // Redirect with success indicator
    header('Location: /customer/available_cars.php?rented=1');
    exit;

} catch (PDOException $e) {
    if ($pdo->inTransaction()) {
        $pdo->rollBack();
    }
    $pageTitle = 'Booking Error';
    require_once __DIR__ . '/../includes/header.php';
    echo '<div class="alert alert-danger">Failed to create booking: ' . escape($e->getMessage()) . '</div>';
    echo '<a href="/customer/available_cars.php" class="btn btn-primary">Back to Available Cars</a>';
    require_once __DIR__ . '/../includes/footer.php';
    exit;
}
