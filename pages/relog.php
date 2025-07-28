<?php
ob_start();
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
error_reporting(E_ALL);

$auth_user = new USER();

if (isset($_GET['msg']) && $_GET['msg'] === 'signin_required') {
    echo '<div class="alert alert-warning text-center mt-5 pt-4 mb-0">Sign In Required!</div>';
}

$register_msg = '';
$login_msg = '';

// Fetch user types for select options
$userTypes = [];
try {
    $stmt = $auth_user->runQuery("SELECT id, type_name FROM user_types");
    $stmt->execute();
    $userTypes = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $ex) {
    $userTypes = [];
}

// Registration
if (isset($_POST['signup'])) {
    $uname = trim($_POST['uname']);
    $fname = trim($_POST['fname']);
    $contact = trim($_POST['contact']);
    $email = trim($_POST['email']);
    $upass = trim($_POST['password']);
    $user_type_id = intval($_POST['user_type_id']);

    $stmt = $auth_user->runQuery("SELECT * FROM users WHERE user_email= :email_id");
    $stmt->execute(array(':email_id' => $email));

    if ($stmt->rowCount() > 0) {
        $register_msg = "<div class='alert alert-warning'>This email is already registered.</div>";
    } else {
        if ($auth_user->register($uname, $fname, $contact, $email, $upass, $user_type_id)) {
            $register_msg = "<div class='alert alert-success'>Registration Successful!</div>";
        } else {
            $register_msg = "<div class='alert alert-warning'>Registration Not Successful!</div>";
        }
    }
}

// Login
if (isset($_POST['signin'])) {
    $email = trim($_POST['email']);
    $upass = trim($_POST['password']);

    $result = $auth_user->login($email, $upass);

    if ($result === true) {
        header('location: index.php?page=dashboard');
        exit();
    } elseif ($result === 'inactive') {
        $login_msg = "<div class='alert alert-warning text-center'>This account is not active.</div>";
    } elseif ($result === 'invalid') {
        $login_msg = "<div class='alert alert-warning text-center'>Invalid email or password.</div>";
    } else {
        $login_msg = "<div class='alert alert-warning text-center'>Account not found.</div>";
    }
}
?>


<!-- Sign in & Sign up -->
  <div class="container-fluid d-flex justify-content-center">
    <div class="form-container mt-5 pt-4" style="width: 500px;">
      <div class="card shadow mb-3">
        <div class="card-body p-4">
      <!-- Toggle Buttons -->
      <div class="text-center mb-2 d-flex justify-content-between">
        <button id="loginBtn" class="btn btn-outline-secondary active flex-fill me-2 mr-2">
          <i class="fas fa-sign-in-alt"></i> Sign In
        </button>
        <button id="signupBtn" class="btn btn-outline-secondary flex-fill">
          <i class="fas fa-user-plus"></i> Sign Up
        </button>
      </div>
      <?= $register_msg ?>
      <?= $login_msg ?>


      <!-- Login Form -->
      <form id="loginForm" class="form" method="POST" action="">
        <h2 class="text-center mb-5 pt-2">Sign In</h2>
        <div class="mb-3">
          <label for="loginEmail" class="form-label">E-mail:</label>
          <input type="email" class="form-control" name="email" id="loginEmail" placeholder="Enter your email" required>
        </div>
        <div class="mb-3">
          <label for="loginPassword" class="form-label">Password</label>
          <input type="password" class="form-control" name="password" id="loginPassword" placeholder="Enter your password" required>
        </div>
        <div class="mb-3 text-end">
          <a href="fpass.php" class="text-decoration-none">Forgot Password?</a>
        </div>
        <div class="d-grid mb-3">
          <button type="submit" name="signin" class="btn btn-outline-secondary">Sign In</button>
        </div>
      </form>

      <!-- Signup Form -->
      <form id="signupForm" class="form d-none" method="POST" action="">
        <h2 class="text-center mb-4">Sign Up</h2>
        <div class="mb-3">
          <label for="uname" class="form-label">User Name:</label>
          <input type="text" class="form-control" name="uname" id="uname" placeholder="Enter your user name" required>
        </div>
        <div class="mb-3">
          <label for="fname" class="form-label">Full Name:</label>
          <input type="text" class="form-control" name="fname" id="fname" placeholder="Enter your full name" required>
        </div>
        <div class="mb-3">
          <label for="contact" class="form-label">Contact:</label>
          <input type="text" class="form-control" name="contact" id="contact" placeholder="Enter your contact number" required>
        </div>
        <div class="mb-3">
          <label for="email" class="form-label">E-mail:</label>
          <input type="email" class="form-control" name="email" id="email" placeholder="Enter your email" required>
        </div>
        <div class="mb-3">
          <label for="password" class="form-label">Password:</label>
          <input type="password" class="form-control" name="password" id="password" placeholder="Enter your password" required>
        </div>
        <div class="mb-3">
          <label for="user_type_id" class="form-label">User Type:</label>
          <select class="form-control" name="user_type_id" id="user_type_id" required>
            <option value="">Select user type</option>
            <?php foreach ($userTypes as $type): ?>
              <option value="<?= htmlspecialchars($type['id']) ?>"><?= htmlspecialchars(ucfirst($type['type_name'])) ?></option>
            <?php endforeach; ?>
          </select>
        </div>
        <div class="d-grid mb-3">
          <button type="submit" name="signup" class="btn btn-outline-secondary">Sign Up</button>
        </div>
      </form>
        </div>
    </div>
  </div>


  <!-- Bootstrap CSS -->
  <!-- <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet"> -->
  
  <!-- Bootstrap JS and Dependencies -->
  <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.6/dist/umd/popper.min.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.min.js"></script>

  <!-- Custom JS -->
  <script src="assets/js/relog.js"></script>

  <script>
    // Toggle between Login and Signup forms
    const loginBtn = document.getElementById('loginBtn');
    const signupBtn = document.getElementById('signupBtn');
    const loginForm = document.getElementById('loginForm');
    const signupForm = document.getElementById('signupForm');

    loginBtn.addEventListener('click', () => {
      loginForm.classList.remove('d-none');
      signupForm.classList.add('d-none');
      loginBtn.classList.add('active');
      signupBtn.classList.remove('active');
    });

    signupBtn.addEventListener('click', () => {
      signupForm.classList.remove('d-none');
      loginForm.classList.add('d-none');
      signupBtn.classList.add('active');
      loginBtn.classList.remove('active');
    });
  </script>
