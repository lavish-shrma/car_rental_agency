<?php
/**
 * Available Cars Page (Public)
 *
 * Displays all available cars.
 * Conditional rendering:
 *   - Guest: "Login to Rent" button
 *   - Customer: rent form (days, start date, rent button)
 *   - Agency: no rent controls
 */
$pageTitle = 'Available Cars';
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../includes/functions.php';
startSession();

// Check for success message from rent_car.php
$successMessage = '';
if (isset($_GET['rented'])) {
    $successMessage = 'Car booked successfully!';
}
if (isset($_GET['updated'])) {
    $successMessage = 'Car updated successfully!';
}

// Fetch all available cars
$query = 'SELECT cars.*, users.company_name
          FROM cars
          INNER JOIN users ON cars.agency_id = users.id
          WHERE cars.is_available = 1
          ORDER BY cars.created_at DESC';
$result = $conn->query($query);

require_once __DIR__ . '/../includes/header.php';
?>

<h2 class="page-heading">Available Cars</h2>

<?php if ($successMessage): ?>
    <div class="alert alert-success"><?php echo escape($successMessage); ?></div>
<?php endif; ?>

<?php if (isLoggedIn() && getUserType() === 'agency'): ?>
    <div class="alert alert-info">You are logged in as an agency. Agencies cannot rent cars.</div>
<?php endif; ?>

<?php if ($result->num_rows === 0): ?>
    <div class="alert alert-info">No cars are currently available.</div>
<?php else: ?>
    <div class="row">
        <?php while ($car = $result->fetch_assoc()): ?>
            <div class="col-md-4 col-sm-6 mb-4">
                <div class="card car-card h-100">
                    <div class="card-body d-flex flex-column">
                        <h5 class="car-model"><?php echo escape($car['vehicle_model']); ?></h5>

                        <div class="car-detail">
                            <i class="bi bi-hash"></i>
                            <span><?php echo escape($car['vehicle_number']); ?></span>
                        </div>
                        <div class="car-detail">
                            <i class="bi bi-people"></i>
                            <span><?php echo (int)$car['seating_capacity']; ?> Seats</span>
                        </div>
                        <div class="car-detail">
                            <i class="bi bi-building"></i>
                            <span><?php echo escape($car['company_name']); ?></span>
                        </div>

                        <div class="car-price"><?php echo formatCurrency($car['rent_per_day']); ?> / day</div>

                        <div class="mt-auto pt-3">
                            <?php if (!isLoggedIn()): ?>
                                <!-- Guest: show login button -->
                                <a href="/auth/login.php" class="btn btn-primary w-100">Login to Rent</a>

                            <?php elseif (getUserType() === 'customer'): ?>
                                <!-- Customer: show rent form -->
                                <form method="POST" action="/customer/rent_car.php"
                                      onsubmit="return validateRentForm(this);">
                                    <input type="hidden" name="car_id" value="<?php echo (int)$car['id']; ?>">

                                    <div class="mb-2">
                                        <label class="form-label small">Start Date</label>
                                        <input type="date" class="form-control form-control-sm"
                                               name="start_date" required
                                               min="<?php echo date('Y-m-d'); ?>">
                                    </div>

                                    <div class="mb-2">
                                        <label class="form-label small">Number of Days</label>
                                        <select class="form-select form-select-sm" name="number_of_days" required>
                                            <option value="">Select days</option>
                                            <?php for ($i = 1; $i <= 30; $i++): ?>
                                                <option value="<?php echo $i; ?>"><?php echo $i; ?> day<?php echo $i > 1 ? 's' : ''; ?></option>
                                            <?php endfor; ?>
                                        </select>
                                    </div>

                                    <button type="submit" class="btn btn-success w-100">Rent Car</button>
                                </form>

                            <?php endif; ?>
                            <!-- Agency: no rent controls shown -->
                        </div>
                    </div>
                </div>
            </div>
        <?php endwhile; ?>
    </div>
<?php endif; ?>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
