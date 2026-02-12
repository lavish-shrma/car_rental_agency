<?php
/**
 * Login Page
 *
 * Unified login for both customers and agencies.
 * Verifies email, password, and user type.
 */
$pageTitle = 'Login';
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../includes/functions.php';

$errors = [];
$successMessage = '';

// Show success message after registration
if (isset($_GET['registered'])) {
    $successMessage = 'Registration successful! Please log in.';
}

$email = '';

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email    = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    $userType = trim($_POST['user_type'] ?? '');

    // --- Server-side Validation ---

    if ($email === '') {
        $errors[] = 'Email is required.';
    }

    if ($password === '') {
        $errors[] = 'Password is required.';
    }

    if ($userType === '' || !in_array($userType, ['customer', 'agency'])) {
        $errors[] = 'Please select a valid user type.';
    }

    // Verify credentials
    if (empty($errors)) {
        try {
            $stmt = $pdo->prepare('SELECT id, user_type, password, full_name FROM users WHERE email = ? AND user_type = ?');
            $stmt->execute([$email, $userType]);
            $user = $stmt->fetch();

            if ($user) {
                // Verify hashed password
                if (password_verify($password, $user['password'])) {
                    // Create session
                    startSession();
                    $_SESSION['user_id']   = $user['id'];
                    $_SESSION['user_type'] = $user['user_type'];
                    $_SESSION['full_name'] = $user['full_name'];

                    // Redirect based on user type
                    if ($user['user_type'] === 'customer') {
                        header('Location: /customer/available_cars.php');
                        exit;
                    } else {
                        header('Location: /agency/add_car.php');
                        exit;
                    }
                } else {
                    $errors[] = 'Invalid email or password.';
                }
            } else {
                $errors[] = 'Invalid email, password, or user type selection.';
            }
        } catch (PDOException $e) {
            error_log('Login error: ' . $e->getMessage());
            $errors[] = 'Something went wrong. Please try again.';
        }
    }
}

require_once __DIR__ . '/../includes/header.php';
?>

<div class="row justify-content-center">
    <div class="col-md-5">
        <div class="form-wrapper">
            <h2 class="page-heading text-center">Login</h2>

            <!-- Success Message -->
            <?php if ($successMessage): ?>
                <div class="alert alert-success"><?php echo escape($successMessage); ?></div>
            <?php endif; ?>

            <!-- Error Messages -->
            <?php if (!empty($errors)): ?>
                <div class="alert alert-danger">
                    <ul class="mb-0">
                        <?php foreach ($errors as $error): ?>
                            <li><?php echo escape($error); ?></li>
                        <?php endforeach; ?>
                    </ul>
                </div>
            <?php endif; ?>

            <!-- Login Form -->
            <form method="POST" action="" onsubmit="return validateLoginForm(this);">
                <div class="mb-3">
                    <label for="email" class="form-label">Email Address <span class="text-danger">*</span></label>
                    <input type="email" class="form-control" id="email" name="email"
                           value="<?php echo escape($email); ?>" required>
                </div>

                <div class="mb-3">
                    <label for="password" class="form-label">Password <span class="text-danger">*</span></label>
                    <input type="password" class="form-control" id="password" name="password" required>
                </div>

                <div class="mb-4">
                    <label class="form-label">Login As <span class="text-danger">*</span></label>
                    <div class="d-flex gap-4">
                        <div class="form-check">
                            <input class="form-check-input" type="radio" name="user_type"
                                   id="type_customer" value="customer" checked>
                            <label class="form-check-label" for="type_customer">Customer</label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="radio" name="user_type"
                                   id="type_agency" value="agency">
                            <label class="form-check-label" for="type_agency">Agency</label>
                        </div>
                    </div>
                </div>

                <button type="submit" class="btn btn-primary w-100">Login</button>
            </form>

            <p class="text-center mt-3">
                Don't have an account?
                <a href="/auth/register_customer.php">Register as Customer</a> |
                <a href="/auth/register_agency.php">Register as Agency</a>
            </p>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
