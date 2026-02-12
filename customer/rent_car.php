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
    $stmt = $conn->prepare('SELECT id, rent_per_day, is_available FROM cars WHERE id = ?');
    $stmt->bind_param('i', $carId);
    $stmt->execute();
    $carResult = $stmt->get_result();

    if ($carResult->num_rows === 0) {
        $errors[] = 'Car not found.';
    } else {
        $carData = $carResult->fetch_assoc();
        if (!$carData['is_available']) {
            $errors[] = 'This car is no longer available.';
        }
        $rentPerDay = (float)$carData['rent_per_day'];
    }
    $stmt->close();
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

// Insert booking record
$stmt = $conn->prepare(
    'INSERT INTO bookings (car_id, customer_id, start_date, number_of_days, end_date, total_cost)
     VALUES (?, ?, ?, ?, ?, ?)'
);
$stmt->bind_param('iisisd', $carId, $customerId, $startDate, $numberOfDays, $endDate, $totalCost);

if ($stmt->execute()) {
    $stmt->close();

    // Update car availability to false
    $stmt = $conn->prepare('UPDATE cars SET is_available = 0 WHERE id = ?');
    $stmt->bind_param('i', $carId);
    $stmt->execute();
    $stmt->close();

    // Redirect with success indicator
    header('Location: /customer/available_cars.php?rented=1');
    exit;
} else {
    $stmt->close();
    $pageTitle = 'Booking Error';
    require_once __DIR__ . '/../includes/header.php';
    echo '<div class="alert alert-danger">Failed to create booking. Please try again.</div>';
    echo '<a href="/customer/available_cars.php" class="btn btn-primary">Back to Available Cars</a>';
    require_once __DIR__ . '/../includes/footer.php';
    exit;
}
