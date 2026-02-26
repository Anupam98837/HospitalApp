{{-- resources/views/layouts/admin/structure.blade.php --}}
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width,initial-scale=1">
    <title>@yield('title','Dashboard')</title>

    <!-- ─────────── 3rd-party UI ─────────── -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css"   rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-icons/1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css" rel="stylesheet">

    <!-- ─────────── Shared palette & layout ─────────── -->
    <link rel="stylesheet" href="{{ asset('css/common/main.css') }}">
    <link rel="stylesheet" href="{{ asset('css/layout/structure.css') }}">

    @stack('styles')
</head>
<body>
<div class="layout">

    <!-- ╭─────────── Sidebar ───────────╮ -->
    <aside id="sidebar" class="dashboard-sidebar">
        <button id="closeSidebar" class="close-sidebar"><i class="bi bi-x-lg"></i></button>

        <div class="sidebar-logo text-center">
            <img src="{{ asset('assets/images/web_assets/logo.jpg') }}" alt="Logo">
        </div>

        <nav class="sidebar-nav flex-grow-1">
            <a href="/admin/dashboard" class="nav-link">
                <i class="fas fa-home"></i><span>Dashboard</span>
            </a>

            <!-- Department -->
            <div class="nav-group">
                <a href="#" class="nav-link group-toggle" data-target="deptMenu">
                    <i class="fas fa-building"></i><span>Department</span>
                    <i class="fas fa-chevron-down ms-auto"></i>
                </a>
                <div id="deptMenu" class="submenu">
                    <a href="/admin/department/manage" class="nav-link">Manage Department</a>
                </div>
            </div>

            <!-- Doctors -->
            <div class="nav-group">
                <a href="#" class="nav-link group-toggle" data-target="docMenu">
                    <i class="fas fa-user-md"></i><span>Doctors</span>
                    <i class="fas fa-chevron-down ms-auto"></i>
                </a>
                <div id="docMenu" class="submenu">
                    <a href="/admin/doctor/create"  class="nav-link">Create Appointment</a>
                    <a href="/admin/doctor/manage" class="nav-link">Manage Doctor</a>
                </div>
            </div>
        </nav>

        <div class="sidebar-auth p-3">
            <a href="#" id="logoutBtn" class="auth-link">
                <i class="fas fa-sign-out-alt me-2"></i><span>Logout</span>
            </a>
        </div>
    </aside>
    <!-- ╰─────────────────────────────────╯ -->

    <div id="sidebarOverlay" class="sidebar-overlay"></div>

    <!-- ╭─────────── Right panel ───────────╮ -->
    <div id="rightPanel" class="right-panel">
        <header class="admin-header d-flex align-items-center px-3 shadow-sm">
            <button id="toggleSidebar" class="btn btn-link p-0 me-2 d-lg-none">
                <i class="bi bi-list fs-3"></i>
            </button>

            <div class="ms-auto dropdown">
                <span id="userDropdown" class="d-flex align-items-center dropdown-toggle" data-bs-toggle="dropdown">
                    <i class="fa-solid fa-user ah_usericon me-2"></i>
                    <span id="userName">
                        <span class="spinner-border spinner-border-sm me-1"></span>Loading…
                    </span>
                </span>
                <ul class="dropdown-menu dropdown-menu-end">
                    <li class="dropdown-header" id="userRole">
                        <span class="spinner-border spinner-border-sm me-1"></span>Loading…
                    </li>
                    <li><a id="profileLink"  class="dropdown-item" href="#">Profile</a></li>
                    <li><a               class="dropdown-item" href="#">Settings</a></li>
                    <li><hr class="dropdown-divider"></li>
                    <li><a id="logoutBtnMobile" class="dropdown-item" href="#">Logout</a></li>
                </ul>
            </div>
        </header>

        <main class="main-content p-4">@yield('content')</main>
    </div>
    <!-- ╰──────────────────────────────────╯ -->
</div>

<!-- ─────────── Core scripts ─────────── -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

@stack('scripts')
@yield('scripts')

