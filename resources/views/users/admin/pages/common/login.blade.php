
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Admin Login</title>

    {{-- Bootstrap 5 --}}
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css"
          rel="stylesheet" crossorigin="anonymous">

    {{-- Font Awesome --}}
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css"
          rel="stylesheet" referrerpolicy="no-referrer" crossorigin="anonymous">

    {{-- Horizon Allienz colour variables + shared styles --}}
    <link rel="stylesheet" href="{{ asset('css/common/main.css') }}">
    {{-- Page-specific styles --}}
    <link rel="stylesheet" href="{{ asset('css/common/login.css') }}">

    {{-- CSRF token for fetch() --}}
    <meta name="csrf-token" content="{{ csrf_token() }}">
</head>
<body class="bg-light">
<div class="container">
    <div class="row justify-content-center align-items-center min-vh-100">
        <div class="col-md-6">
            <div class="card shadow-sm">
                <div class="card-body p-5">
                    {{-- logo + heading --}}
                    <div class="text-center mb-4">
                        <img src="{{ asset('assets/images/web_assets/logo.jpg') }}"
                             alt="Hospital APP" width="100" class="mb-3">
                        <h2 class="h4 fw-bold">Admin Portal Login</h2>
                    </div>

                    {{-- login form --}}
                    <form id="loginForm">
                        <div class="mb-3">
                            <label for="email" class="form-label">
                                <i class="fas fa-envelope me-2 text-primary"></i>Email address
                            </label>
                            <input type="email" class="form-control" id="email" name="email"
                                   placeholder="admin@example.com" required>
                        </div>

                        <div class="mb-3">
                            <label for="password" class="form-label">
                                <i class="fas fa-lock me-2 text-primary"></i>Password
                            </label>
                            <div class="input-group">
                                <input type="password" class="form-control" id="password"
                                       name="password" placeholder="********" required>
                                <span class="input-group-text" id="togglePassword">
                                    <i class="fas fa-eye"></i>
                                </span>
                            </div>
                        </div>

                        <div class="d-flex justify-content-between align-items-center mb-4">
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox"
                                       id="rememberMe" name="remember">
                                <label class="form-check-label" for="rememberMe">
                                    Remember me
                                </label>
                            </div>
                            <a href="#" class="link-primary">Forgot password?</a>
                        </div>

                        <button type="submit" class="btn btn-primary w-100">
                            <i class="fas fa-sign-in-alt me-2"></i>Log In
                        </button>

                        <div class="text-center mt-3">
                            <a href="/" class="btn btn-outline-secondary btn-back">
                                <i class="fas fa-arrow-left me-1"></i>Back
                            </a>
                        </div>
                    </form>

                    <div class="text-center mt-4 text-muted small">
                        &copy; 2025 Exam Management Portal
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>


<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"
        crossorigin="anonymous"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
/* ---------------------------------------------------------------
   Utility: toggle password visibility
---------------------------------------------------------------- */
document.getElementById('togglePassword').addEventListener('click', () => {
    const pw   = document.getElementById('password');
    const icon = document.querySelector('#togglePassword i');
    const isHidden = pw.type === 'password';
    pw.type = isHidden ? 'text' : 'password';
    icon.classList.toggle('fa-eye',  !isHidden);
    icon.classList.toggle('fa-eye-slash', isHidden);
});

const loginForm  = document.getElementById('loginForm');
const submitBtn  = loginForm.querySelector('button[type="submit"]');
const csrfToken  = document.querySelector('meta[name="csrf-token"]').content;

loginForm.addEventListener('submit', e => {
    e.preventDefault();

    submitBtn.disabled = true;
    submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>Signing in…';

    fetch('{{ url('/api/admin/login') }}', {
        method : 'POST',
        headers: {
            'Content-Type' : 'application/json',
            'X-CSRF-TOKEN' : csrfToken,
            'Accept'       : 'application/json'
        },
        body: JSON.stringify({
            email   : loginForm.email.value,
            password: loginForm.password.value
        })
    })
    .then(res => res.json())
    .then(data => {
        submitBtn.disabled = false;
        submitBtn.innerHTML = '<i class="fas fa-sign-in-alt me-2"></i>Sign In';

        if (data.status === 'success') {
            /*  ➜ persist credentials in sessionStorage  */
            sessionStorage.setItem('token', data.access_token);
            if (data.id) { sessionStorage.setItem('admin_id', data.id); }

            Swal.fire({
                icon: 'success',
                title: 'Login successful!',
                text:  data.message || 'Redirecting to dashboard…',
                timer: 1800,
                showConfirmButton: false
            }).then(() => window.location.href = '/admin/dashboard');
        } else {
            Swal.fire({
                icon: 'error',
                title: 'Login failed',
                text:  data.message || 'Invalid credentials — please try again.'
            });
        }
    })
    .catch(() => {
        submitBtn.disabled = false;
        submitBtn.innerHTML = '<i class="fas fa-sign-in-alt me-2"></i>Sign In';

        Swal.fire({
            icon: 'error',
            title: 'Server unreachable',
            text:  'Please check your connection and try again.'
        });
    });
});
</script>
</body>
</html>
