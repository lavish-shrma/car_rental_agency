<?php
/**
 * View Bookings Page (Agency Only)
 *
 * Displays all bookings for cars owned by the logged-in agency.
 * Uses JOIN across bookings, cars, and users tables.
 */
$pageTitle = 'View Bookings';
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../includes/functions.php';
startSession();

// Access control: must be logged in as agency
if (!isLoggedIn() || getUserType() !== 'agency') {
    header('Location: /auth/login.php');
    exit;
}

$agencyId = getUserId();

// Fetch bookings for this agency's cars
$stmt = $conn->prepare(
    'SELECT
        bookings.id AS booking_id,
        bookings.start_date,
        bookings.number_of_days,
        bookings.end_date,
        bookings.total_cost,
        bookings.booking_status,
        bookings.created_at AS booking_date,
        cars.vehicle_model,
        cars.vehicle_number,
        users.full_name AS customer_name,
        users.email AS customer_email,
        users.phone_number AS customer_phone
    FROM bookings
    INNER JOIN cars ON bookings.car_id = cars.id
    INNER JOIN users ON bookings.customer_id = users.id
    WHERE cars.agency_id = ?
    ORDER BY bookings.created_at DESC'
);
$stmt->bind_param('i', $agencyId);
$stmt->execute();
$bookingsResult = $stmt->get_result();

require_once __DIR__ . '/../includes/header.php';
?>

<h2 class="page-heading">Bookings for Your Cars</h2>

<?php if ($bookingsResult->num_rows === 0): ?>
    <div class="alert alert-info">No bookings found for your cars yet.</div>
<?php else: ?>
    <div class="table-responsive">
        <table class="table table-striped">
            <thead>
                <tr>
                    <th>Customer Name</th>
                    <th>Customer Email</th>
                    <th>Customer Phone</th>
                    <th>Vehicle Model</th>
                    <th>Vehicle Number</th>
                    <th>Start Date</th>
                    <th>Days</th>
                    <th>End Date</th>
                    <th>Total Cost</th>
                    <th>Status</th>
                    <th>Booking Date</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($booking = $bookingsResult->fetch_assoc()): ?>
                    <tr>
                        <td><?php echo escape($booking['customer_name']); ?></td>
                        <td><?php echo escape($booking['customer_email']); ?></td>
                        <td><?php echo escape($booking['customer_phone']); ?></td>
                        <td><?php echo escape($booking['vehicle_model']); ?></td>
                        <td><?php echo escape($booking['vehicle_number']); ?></td>
                        <td><?php echo formatDate($booking['start_date']); ?></td>
                        <td><?php echo (int)$booking['number_of_days']; ?></td>
                        <td><?php echo formatDate($booking['end_date']); ?></td>
                        <td><?php echo formatCurrency($booking['total_cost']); ?></td>
                        <td>
                            <?php
                            $statusClass = 'bg-primary';
                            if ($booking['booking_status'] === 'completed') $statusClass = 'bg-success';
                            if ($booking['booking_status'] === 'cancelled') $statusClass = 'bg-danger';
                            ?>
                            <span class="badge <?php echo $statusClass; ?>">
                                <?php echo escape(ucfirst($booking['booking_status'])); ?>
                            </span>
                        </td>
                        <td><?php echo formatDate($booking['booking_date']); ?></td>
                    </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    </div>
<?php endif; ?>

<?php
$stmt->close();
require_once __DIR__ . '/../includes/footer.php';
?>
