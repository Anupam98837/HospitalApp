<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Navbar</title>
    <link rel="stylesheet" href="style.css" />
    <link
      href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/css/bootstrap.min.css"
      rel="stylesheet"
      integrity="sha384-LN+7fdVzj6u52u30Kp6M/trliBMCMKTyK833zpbD+pXdCLuTusPj697FH4R/5mcr"
      crossorigin="anonymous"
    />
        <link rel="stylesheet" href="{{ asset('css/common/viewNavbar.css') }}">
  </head>
  <body>
    <header class="pm-navbar">
        
      <!-- ── TOP STRIP ──  (logo-stack | headline | partner-marquee | burger) -->
      <div class="pm-top container-fluid d-flex align-items-center gap-3">
        <div class="right-portion">
        <!-- logo + company text (vertical stack) -->
        <a
          href="/"
          class="pm-brand d-flex flex-column align-items-start text-decoration-none"
        >
          <img
            src="assets/images/web_assets/logo2.png"
            alt="LegMed logo"
            class="pm-logo pm-logo-desktop mb-1"
          />
          <!-- Mobile logo -->
  <img src="assets/images/web_assets/logo.jpg" alt="LegMed mobile logo" class="pm-logo pm-logo-mobile mb-1" />
          <div class="lh-sm">
            <strong class="pm-name"
              >LegMed Healthcare Solutions Pvt. Ltd.</strong
            ><br />
            <small class="pm-tag fst-italic text-muted">
              Empowering Healthcare, Believing Solutions
            </small>
          </div>
        </a>
