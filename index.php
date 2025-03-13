<?php
session_start();

// If already logged in, redirect to appropriate page
if (isset($_SESSION['user_id'])) {
    header("Cache-Control: no-cache, no-store, must-revalidate");
    header("Pragma: no-cache");
    header("Expires: 0");
    
    switch($_SESSION['role']) {
        case 'admin':
            header('Location: admin.php');
            break;
        case 'faculty':
            header('Location: faculty.php');
            break;
        case 'student':
            header('Location: student.php');
            break;
    }
    exit();
}

// Check if this is a logout or session expiry
$message = '';
if (isset($_GET['session']) && $_GET['session'] === 'expired') {
    $message = 'Your session has expired. Please log in again.';
} else if (isset($_GET['logout']) && $_GET['logout'] === 'success') {
    $message = 'You have been successfully logged out.';
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>RCO Connect - Login</title>
    
</head>
<body>
    <div class="login-container">
        <div class="login-header">
            <h1>RCO Connect</h1>
            <p>Login to your RCO Connect account</p>
        </div>
        
        <?php if ($message): ?>
        <div class="message show"><?php echo htmlspecialchars($message); ?></div>
        <?php endif; ?>
        
        <div id="error-message"></div>
        
        <form onsubmit="handleLogin(event)">
            <div class="form-group">
                <input type="email" name="email" id="email" required placeholder="Email">
            </div>
            <div class="form-group">
                <input type="password" name="password" id="password" required placeholder="Password">
            </div>
            <button type="submit">Login</button>
        </form>
        
        <div class="register-link">
            Don't have an account? <a href="register.php">Register here</a>
        </div>
    </div>

    <script>
    // Show message if it exists in URL parameters
    window.onload = function() {
        const urlParams = new URLSearchParams(window.location.search);
        const message = document.querySelector('.message');
        if (message) {
            message.classList.add('show');
            setTimeout(() => {
                message.classList.remove('show');
            }, 5000);
        }
    }

    async function handleLogin(e) {
        e.preventDefault();
        
        const form = e.target;
        const formData = new FormData(form);
        formData.append('action', 'login');

        const errorMessage = document.getElementById('error-message');
        const submitButton = form.querySelector('button[type="submit"]');
        const originalButtonText = submitButton.textContent;

        try {
            errorMessage.style.display = 'none';
            submitButton.disabled = true;
            submitButton.textContent = 'Logging in...';

            const response = await fetch('includes/auth.php', {
                method: 'POST',
                body: formData
            });
            
            const data = await response.json();
            
            if (data.success) {
                submitButton.textContent = 'Redirecting...';
                switch (data.role) {
                    case 'admin':
                        window.location.href = 'admin.php';
                        break;
                    case 'faculty':
                        window.location.href = 'faculty.php';
                        break;
                    case 'student':
                        window.location.href = 'student.php';
                        break;
                    default:
                        throw new Error('Unknown user role');
                }
            } else {
                throw new Error(data.error || 'Login failed');
            }
        } catch (error) {
            console.error('Login error:', error);
            errorMessage.textContent = error.message || 'An error occurred during login';
            errorMessage.style.display = 'block';
        } finally {
            submitButton.disabled = false;
            submitButton.textContent = originalButtonText;
        }
    }
    </script>
</body>
</html>
  </rewritten_file> 