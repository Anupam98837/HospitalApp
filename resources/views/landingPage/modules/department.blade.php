@extends('landingPage.layout.structure')

@section('title', 'Departments - Find Your Specialist')

@push('styles')
 <link rel="stylesheet" href="{{ asset('css/pages/department/showDepartment.css') }}">

@endpush

@section('content')
<!-- Hero Search Section -->
<section class="hero-search-section">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-12 text-center mb-4">
                <h1 class="hero-title fw-bold text-center text-white display-5">
                    Search & Book a Consultation with Committed Doctors
                </h1>
            </div>
            <div class="col-lg-10 col-xl-8">
                <div class="search-form-wrapper">
                    <form id="searchForm" class="row g-3 align-items-center">
                        <div class="col-md-5">
                            <div class="position-relative">
                                <input type="text" 
                                       id="doctorSearch" 
                                       class="form-control" 
                                       placeholder="Search by Doctor Name..."
                                       autocomplete="off">
                                <i class="fas fa-search position-absolute top-50 end-0 translate-middle-y me-3 text-muted"></i>
                            </div>
                        </div>
                        <div class="col-md-5">
                            <select id="departmentSelect" class="form-select">
                                <option value="">Search by Department</option>
                            </select>
                        </div>
                        <div class="col-md-2">
                            <button type="submit" class="btn btn-submit w-100">
                                <i class="fas fa-search me-1"></i> Submit
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Departments Section -->
<section class="departments-section py-5">
    <div class="container">
        <div class="row mb-4">
            <div class="col-lg-6">
                <h2 class="fw-bold text-dark mb-2">Our Medical Departments</h2>
                <p class="text-muted">Choose from our wide range of specialized medical departments</p>
            </div>
            <div class="col-lg-6">
                <div class="d-flex justify-content-end">
                    <select id="departmentFilter" class="form-select" style="max-width: 300px;">
                        <option value="">All Departments</option>
                    </select>
                </div>
            </div>
        </div>

        <!-- Loading Spinner -->
        <div id="loadingSpinner" class="text-center py-5">
            <div class="spinner-border text-primary" role="status">
                <span class="visually-hidden">Loading...</span>
            </div>
            <p class="mt-2 text-muted">Loading departments...</p>
        </div>

        <!-- Department Cards Container -->
        <div id="departmentCardsContainer" class="row" style="display: none;">
            <!-- Cards will be populated here -->
        </div>

        <!-- No Results Message -->
        <div id="noResults" class="text-center py-5" style="display: none;">
            <i class="fas fa-search fa-3x text-muted mb-3"></i>
            <h4 class="text-muted">No departments found</h4>
            <p class="text-muted">Try adjusting your search criteria</p>
        </div>
    </div>
</section>
@endsection