</div>
        <div class="left-portion">
        <div class="upper-nav">
          <!-- rotating headline  (≥ md) -->
          <div
            class="pm-headline flex-grow-1 text-center d-none d-md-flex justify-content-center"
          >
            <span class="hword active">Your Comprehensive Care Provider</span>
            <span class="hword"
              >Empowering Healthcare, Believing Solutions</span
            >
            <span class="hword">Your One-Stop Solution for Healthcare</span>
          </div>

          <!-- partner marquee  (≥ xl) -->
          <div class="pm-partners d-none d-xl-block">
            <div class="pm-track">
              <img
                src="assets/images/web_assets/partner1.png"
                alt="Partner"
                class="pm-plogo"
              />
              <img
                src="assets/images/web_assets/partner2.png"
                alt="Partner"
                class="pm-plogo"
              />
              <img
                src="assets/images/web_assets/partner3.png"
                alt="Partner"
                class="pm-plogo"
              />
              <img
                src="assets/images/web_assets/partner4.png"
                alt="Partner"
                class="pm-plogo"
              />
              <img
                src="assets/images/web_assets/partner5.png"
                alt="Partner"
                class="pm-plogo"
              />
              <img
                src="assets/images/web_assets/partner6.png"
                alt="Partner"
                class="pm-plogo"
              />
              <img
                src="assets/images/web_assets/partner7.jpg"
                alt="Partner"
                class="pm-plogo"
              />
              <img
                src="assets/images/web_assets/partner8.jpg"
                alt="Partner"
                class="pm-plogo"
              />
              <img
                src="assets/images/web_assets/partner9.jpg"
                alt="Partner"
                class="pm-plogo"
              />
              <img
                src="assets/images/web_assets/partner10.jpg"
                alt="Partner"
                class="pm-plogo"
              />
              <img
                src="assets/images/web_assets/partner11.jpg"
                alt="Partner"
                class="pm-plogo"
              />
              <img
                src="assets/images/web_assets/partner12.jpg"
                alt="Partner"
                class="pm-plogo"
              />
              <img
                src="assets/images/web_assets/partner13.jpg"
                alt="Partner"
                class="pm-plogo"
              />
              <img
                src="assets/images/web_assets/partner14.jpg"
                alt="Partner"
                class="pm-plogo"
              />
              <img
                src="assets/images/web_assets/partner15.jpg"
                alt="Partner"
                class="pm-plogo"
              />
              <img
                src="assets/images/web_assets/partner16.jpg"
                alt="Partner"
                class="pm-plogo"
              />
              <img
                src="assets/images/web_assets/partner17.jpg"
                alt="Partner"
                class="pm-plogo"
              />
              <img
                src="assets/images/web_assets/partner18.jpg"
                alt="Partner"
                class="pm-plogo"
              />
              <img
                src="assets/images/web_assets/partner19.jpg"
                alt="Partner"
                class="pm-plogo"
              />
              <img
                src="assets/images/web_assets/partner1.png"
                alt="Partner"
                class="pm-plogo"
              />
              <img
                src="assets/images/web_assets/partner2.png"
                alt="Partner"
                class="pm-plogo"
              />
              <img
                src="assets/images/web_assets/partner3.png"
                alt="Partner"
                class="pm-plogo"
              />
              <img
                src="assets/images/web_assets/partner4.png"
                alt="Partner"
                class="pm-plogo"
              />
              <img
                src="assets/images/web_assets/partner5.png"
                alt="Partner"
                class="pm-plogo"
              />
              <img
                src="assets/images/web_assets/partner6.png"
                alt="Partner"
                class="pm-plogo"
              />
              <img
                src="assets/images/web_assets/partner7.jpg"
                alt="Partner"
                class="pm-plogo"
              />
              <img
                src="assets/images/web_assets/partner8.jpg"
                alt="Partner"
                class="pm-plogo"
              />
              <img
                src="assets/images/web_assets/partner9.jpg"
                alt="Partner"
                class="pm-plogo"
              />
              <img
                src="assets/images/web_assets/partner10.jpg"
                alt="Partner"
                class="pm-plogo"
              />
              <img
                src="assets/images/web_assets/partner11.jpg"
                alt="Partner"
                class="pm-plogo"
              />
              <img
                src="assets/images/web_assets/partner12.jpg"
                alt="Partner"
                class="pm-plogo"
              />
              <img
                src="assets/images/web_assets/partner13.jpg"
                alt="Partner"
                class="pm-plogo"
              />
              <img
                src="assets/images/web_assets/partner14.jpg"
                alt="Partner"
                class="pm-plogo"
              />
              <img
                src="assets/images/web_assets/partner15.jpg"
                alt="Partner"
                class="pm-plogo"
              />
              <img
                src="assets/images/web_assets/partner16.jpg"
                alt="Partner"
                class="pm-plogo"
              />
              <img
                src="assets/images/web_assets/partner17.jpg"
                alt="Partner"
                class="pm-plogo"
              />
              <img
                src="assets/images/web_assets/partner18.jpg"
                alt="Partner"
                class="pm-plogo"
              />
              <img
                src="assets/images/web_assets/partner19.jpg"
                alt="Partner"
                class="pm-plogo"
              />
            </div>
          </div>

          <!-- burger  (< lg) -->
          <button
            class="pm-burger d-lg-none ms-auto"
            type="button"
            aria-label="Toggle navigation"
            data-bs-toggle="offcanvas"
            data-bs-target="#pm-sidebar"
          >
            <span></span><span></span><span></span>
          </button>
        </div>


        <div class="lower-nav">

        
        <!-- ── MAIN NAV (desktop) ── -->
        <nav class="pm-nav container-fluid d-none d-lg-block">
          <ul class="pm-navlist">
            <li><a class="navlink active" href="https://legmed.in/">Home</a></li>
            <li><a class="navlink" href="https://legmed.in/about-us/">About Us</a></li>

            <li class="dropdown">
              <a
                class="navlink dropdown-toggle"
                href="#services"
                data-bs-toggle="dropdown"
              >
                Our Services<i class="fas fa-chevron-down ms-1"></i>
              </a>
              <div class="dropdown-menu">
                <a class="dropdown-item" href="https://legmed.in/healthcare-licences/">Healthcare Licences</a>
                <a class="dropdown-item" href="https://legmed.in/accreditation-support/">Accreditation Support</a>
                <a class="dropdown-item" href="https://legmed.in/compliance-solutions/">Compliance Solutions</a>
                <a class="dropdown-item" href="https://legmed.in/run-and-operate/">Run and Operate</a>
                <a class="dropdown-item" href="https://legmed.in/health-scheme-tpa-tie-ups/">Health Scheme TPA Tie-Ups</a>
                <a class="dropdown-item" href="https://legmed.in/planning-design/">Planning & Design</a>
                <a class="dropdown-item" href="https://legmed.in/clinical-trial-support/">Clinical Trial Support</a>
                <a class="dropdown-item" href="https://legmed.in/educational-support/">Educational Support</a>
                <a class="dropdown-item" href="https://legmed.in/medico-legal-assistance/">Medico-Legal Assistance</a>
                <a class="dropdown-item" href="https://legmed.in/legmed-pharmacy-opticals/">LegMed Pharmacy & Opticals</a>
              </div>
            </li>
            <li><a class="navlink" href="https://legmed.in/association/">Association</a></li>
              <li class="dropdown">
              <a
                class="navlink dropdown-toggle"
                href="#services"
                data-bs-toggle="dropdown"
              >
                Special Initiatives<i class="fas fa-chevron-down ms-1"></i>
              </a>
              <div class="dropdown-menu">
                <a class="dropdown-item" href="https://legmed.in/home-care/">Home Care</a>
                <a class="dropdown-item" href="#">Tourism</a>
                <a class="dropdown-item" href="#">Super-Market</a>
                <a class="dropdown-item" href="#">Manpower Solutions</a>
                <a class="dropdown-item" href="#">Virtual Hospital</a>
              </div>
            </li>
              <li><a class="navlink" href="https://legmed.in/legmed-astro/">LegMed Astro</a></li>
            <li><a class="navlink" href="https://legmed.in/contact-us/">Contact Us</a></li>
            <li><a class="navlink" href="https://legmed.in/careers/">Careers</a></li>
          </ul>
        </nav>
        </div>
        </div>
      </div>
    </header>

    <!-- ── OFF-CANVAS (mobile menu) ── -->
    <aside class="offcanvas offcanvas-start" id="pm-sidebar" tabindex="-1">
      <div class="offcanvas-header">
        <img class="sidebar-logo" src="assets/images/web_assets/sidebar-logo.png" alt="">
        <button
          class="btn-close"
          type="button"
          data-bs-dismiss="offcanvas"
        ></button>
      </div>
      <div class="offcanvas-body">
        <ul class="list-unstyled">
          <li><a class="mitem" href="#home">Home</a></li>
          <li><a class="mitem" href="#about">About Us</a></li>
          <li>
            <button
              class="mitem w-100 text-start collapsed"
              data-bs-toggle="collapse"
              data-bs-target="#mSrv"
            >
              Our Services
            </button>
            <div class="collapse" id="mSrv">
              <ul class="list-unstyled ps-3">
                <li><a class="msub" href="#">Healthcare Licences</a></li>
                <li><a class="msub" href="#">Accreditation Support</a></li>
                <li><a class="msub" href="#">Compliance Solutions</a></li>
                <li><a class="msub" href="#">Run and Operate</a></li>
                <li><a class="msub" href="#">Health Scheme TPA Tie-Ups</a></li>
                <li><a class="msub" href="#">Planning & Design</a></li>
                <li><a class="msub" href="#">Clinical Trial Support</a></li>
                <li><a class="msub" href="#">Educational Support</a></li>
                <li><a class="msub" href="#">Medico-Legal Assistance</a></li>
                <li><a class="msub" href="#">LegMed Pharmacy & Opticals</a></li>
              </ul>
            </div>
          </li>
          <li><a class="mitem" href="#association">Association</a></li>
          <li><a class="mitem" href="#gallery">Gallery</a></li>
          <li><a class="mitem" href="#contact">Contact Us</a></li>
          <li><a class="mitem" href="#careers">Careers</a></li>
        </ul>
      </div>
    </aside>
    <script
      src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.8/dist/umd/popper.min.js"
      integrity="sha384-I7E8VVD/ismYTF4hNIPjVp/Zjvgyol6VFvRkX/vR+Vc4jQkC+hVqc2pM8ODewa9r"
      crossorigin="anonymous"
    ></script>
    <script
      src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/js/bootstrap.min.js"
      integrity="sha384-7qAoOXltbVP82dhxHAUje59V5r2YsVfBafyUDxEdApLPmcdhBPg1DKg1ERo0BZlK"
      crossorigin="anonymous"
    ></script>
    <!-- JS — place after Bootstrap bundle -->
    <script>
      document.addEventListener("DOMContentLoaded", () => {
        /* headline rotator */
        const words = [...document.querySelectorAll(".hword")];
        if (!words.length) return;
        let i = 0;
        setInterval(() => {
          words[i].classList.remove("active");
          i = (i + 1) % words.length;
          words[i].classList.add("active");
        }, 2200);
      });
    </script>
  </body>
</html>
