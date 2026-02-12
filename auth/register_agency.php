<?php
/**
 * Agency Registration Page
 *
 * Allows new agencies to create an account.
 * Includes company_name field in addition to standard fields.
 */
$pageTitle = 'Agency Registration';
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../includes/functions.php';

$errors = [];

// Form field values (to repopulate on error)
$companyName = '';
$fullName    = '';
$email       = '';
$phoneNumber = '';

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $companyName     = trim($_POST['company_name'] ?? '');
    $fullName        = trim($_POST['full_name'] ?? '');
    $email           = trim($_POST['email'] ?? '');
    $phoneNumber     = trim($_POST['phone_number'] ?? '');
    $password        = $_POST['password'] ?? '';
    $confirmPassword = $_POST['confirm_password'] ?? '';

    // --- Server-side Validation ---

    if ($companyName === '') {
        $errors[] = 'Company name is required.';
    }

    if ($fullName === '') {
        $errors[] = 'Full name is required.';
    }

    if ($email === '') {
        $errors[] = 'Email is required.';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = 'Please enter a valid email address.';
    }

    if ($phoneNumber === '') {
        $errors[] = 'Phone number is required.';
    }

    if ($password === '') {
        $errors[] = 'Password is required.';
    } elseif (strlen($password) < 8) {
        $errors[] = 'Password must be at least 8 characters.';
    }

    if ($confirmPassword === '') {
        $errors[] = 'Please confirm your password.';
    } elseif ($password !== $confirmPassword) {
        $errors[] = 'Passwords do not match.';
    }

    // Check duplicate email
    if (empty($errors)) {
        try {
            $stmt = $pdo->prepare('SELECT COUNT(*) FROM users WHERE email = ?');
            $stmt->execute([$email]);
            if ($stmt->fetchColumn() > 0) {
                $errors[] = 'An account with this email already exists.';
            }
        } catch (PDOException $e) {
            $errors[] = 'Database error: ' . $e->getMessage();
        }
    }

    // Insert into database
    if (empty($errors)) {
        try {
            $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
            $userType = 'agency';

            $stmt = $pdo->prepare(
                'INSERT INTO users (user_type, email, password, full_name, phone_number, company_name) VALUES (?, ?, ?, ?, ?, ?)'
            );
            
            if ($stmt->execute([$userType, $email, $hashedPassword, $fullName, $phoneNumber, $companyName])) {
                header('Location: /auth/login.php?registered=1');
                exit;
            } else {
                $errors[] = 'Registration failed. Please try again.';
            }
        } catch (PDOException $e) {
            $errors[] = 'Database error: ' . $e->getMessage();
        }
    }
}

require_once __DIR__ . '/../includes/header.php';
?>

<div class="row justify-content-center">
    <div class="col-md-6">
        <div class="form-wrapper">
            <h2 class="page-heading text-center">Agency Registration</h2>

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

            <!-- Registration Form -->
            <form method="POST" action="" onsubmit="return validateRegistrationForm(this, true);">
                <div class="mb-3">
                    <label for="company_name" class="form-label">Company Name <span class="text-danger">*</span></label>
                    <input type="text" class="form-control" id="company_name" name="company_name"
                           value="<?php echo escape($companyName); ?>" required>
                </div>

                <div class="mb-3">
                    <label for="full_name" class="form-label">Full Name <span class="text-danger">*</span></label>
                    <input type="text" class="form-control" id="full_name" name="full_name"
                           value="<?php echo escape($fullName); ?>" required>
                </div>

                <div class="mb-3">
                    <label for="email" class="form-label">Email Address <span class="text-danger">*</span></label>
                    <input type="email" class="form-control" id="email" name="email"
                           value="<?php echo escape($email); ?>" required>
                </div>

                <div class="mb-3">
                    <label for="phone_number" class="form-label">Phone Number <span class="text-danger">*</span></label>
                    <input type="text" class="form-control" id="phone_number" name="phone_number"
                           value="<?php echo escape($phoneNumber); ?>" required>
                </div>

                <div class="mb-3">
                    <label for="password" class="form-label">Password <span class="text-danger">*</span></label>
                    <input type="password" class="form-control" id="password" name="password"
                           minlength="8" required>
                    <div class="form-text">Minimum 8 characters.</div>
                </div>

                <div class="mb-3">
                    <label for="confirm_password" class="form-label">Confirm Password <span class="text-danger">*</span></label>
                    <input type="password" class="form-control" id="confirm_password" name="confirm_password"
                           minlength="8" required>
                </div>

                <button type="submit" class="btn btn-primary w-100">Register</button>
            </form>

            <p class="text-center mt-3">
                Already have an account? <a href="/auth/login.php">Login here</a>
            </p>
            <p class="text-center">
                Register as Customer? <a href="/auth/register_customer.php">Click here</a>
            </p>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
