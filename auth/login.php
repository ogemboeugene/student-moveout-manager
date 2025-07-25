<?php
require_once '../includes/init.php';

// Redirect if already logged in
if (Auth::isLoggedIn()) {
    $user = Auth::getUser();
    $redirectUrl = $user['role'] === 'dean' ? '../admin/' : '../teacher/';
    header("Location: $redirectUrl");
    exit;
}

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = Validator::sanitizeInput($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';
    $csrf_token = $_POST['csrf_token'] ?? '';
    
    // Validate CSRF token
    if (!Auth::validateCSRFToken($csrf_token)) {
        $error = 'Invalid security token. Please try again.';
    } else {
        // Validate input
        if (empty($username) || empty($password)) {
            $error = 'Please enter both username and password.';
        } else {
            // Attempt login
            if (Auth::login($username, $password)) {
                $user = Auth::getUser();
                $redirectUrl = $user['role'] === 'dean' ? '../admin/' : '../teacher/';
                header("Location: $redirectUrl");
                exit;
            } else {
                $error = 'Invalid username or password.';
            }
        }
    }
}

$pageTitle = 'Login';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="<?php echo Auth::generateCSRFToken(); ?>">
    <title><?php echo $pageTitle; ?> - Student Move-Out Manager</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="../assets/css/style.css" rel="stylesheet">
</head>
<body>
    <div class="login-container">
        <div class="login-card">
            <div class="login-header">
                <h1>Student Move-Out Manager</h1>
                <p>Please sign in to continue</p>
            </div>
            
            <?php if ($error): ?>
                <div class="alert alert-danger" role="alert">
                    <?php echo htmlspecialchars($error); ?>
                </div>
            <?php endif; ?>
            
            <form method="POST" data-validate>
                <input type="hidden" name="csrf_token" value="<?php echo Auth::generateCSRFToken(); ?>">
                
                <div class="form-group mb-3">
                    <label for="username" class="form-label">Username</label>
                    <input type="text" 
                           class="form-control" 
                           id="username" 
                           name="username" 
                           value="<?php echo htmlspecialchars($username ?? ''); ?>"
                           required 
                           autocomplete="username"
                           autofocus>
                </div>
                
                <div class="form-group mb-4">
                    <label for="password" class="form-label">Password</label>
                    <input type="password" 
                           class="form-control" 
                           id="password" 
                           name="password" 
                           required 
                           autocomplete="current-password">
                </div>
                
                <button type="submit" class="btn btn-primary w-100">
                    Sign In
                </button>
            </form>
            
            <div class="mt-4 text-center">
                <small class="text-muted">
                    <strong>Demo Accounts:</strong><br>
                    Admin: admin / admin123<br>
                    Teacher: teacher1 / teacher123
                </small>
            </div>
            
            <div class="mt-3 text-center">
                <a href="../display/" class="btn btn-outline-secondary btn-sm">
                    View Public Display
                </a>
            </div>
        </div>
    </div>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="../assets/js/main.js"></script>
</body>
</html>