@section('scripts')
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Global state
    let allDepartments = [];
    let filteredDeps = [];
    let doctorsCache = [];

    // DOM elements
    const loadingSpinner = document.getElementById('loadingSpinner');
    const departmentCardsContainer = document.getElementById('departmentCardsContainer');
    const noResults = document.getElementById('noResults');
    const departmentFilter = document.getElementById('departmentFilter');
    const departmentSelect = document.getElementById('departmentSelect');
    const searchForm = document.getElementById('searchForm');
    const doctorSearch = document.getElementById('doctorSearch');

    // Initialize
    init();

    async function init() {
        try {
            // Parallel fetch
            const [deptsResponse, doctorsResponse] = await Promise.all([
                fetch('/api/departments'),
                fetch('/api/doctors')
            ]);

            const deptsData = await deptsResponse.json();
            const doctorsData = await doctorsResponse.json();

            if (deptsData.success && deptsData.departments) {
                allDepartments = deptsData.departments.filter(dept => dept.status === 'active');
                filteredDeps = [...allDepartments];
                populateDepartmentFilter();
                populateDepartmentSelect();
            }

            if (doctorsData.success && doctorsData.doctors) {
                doctorsCache = doctorsData.doctors.filter(doctor => doctor.is_active === 1);
            }

            renderAllDepartments();
            setupAutocomplete();
            hideLoading();

        } catch (error) {
            console.error('Error initializing:', error);
            showNoResults();
        }
    }

    function populateDepartmentFilter() {
        const options = allDepartments.map(dept => 
            `<option value="${dept.id}">${dept.title}</option>`
        ).join('');
        departmentFilter.innerHTML = '<option value="">All Departments</option>' + options;
    }

    function populateDepartmentSelect() {
        const options = allDepartments.map(dept => 
            `<option value="${dept.id}">${dept.title}</option>`
        ).join('');
        departmentSelect.innerHTML = '<option value="">Search by Department</option>' + options;
    }

    function renderAllDepartments() {
        departmentCardsContainer.innerHTML = '';

        if (filteredDeps.length === 0) {
            showNoResults();
            return;
        }

        const cardsHTML = filteredDeps.map(dept => `
            <div class="col department-item">
                <a href="/doctors?department_id=${dept.id}" class="dep-link">
                    <span class="dep-badge">
                        <img src="{{ asset('') }}${dept.image_url}" alt="${dept.title}" />
                    </span>
                    <span class="dep-title">${dept.title}</span>
                </a>
            </div>
        `).join('');

        departmentCardsContainer.innerHTML = cardsHTML;
        
        // Show container
        departmentCardsContainer.style.display = 'block';
        noResults.style.display = 'none';
    }

    function hideLoading() {
        loadingSpinner.style.display = 'none';
    }

    function showNoResults() {
        loadingSpinner.style.display = 'none';
        departmentCardsContainer.style.display = 'none';
        noResults.style.display = 'block';
    }

    // Department filter
    departmentFilter.addEventListener('change', function() {
        const selectedId = this.value;
        filteredDeps = selectedId ? 
            allDepartments.filter(dept => dept.id == selectedId) : 
            [...allDepartments];
        renderAllDepartments();
    });

    // Search form submission
    searchForm.addEventListener('submit', function(e) {
        e.preventDefault();
        
        const doctorName = doctorSearch.value.trim();
        const departmentId = departmentSelect.value;
        
        if (!doctorName && !departmentId) {
            alert('Please enter a doctor name or select a department');
            return;
        }

        // Build query string
        const params = new URLSearchParams();
        if (doctorName) params.append('doctor_name', doctorName);
        if (departmentId) params.append('department_id', departmentId);
        
        // Redirect
        window.location.href = `/doctors?${params.toString()}`;
    });

    // Doctor autocomplete
    function setupAutocomplete() {
        const debounce = (fn, ms) => {
            let timeout;
            return (...args) => {
                clearTimeout(timeout);
                timeout = setTimeout(() => fn(...args), ms);
            };
        };

        const menu = document.createElement('ul');
        menu.className = 'dropdown-menu autocomplete-menu';
        doctorSearch.parentNode.appendChild(menu);

        doctorSearch.addEventListener('keyup', debounce(function(e) {
            const term = e.target.value.trim().toLowerCase();
            
            if (term.length < 2) {
                menu.innerHTML = '';
                menu.classList.remove('show');
                return;
            }

            const hits = doctorsCache.filter(doctor => {
                const fullName = `${doctor.first_name} ${doctor.last_name}`.toLowerCase();
                return fullName.includes(term);
            }).slice(0, 10);

            if (hits.length === 0) {
                menu.innerHTML = '';
                menu.classList.remove('show');
                return;
            }

            menu.innerHTML = hits.map(doctor => 
                `<li class="dropdown-item">
                    <i class="fas fa-user-md me-2"></i>
                    ${doctor.first_name}&nbsp;${doctor.last_name}
                </li>`
            ).join('');

            menu.classList.add('show');

            // Add click handlers
            [...menu.children].forEach(li => {
                li.addEventListener('click', function() {
                    doctorSearch.value = this.textContent.replace(/\s+/g, ' ').trim();
                    menu.innerHTML = '';
                    menu.classList.remove('show');
                });
            });
        }, 300));

        // Hide menu when clicking outside
        document.addEventListener('click', function(e) {
            if (!doctorSearch.contains(e.target) && !menu.contains(e.target)) {
                menu.classList.remove('show');
            }
        });
    }
});
</script>
@endsection