<script>
document.addEventListener('DOMContentLoaded', async () => {
    /* ──────────────────────────────────────────────────────────
       TOKEN → stored at login. Redirect to login if missing.
    ────────────────────────────────────────────────────────── */
    const TOKEN = sessionStorage.getItem('token');
    if (!TOKEN) return window.location.replace('/admin/login');

    /* ──────────────────────────────────────────────────────────
       TEMP DISABLED “who-am-I” REQUEST
       ✱ Remove the comment wrapper once /api/admin/me exists ✱
    ──────────────────────────────────────────────────────────
    try {
        const res = await fetch('/api/admin/me', {
            headers: { Accept: 'application/json', Authorization: `Bearer ${TOKEN}` }
        });
        if (!res.ok) throw new Error();
        const { data } = await res.json();

        document.getElementById('userName').textContent = data.user_data.name;
        document.getElementById('userRole').textContent =
            data.user_type.charAt(0).toUpperCase() + data.user_type.slice(1);
        document.getElementById('profileLink').href = `/${data.user_type}/profile`;
    } catch { }
    */

    /* ───── Placeholder name/role while API is absent ───── */
    document.getElementById('userName').textContent = 'Admin';
    document.getElementById('userRole').textContent = 'Administrator';

    /* ───────────────── SIDEBAR: open / close ───────────────── */
    const sidebar   = document.getElementById('sidebar');
    const overlay   = document.getElementById('sidebarOverlay');
    const rightPane = document.getElementById('rightPanel');

    function openSidebar () { sidebar.classList.add('active'); overlay.classList.add('active'); rightPane.classList.add('shifted'); }
    function closeSidebar() { sidebar.classList.remove('active'); overlay.classList.remove('active'); rightPane.classList.remove('shifted'); }

    document.getElementById('toggleSidebar')?.addEventListener('click', openSidebar);
    document.getElementById('closeSidebar').addEventListener('click', closeSidebar);
    overlay.addEventListener('click', closeSidebar);

    /* Submenu toggle */
    document.querySelectorAll('.group-toggle').forEach(el => el.addEventListener('click', e => {
        e.preventDefault();
        const menu = document.getElementById(el.dataset.target);
        const icon = el.querySelector('.fa-chevron-down');
        const open = menu.classList.toggle('open');
        menu.style.display = open ? 'flex' : 'none';
        icon.classList.toggle('fa-chevron-up', open);
    }));

    /* Highlight active link */
    (() => {
        const path = window.location.pathname;
        document.querySelectorAll('.sidebar-nav .nav-link[href]').forEach(a => {
            const href = a.getAttribute('href');
            if (path === href || (path.startsWith(href) && href !== '/')) {
                a.classList.add('active');
                const grp = a.closest('.nav-group');
                if (grp) {
                    const menu = grp.querySelector('.submenu');
                    const icon = grp.querySelector('.fa-chevron-down');
                    menu.classList.add('open'); menu.style.display='flex';
                    icon.classList.replace('fa-chevron-down','fa-chevron-up');
                }
            }
        });
    })();

    /* ───────────────────────── Logout ───────────────────────── */
    async function doLogout() {

  /* 1.  “Logging out …” spinner */
  Swal.fire({
    title: 'Logging out…',
    didOpen: () => Swal.showLoading(),
    allowOutsideClick: false,
    heightAuto: false        // keeps it centred on short pages
  });

  try {
    const res = await fetch('/api/admin/logout', {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json',
        'Authorization': `Bearer ${sessionStorage.getItem('token')}`
      }
    });

    if (!res.ok) {
      const { message } = await res.json();
      throw new Error(message || 'Logout failed');
    }

    /* clear token **before** leaving */
    sessionStorage.removeItem('token');

    /* 2. success toast → then redirect */
    Swal.fire({
      icon: 'success',
      title: 'Logged out!',
      timer: 1200,
      timerProgressBar: true,
      showConfirmButton: false,
      heightAuto: false
    }).then(() => {
      window.location.replace('/admin/login');   // or '/'
    });

  } catch (err) {
    Swal.fire('Error', err.message, 'error');
  }
}

/* hook up both buttons */
['logoutBtn', 'logoutBtnMobile'].forEach(id =>
  document.getElementById(id)?.addEventListener('click', e => {
    e.preventDefault();
    doLogout();
  })
);
});
</script>
</body>
</html>
