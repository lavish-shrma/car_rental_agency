<?php
/**
 * Add New Car Page (Agency Only)
 *
 * Allows agencies to add cars to their inventory.
 * Displays a list of the agency's existing cars below the form.
 */
$pageTitle = 'Add New Car';
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

// Show success message if redirected from edit_car.php
if (isset($_GET['updated'])) {
    $successMessage = 'Car updated successfully!';
}

// Form field values
$vehicleModel    = '';
$vehicleNumber   = '';
$seatingCapacity = '';
$rentPerDay      = '';

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

    // Check vehicle number uniqueness
    if (empty($errors)) {
        $stmt = $conn->prepare('SELECT id FROM cars WHERE vehicle_number = ?');
        $stmt->bind_param('s', $vehicleNumber);
        $stmt->execute();
        $stmt->store_result();
        if ($stmt->num_rows > 0) {
            $errors[] = 'This vehicle number is already registered.';
        }
        $stmt->close();
    }

    // Insert car
    if (empty($errors)) {
        $stmt = $conn->prepare(
            'INSERT INTO cars (agency_id, vehicle_model, vehicle_number, seating_capacity, rent_per_day) VALUES (?, ?, ?, ?, ?)'
        );
        $seatInt = (int)$seatingCapacity;
        $rentDec = (float)$rentPerDay;
        $stmt->bind_param('issid', $agencyId, $vehicleModel, $vehicleNumber, $seatInt, $rentDec);

        if ($stmt->execute()) {
            $successMessage = 'Car added successfully!';
            // Clear form fields
            $vehicleModel = '';
            $vehicleNumber = '';
            $seatingCapacity = '';
            $rentPerDay = '';
        } else {
            $errors[] = 'Failed to add car. Please try again.';
        }
        $stmt->close();
    }
}

// Fetch agency's existing cars
$stmt = $conn->prepare('SELECT * FROM cars WHERE agency_id = ? ORDER BY created_at DESC');
$stmt->bind_param('i', $agencyId);
$stmt->execute();
$carsResult = $stmt->get_result();

require_once __DIR__ . '/../includes/header.php';
?>

<h2 class="page-heading">Add New Car</h2>

<div class="row">
    <!-- Add Car Form -->
    <div class="col-md-6 mb-4">
        <div class="form-wrapper">
            <?php if ($successMessage): ?>
                <div class="alert alert-success"><?php echo escape($successMessage); ?></div>
            <?php endif; ?>

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

                <button type="submit" class="btn btn-primary w-100">Add Car</button>
            </form>
        </div>
    </div>

    <!-- Agency's Existing Cars -->
    <div class="col-md-6">
        <h4>Your Cars</h4>
        <?php if ($carsResult->num_rows === 0): ?>
            <p class="text-muted">You haven't added any cars yet.</p>
        <?php else: ?>
            <div class="table-responsive">
                <table class="table table-striped">
                    <thead>
                        <tr>
                            <th>Model</th>
                            <th>Number</th>
                            <th>Seats</th>
                            <th>Rent/Day</th>
                            <th>Status</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while ($car = $carsResult->fetch_assoc()): ?>
                            <tr>
                                <td><?php echo escape($car['vehicle_model']); ?></td>
                                <td><?php echo escape($car['vehicle_number']); ?></td>
                                <td><?php echo (int)$car['seating_capacity']; ?></td>
                                <td><?php echo formatCurrency($car['rent_per_day']); ?></td>
                                <td>
                                    <?php if ($car['is_available']): ?>
                                        <span class="badge bg-success">Available</span>
                                    <?php else: ?>
                                        <span class="badge bg-secondary">Booked</span>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <a href="/agency/edit_car.php?id=<?php echo (int)$car['id']; ?>"
                                       class="btn btn-sm btn-warning">Edit</a>
                                </td>
                            </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            </div>
        <?php endif; ?>
    </div>
</div>

<?php
$stmt->close();
require_once __DIR__ . '/../includes/footer.php';
?>
