<?php
/**
 * Edit Car Page (Agency Only)
 *
 * Allows agencies to edit details of their own cars.
 * Verifies ownership before allowing edits.
 */
$pageTitle = 'Edit Car';
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../includes/functions.php';
startSession();

// Access control: must be logged in as agency
if (!isLoggedIn() || getUserType() !== 'agency') {
    header('Location: /auth/login.php');
    exit;
}

$agencyId = getUserId();
$errors = [];
$successMessage = '';

// Get car ID from URL
$carId = isset($_GET['id']) ? (int)$_GET['id'] : 0;

if ($carId <= 0) {
    header('Location: /agency/add_car.php');
    exit;
}

// Fetch car details and verify ownership
try {
    $stmt = $pdo->prepare('SELECT * FROM cars WHERE id = ? AND agency_id = ?');
    $stmt->execute([$carId, $agencyId]);
    $car = $stmt->fetch();

    if (!$car) {
        // Car not found or doesn't belong to this agency
        header('Location: /agency/add_car.php');
        exit;
    }
} catch (PDOException $e) {
    error_log('Edit car fetch error: ' . $e->getMessage());
    die('Something went wrong. Please try again later.');
}

// Pre-populate form fields
$vehicleModel    = $car['vehicle_model'];
$vehicleNumber   = $car['vehicle_number'];
$seatingCapacity = $car['seating_capacity'];
$rentPerDay      = $car['rent_per_day'];

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $vehicleModel    = trim($_POST['vehicle_model'] ?? '');
    $vehicleNumber   = trim($_POST['vehicle_number'] ?? '');
    $seatingCapacity = trim($_POST['seating_capacity'] ?? '');
    $rentPerDay      = trim($_POST['rent_per_day'] ?? '');

    // --- Server-side Validation ---

    if ($vehicleModel === '') {
        $errors[] = 'Vehicle model is required.';
    }

    if ($vehicleNumber === '') {
        $errors[] = 'Vehicle number is required.';
    }

    if ($seatingCapacity === '' || !ctype_digit($seatingCapacity) || (int)$seatingCapacity < 1) {
        $errors[] = 'Seating capacity must be a number of at least 1.';
    }

    if ($rentPerDay === '' || !is_numeric($rentPerDay) || (float)$rentPerDay <= 0) {
        $errors[] = 'Rent per day must be a positive number.';
    }

    // Check vehicle number uniqueness (exclude current car)
    if (empty($errors)) {
        try {
            $stmt = $pdo->prepare('SELECT COUNT(*) FROM cars WHERE vehicle_number = ? AND id != ?');
            $stmt->execute([$vehicleNumber, $carId]);
            if ($stmt->fetchColumn() > 0) {
                $errors[] = 'This vehicle number is already registered to another car.';
            }
        } catch (PDOException $e) {
            error_log('Edit car check error: ' . $e->getMessage());
            $errors[] = 'Something went wrong. Please try again.';
        }
    }

    // Update car
    if (empty($errors)) {
        try {
            $stmt = $pdo->prepare(
                'UPDATE cars SET vehicle_model = ?, vehicle_number = ?, seating_capacity = ?, rent_per_day = ? WHERE id = ? AND agency_id = ?'
            );
            $seatInt = (int)$seatingCapacity;
            $rentDec = (float)$rentPerDay;
            
            if ($stmt->execute([$vehicleModel, $vehicleNumber, $seatInt, $rentDec, $carId, $agencyId])) {
                header('Location: /agency/add_car.php?updated=1');
                exit;
            } else {
                $errors[] = 'Failed to update car. Please try again.';
            }
        } catch (PDOException $e) {
            error_log('Edit car update error: ' . $e->getMessage());
            $errors[] = 'Something went wrong. Please try again.';
        }
    }
}

require_once __DIR__ . '/../includes/header.php';
?>

<div class="row justify-content-center">
    <div class="col-md-6">
        <div class="form-wrapper">
            <h2 class="page-heading text-center">Edit Car</h2>

            <?php if (!empty($errors)): ?>
                <div class="alert alert-danger">
                    <ul class="mb-0">
                        <?php foreach ($errors as $error): ?>
                            <li><?php echo escape($error); ?></li>
                        <?php endforeach; ?>
                    </ul>
                </div>
            <?php endif; ?>

            <form method="POST" action="" onsubmit="return validateCarForm(this);">
                <div class="mb-3">
                    <label for="vehicle_model" class="form-label">Vehicle Model <span class="text-danger">*</span></label>
                    <input type="text" class="form-control" id="vehicle_model" name="vehicle_model"
                           value="<?php echo escape($vehicleModel); ?>" required>
                </div>

                <div class="mb-3">
                    <label for="vehicle_number" class="form-label">Vehicle Number <span class="text-danger">*</span></label>
                    <input type="text" class="form-control" id="vehicle_number" name="vehicle_number"
                           value="<?php echo escape($vehicleNumber); ?>" required>
                </div>

                <div class="mb-3">
                    <label for="seating_capacity" class="form-label">Seating Capacity <span class="text-danger">*</span></label>
                    <input type="number" class="form-control" id="seating_capacity" name="seating_capacity"
                           value="<?php echo escape($seatingCapacity); ?>" min="1" required>
                </div>

                <div class="mb-3">
                    <label for="rent_per_day" class="form-label">Rent Per Day (₹) <span class="text-danger">*</span></label>
                    <input type="number" class="form-control" id="rent_per_day" name="rent_per_day"
                           value="<?php echo escape($rentPerDay); ?>" min="0.01" step="0.01" required>
                </div>

                <div class="d-flex gap-2">
                    <button type="submit" class="btn btn-primary flex-fill">Update Car</button>
                    <a href="/agency/add_car.php" class="btn btn-secondary flex-fill">Cancel</a>
                </div>
            </form>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
