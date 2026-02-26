@extends('landingPage.layout.structure')

@section('title', 'Book a Doctor – Find Your Specialist')

@section('content')
<div class="container-fluid py-4">
  <div class="row">
    {{-- ───────────── LEFT / MAIN ───────────── --}}
    <div class="col-lg-9">
        {{-- ✅ Helpline highlight --}}
  <div class="alert alert-warning d-flex align-items-center justify-content-between flex-wrap gap-2 shadow-sm"
        role="alert"
        style="border-left:6px solid var(--bs-primary);">
      <div class="d-flex align-items-center gap-2">
      <i class="fas fa-headset"></i>
      <div>
        <strong>Helpline:</strong>
        For charges, booking and any other information please reach us on
        <a href="tel:8272994771" class="fw-bold text-decoration-none">8272994771</a>
      </div>
      </div>
      <a href="tel:8272994771" class="btn btn-sm btn-primary">
      Call Now
      </a>
      </div>
      <div class="d-flex align-items-center mb-4">
        <i class="fas fa-sliders-h me-2"></i>
        <span class="me-3">Filter</span>
        <select id="filterDepartment" class="form-select form-select-sm" style="max-width:240px">
          <option value="">All Departments</option>
        </select>
      </div>

      <div id="doctorsLoader" class="text-center my-5">
        <div class="spinner-border text-primary" style="width:3rem;height:3rem;" role="status">
          <span class="visually-hidden">Loading…</span>
        </div>
      </div>

      <div id="doctorList" class="d-none"></div>

      <div class="text-center">
        <button id="viewMoreBtn" class="btn btn-outline-primary px-4 py-2" style="display:none">
          View More Doctors
        </button>
      </div>
    </div>

    {{-- ───────────── RIGHT / BLOG ───────────── --}}
    <div class="col-lg-3 d-none d-lg-block">
      <div class="blog-teaser">
        <img src="{{ asset('assets/images/web_assets/paediatrics.jpg') }}" alt="Paediatrics">
        <div class="blog-overlay">
          <h6 class="fw-bold mb-2">Paediatrics</h6>
          <p class="small mb-0">The Growing Concern of Children's Screen Addiction</p>
        </div>
      </div>
      <div class="blog-teaser">
        <img src="{{ asset('assets/images/web_assets/orthopaedics.jpg') }}" alt="Orthopaedics">
        <div class="blog-overlay">
          <h6 class="fw-bold mb-2">Orthopaedics</h6>
          <p class="small mb-0">When Should You Consider a Knee Replacement Surgery?</p>
        </div>
      </div>
    </div>
  </div>
</div>

{{-- ─────────────────────────  MODALS  ───────────────────────── --}}
{{-- 1) Auth pick --}}
<div class="modal fade" id="authSelectModal" tabindex="-1">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content">
      <div class="modal-header"><h5 class="modal-title">Authentication Required</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button></div>
      <div class="modal-body text-center">
        <p>Please login or register to book an appointment.</p>
        <div class="d-grid gap-2">
          <button class="btn btn-primary"  data-bs-target="#registerModal" data-bs-toggle="modal" data-bs-dismiss="modal">Register New Account</button>
          <button class="btn btn-outline-primary" data-bs-target="#loginModal"    data-bs-toggle="modal" data-bs-dismiss="modal">Login to Existing Account</button>
        </div>
      </div>
    </div>
  </div>
</div>

{{-- 2) Register --}}
<div class="modal fade" id="registerModal" tabindex="-1">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content">
      <div class="modal-header"><h5 class="modal-title">Register New Account</h5>
        <button class="btn-close" data-bs-dismiss="modal"></button></div>
      <div class="modal-body">
        <form id="registerForm">
          <div class="mb-3"><label class="form-label">Full Name</label><input name="name" type="text" class="form-control" required></div>
          <div class="mb-3"><label class="form-label">Email</label><input name="email" type="email" class="form-control" required></div>
          <div class="mb-3"><label class="form-label">Phone</label><input name="phone" type="tel" class="form-control" required></div>
          <div class="mb-3">
            <label class="form-label">Password</label>
            <div class="input-group">
              <input id="registerPassword" name="password" type="password" class="form-control" required>
              <button class="btn btn-outline-secondary" type="button" id="toggleRegisterPassword"><i class="fas fa-eye"></i></button>
            </div>
          </div>
          <div class="mb-3"><label class="form-label">Address</label><textarea name="address" rows="3" class="form-control"></textarea></div>
          <div class="d-grid">
            <button id="registerSubmitBtn" class="btn btn-primary">
              <span class="btn-text">Register</span>
              <span class="spinner-border spinner-border-sm d-none" role="status" aria-hidden="true"></span>
            </button>
          </div>
        </form>
      </div>
    </div>
  </div>
</div>

{{-- 3) Login --}}
<div class="modal fade" id="loginModal" tabindex="-1">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content">
      <div class="modal-header"><h5 class="modal-title">Login</h5><button class="btn-close" data-bs-dismiss="modal"></button></div>
      <div class="modal-body">
        <form id="loginForm">
          <div class="mb-3"><label class="form-label">Email</label><input name="email" type="email" class="form-control" required></div>
          <div class="mb-3">
            <label class="form-label">Password</label>
            <div class="input-group">
              <input id="loginPassword" name="password" type="password" class="form-control" required>
              <button class="btn btn-outline-secondary" type="button" id="toggleLoginPassword"><i class="fas fa-eye"></i></button>
            </div>
          </div>
          <div class="d-grid">
            <button id="loginSubmitBtn" class="btn btn-primary">
              <span class="btn-text">Login</span>
              <span class="spinner-border spinner-border-sm d-none" role="status" aria-hidden="true"></span>
            </button>
          </div>
        </form>
      </div>
    </div>
  </div>
</div>

{{-- 4) Booking type --}}
<div class="modal fade" id="bookingTypeModal" tabindex="-1">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content">
      <div class="modal-header"><h5 class="modal-title">Book Appointment</h5><button class="btn-close" data-bs-dismiss="modal"></button></div>
      <div class="modal-body">
        <p class="mb-3">Who are you booking this appointment for?</p>
        <div class="form-check mb-2"><input class="form-check-input" type="radio" name="bookingType" value="self"   id="bookingSelf"  checked><label class="form-check-label" for="bookingSelf">Book for Self</label></div>
        <div class="form-check mb-2"><input class="form-check-input" type="radio" name="bookingType" value="family" id="bookingFamily"><label class="form-check-label" for="bookingFamily">Book for Others</label></div>
        <div class="d-grid"><button class="btn btn-primary" id="bookingTypeNext">Next</button></div>
      </div>
    </div>
  </div>
</div>

{{-- 5) Booking form (3-step) --}}
<div class="modal fade" id="bookingFormModal" tabindex="-1">
  <div class="modal-dialog modal-lg modal-dialog-centered">
    <div class="modal-content">
      <div class="modal-header"><h5 class="modal-title">Book Appointment</h5><button class="btn-close" data-bs-dismiss="modal"></button></div>
      <div class="modal-body">
        <form id="bookingForm">
          @csrf
          <input type="hidden" id="bookingDoctorId" name="doctor_id">
          <input type="hidden" id="bookingSlotId"   name="slot_id">
          <input type="hidden" id="bookingDate"     name="appointment_date">

          {{-- STEP 1 – patient details --}}
          <div id="step1" class="booking-step">
            <div class="mb-3"><label class="form-label">Patient Name</label><input id="patientName" class="form-control" name="patient_name" required></div>
            <div class="mb-3"><label class="form-label">Email</label><input id="patientEmail" class="form-control" name="email" required></div>
            <div class="mb-3"><label class="form-label">Phone</label><input id="patientPhone" class="form-control" name="phone" required></div>
            <div class="mb-3 family-only d-none"><label class="form-label">Alternate Email</label><input id="alternateEmail" class="form-control" name="alternate_email" type="email"></div>
            <div class="mb-3 family-only d-none"><label class="form-label">Alternate Phone</label><input id="alternatePhone" class="form-control" name="alternate_phone" type="tel"></div>
            <div class="mb-3"><label class="form-label">Address</label><textarea id="patientAddress" class="form-control" name="patient_address" rows="3"></textarea></div>
            <div class="mb-3"><label class="form-label">Additional Notes</label><textarea id="appointmentNote" class="form-control" name="additional_note" rows="3"></textarea></div>
            <div class="d-grid"><button id="step1NextBtn" class="btn btn-primary">Next</button></div>
          </div>

          {{-- STEP 2 – pick weekly slot window --}}
          <div id="step2" class="booking-step d-none">
            <div class="mb-3">
              <label class="form-label">Select Slot Window</label>
              <div id="slotSelection" class="slot-selection"></div>
            </div>
            <div class="d-flex justify-content-between">
              <button type="button" class="btn btn-outline-secondary" id="step2PrevBtn">Previous</button>
              <button type="button" class="btn btn-primary" id="step2NextBtn">
  <span class="btn-text">Next</span>
  <span class="spinner-border spinner-border-sm d-none" role="status" aria-hidden="true"></span>
</button>
            </div>
          </div>

          {{-- STEP 3 – pick 30-min slice --}}
          <div id="step3" class="booking-step d-none">
            <div class="mb-3">
              <label class="form-label">Select 30-minute Timing</label>
              <div id="sliceSelection" class="slot-selection"></div>
            </div>
            <div class="d-flex justify-content-between">
              <button type="button" class="btn btn-outline-secondary" id="step3PrevBtn">Previous</button>
              <button id="bookingSubmitBtn" class="btn btn-success">
                <span class="btn-text">Book Appointment</span>
                <span class="spinner-border spinner-border-sm d-none" role="status" aria-hidden="true"></span>
              </button>
            </div>
          </div>
        </form>
      </div>
    </div>
  </div>
</div>
@endsection



@section('scripts')
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
/* ─────────────────────────── GLOBAL STATE ─────────────────────────── */
let doctorsAll = [], doctorsDisplay = [], departmentsAll = [];
let userDetails = null, shown = 0;
const pageSize = 4;
const slotCache  = {};          // weekly windows keyed by doctorId
const sliceCache = {};          // 30-min slices keyed by slotId|date
let currentStep = 1;
let selectedDoctorId = null, selectedBookingType = 'self';

/* Token & URL params */
const token = sessionStorage.getItem('token') || null;
const qsDepartmentId = new URLSearchParams(location.search).get('department_id') || '';

/* DOM shortcuts */
const listEl        = document.getElementById('doctorList');
const viewMoreBtn   = document.getElementById('viewMoreBtn');
const deptFilterSel = document.getElementById('filterDepartment');
const doctorsLoader = document.getElementById('doctorsLoader');
/* Stepper elements */
const stepEls = [document.getElementById('step1'),
                 document.getElementById('step2'),
                 document.getElementById('step3')];
const familyFields = document.querySelectorAll('.family-only');

/* ─────────────────────────────── INIT ─────────────────────────────── */
document.addEventListener('DOMContentLoaded', init);

async function init(){
  showDoctorSkeleton();
  try{
      await loadDepartments();
      if (token) await loadUserDetails();
      await fetchAndRenderDoctors(qsDepartmentId);
  }catch{
      listEl.innerHTML = '<div class="alert alert-danger">Error loading doctors.</div>';
  }finally{
      doctorsLoader.classList.add('d-none');
      listEl.classList.remove('d-none');
  }

  /* UI listeners */
  viewMoreBtn.addEventListener('click', () => renderDoctors(false));
  listEl.addEventListener('click', e=>{
      const btn = e.target.closest('.book-btn');
      if (btn) openBookingFlow(+btn.dataset.id);
  });
  deptFilterSel.addEventListener('change', () =>
      fetchAndRenderDoctors(deptFilterSel.value || null));

  setupAuthModals();
  setupPasswordToggles();
  setupBookingModalEvents();
}

/* ─────────────────────────── AUTH TOGGLES ─────────────────────────── */
function setupPasswordToggles(){
  [['toggleRegisterPassword','registerPassword'],
   ['toggleLoginPassword','loginPassword']].forEach(([btnId,inId])=>{
      const btn=document.getElementById(btnId), inp=document.getElementById(inId);
      if (!btn || !inp) return;
      btn.addEventListener('click',()=>{
          const show = inp.type==='password';
          inp.type = show?'text':'password';
          btn.querySelector('i').classList.toggle('fa-eye', !show);
          btn.querySelector('i').classList.toggle('fa-eye-slash', show);
      });
  });
}

/* ─────────────────────────── STEP CONTROLLER ─────────────────────── */
function showStep(n){
  currentStep = n;
  stepEls.forEach((el,idx)=> el.classList.toggle('d-none', idx !== n-1));
  /* mark radio requirements */
  document.querySelectorAll('input[name="slot_id"]').forEach(r=> r.required = (n===2));
  document.querySelectorAll('input[name="slice_start"]').forEach(r=> r.required = (n===3));
}

/* ─────────────────────── MODAL EVENT HANDLERS ─────────────────────── */
function setupBookingModalEvents(){
  /* Step 0 → patient/self/family */
  document.getElementById('bookingTypeNext').addEventListener('click', ()=>{
      selectedBookingType=document.querySelector('input[name="bookingType"]:checked').value;
      bootstrap.Modal.getOrCreateInstance('#bookingTypeModal').hide();
      setupBookingForm();
      showStep(1);
      bootstrap.Modal.getOrCreateInstance('#bookingFormModal').show();
  });

  /* Step 1 next */
  document.getElementById('step1NextBtn').addEventListener('click', e=>{
      e.preventDefault();
      if (stepEls[0].parentElement.checkValidity()) showStep(2);
      else stepEls[0].parentElement.reportValidity();
  });

  /* Step 2 prev / next */
  document.getElementById('step2PrevBtn').addEventListener('click', ()=> showStep(1));
const step2NextBtn = document.getElementById('step2NextBtn');

step2NextBtn.addEventListener('click', async () => {
  const slotId = +document.querySelector('input[name="slot_id"]:checked')?.value || 0;
  if (!slotId) { stepEls[1].parentElement.reportValidity(); return; }

  toggleBtnLoading(step2NextBtn, true);     // ⬅️ start spinner

  try {
    const slotObj = slotCache[selectedDoctorId].find(s => s.id === slotId);
    const dateStr = nextDateForDow(slotObj.day_of_week);

    document.getElementById('bookingSlotId').value = slotId;
    document.getElementById('bookingDate')  .value = dateStr;

    await loadSlices(slotId, dateStr);      // fetch 30-min slices
    showStep(3);                            // then reveal STEP 3
  } finally {
    toggleBtnLoading(step2NextBtn, false);  // ⬅️ stop spinner
  }
});

  /* Step 3 prev */
  document.getElementById('step3PrevBtn').addEventListener('click', ()=> showStep(2));

  /* Final submit */
  document.getElementById('bookingForm').addEventListener('submit', handleBookingSubmit);
}

/* ─────────────────────────  USER HELPERS ─────────────────────────── */
async function loadUserDetails(){
  try{
      const res = await fetch('/api/users/me',
          {headers:{'Authorization':`Bearer ${token}`, 'Accept':'application/json'}});
      const json= await res.json();
      if (res.ok && json.success) userDetails = json.user;
  }catch{}
}

async function loadDepartments(){
  try{
      const res = await fetch('/api/departments');
      const json= await res.json();
      if (json.success && Array.isArray(json.departments)){
          departmentsAll = json.departments.filter(d=>d.status==='active');
          deptFilterSel.innerHTML = '<option value="">All Departments</option>';
          departmentsAll.forEach(d=> deptFilterSel.insertAdjacentHTML(
              'beforeend', `<option value="${d.id}">${d.title}</option>`
          ));
          if (qsDepartmentId) deptFilterSel.value = qsDepartmentId;
      }
  }catch{}
}

/* ─────────────────────────── DOCTOR FETCH ────────────────────────── */
async function fetchAndRenderDoctors(deptId){
  doctorsLoader.classList.remove('d-none');
  listEl.classList.add('d-none');
  viewMoreBtn.style.display='none';

  try{
      const url = deptId ? `/api/departments/${deptId}/doctors` : '/api/doctors';
      const res = await fetch(url);
      const json= await res.json();
      if (json.success && Array.isArray(json.doctors)){
          doctorsAll = json.doctors.filter(d=>d.is_active===1);
          doctorsAll.forEach(doc=>{
              if (Array.isArray(doc.schedules)) slotCache[doc.id] = doc.schedules;
          });
          doctorsDisplay=[...doctorsAll];
          renderDoctors(true);
      }else{
          doctorsAll = doctorsDisplay = [];
          showNoDoctorsMessage();
      }
  }catch{
      doctorsAll = doctorsDisplay = [];
      showNoDoctorsMessage();
  }finally{
      doctorsLoader.classList.add('d-none');
      listEl.classList.remove('d-none');
  }
}

function renderDoctors(reset){
  if (reset){ shown=0; listEl.innerHTML=''; }
  const batch = doctorsDisplay.slice(shown, shown+pageSize);
  if (batch.length===0 && shown===0){ showNoDoctorsMessage(); return; }

  batch.forEach(doc=>{
      listEl.insertAdjacentHTML('beforeend', doctorCardHTML(doc));
      if (slotCache[doc.id]) paintSlots(doc.id, slotCache[doc.id]);
      else loadSlots(doc.id);
  });
  shown += batch.length;
  viewMoreBtn.style.display = shown>=doctorsDisplay.length ? 'none' : 'block';
}

function doctorCardHTML(doc){
  return `
    <div class="doctor-card fade-in mb-4" data-doctor-id="${doc.id}">
      <div class="row g-3">
        <div class="col-md-3 text-center">
          <img src="{{ asset('') }}${doc.image_url || 'images/default-doctor.jpg'}"
               class="doc-img" alt="">
        </div>
        <div class="col-md-6">
          <h5 class="fw-bold mb-1">Dr. ${doc.first_name} ${doc.last_name}</h5>
          <p class="mb-1 text-muted"><i class="fas fa-graduation-cap me-1"></i>${doc.degree}, ${doc.specialty}</p>
          <p class="small text-muted mb-2"><i class="fas fa-map-marker-alt me-1"></i>${doc.office_address}</p>
          <div id="slotWrap-${doc.id}" class="slot-wrap"></div>
        </div>
        <div class="col-md-3 d-flex align-items-start justify-content-md-end">
          <button class="btn btn-danger book-btn" data-id="${doc.id}">Book Appointment ›</button>
        </div>
      </div>
    </div>`;
}

function showDoctorSkeleton(count=4){
  listEl.innerHTML='';
  for (let i=0;i<count;i++){
      listEl.insertAdjacentHTML('beforeend',
          `<div class="doctor-card loading-shimmer mb-4" style="height:180px;"></div>`);
  }
}

function showNoDoctorsMessage(){
  const dept = departmentsAll.find(d=>d.id==deptFilterSel.value);
  const label= dept ? ` in ${dept.title} department` : '';
  listEl.innerHTML=`<div class="alert alert-info text-center">No doctors available${label}.</div>`;
  viewMoreBtn.style.display='none';
}

/* ─────────────────────────── WEEKLY SLOTS ───────────────────────── */
async function loadSlots(doctorId){
  if (slotCache[doctorId]){ paintSlots(doctorId, slotCache[doctorId]); return; }
  try{
      const res = await fetch(`/api/appointments/doctor/${doctorId}`);
      const json= await res.json();
      if (json.success && Array.isArray(json.appointments)){
          slotCache[doctorId]=json.appointments;
          paintSlots(doctorId, json.appointments);
      }else slotMsg(doctorId,'No slots');
  }catch{ slotMsg(doctorId,'Error'); }
}

function paintSlots(id,arr){
  const wrap = document.getElementById('slotWrap-'+id); if (!wrap) return;
  if (arr.length===0){ slotMsg(id,'No slots'); return; }

  const days=['Sunday','Monday','Tuesday','Wednesday','Thursday','Friday','Saturday'];
  const colors=['slot-c1','slot-c2','slot-c3','slot-c4','slot-c5','slot-c6'];
  wrap.innerHTML = arr.slice(0,6).map((a,i)=>{
      const day  = days[a.day_of_week%7];
      const time = `${a.start_time.slice(0,5)}–${a.end_time.slice(0,5)}`;
      return `<span class="slot-chip ${colors[i%colors.length]}">${day}: ${time}</span>`;
  }).join('');
}
const slotMsg=(id,msg)=>{
  const w=document.getElementById('slotWrap-'+id);
  if (w) w.innerHTML=`<span class="small text-muted">${msg}</span>`;
};

/* ───────────────────────── 30-MIN SLICES ───────────────────────── */
async function loadSlices(slotId,dateStr){
  const key=`${slotId}|${dateStr}`;
  if (sliceCache[key]){ paintSlices(slotId,dateStr,sliceCache[key]); return; }

  const div=document.getElementById('sliceSelection');
  div.innerHTML='<div class="text-center p-3">Loading…</div>';
  try{
      const res = await fetch(`/api/appointments/${slotId}/slices?date=${dateStr}`);
      const json= await res.json();
      if (json.success){
          sliceCache[key]=json.slices;
          paintSlices(slotId,dateStr,json.slices);
      }else div.innerHTML='<div class="alert alert-danger">Unable to load timings.</div>';
  }catch{
      div.innerHTML='<div class="alert alert-danger">Network error.</div>';
  }
}

function paintSlices(slotId,dateStr,slices){
  const div=document.getElementById('sliceSelection');
  const colors=['slot-c1','slot-c2','slot-c3','slot-c4','slot-c5','slot-c6'];
  div.innerHTML = slices.map((s,i)=>`
      <div class="form-check">
        <input class="form-check-input" type="radio" name="slice_start"
               value="${s.start}" id="slice_${i}" ${s.is_booked?'disabled':''} required>
        <label class="form-check-label ${s.is_booked?'text-muted':''}" for="slice_${i}">
          <span class="slot-chip ${colors[i%colors.length]} me-2">${s.start}–${s.end}</span>
          ${s.is_booked?'<small>(Booked)</small>':''}
        </label>
      </div>`).join('');
  if (slices.length===0)
      div.innerHTML='<div class="alert alert-warning">No free 30-minute slices.</div>';
}

/* ─────────────────────────── BOOKING FLOW ───────────────────────── */
function openBookingFlow(id){
  if (!token){ bootstrap.Modal.getOrCreateInstance('#authSelectModal').show(); return; }
  selectedDoctorId=id;
  bootstrap.Modal.getOrCreateInstance('#bookingTypeModal').show();
}

function setupBookingForm(){
  /* preload patient info */
  const nameInp=document.getElementById('patientName');
  const emailInp=document.getElementById('patientEmail');
  const phoneInp=document.getElementById('patientPhone');
  const addrInp=document.getElementById('patientAddress');

  familyFields.forEach(el=> el.classList.toggle('d-none',selectedBookingType!=='family'));
  document.getElementById('bookingDoctorId').value = selectedDoctorId;

  if (selectedBookingType==='self' && userDetails){
      nameInp.value=userDetails.name||'';
      emailInp.value=userDetails.email||'';
      phoneInp.value=userDetails.phone||'';
      addrInp.value =userDetails.address||'';
      emailInp.readOnly = phoneInp.readOnly = true;
  }else{
      nameInp.value = '';
      emailInp.value=userDetails?.email||'';
      phoneInp.value=userDetails?.phone||'';
      addrInp.value='';
      emailInp.readOnly = phoneInp.readOnly = true;
  }

  /* build weekly-slot radio list */
  const selDiv=document.getElementById('slotSelection');
  const slots=slotCache[selectedDoctorId]||[];
  const colors=['slot-c1','slot-c2','slot-c3','slot-c4','slot-c5','slot-c6'];
  selDiv.innerHTML = slots.map((s,i)=>{
      const d=['Sun','Mon','Tue','Wed','Thu','Fri','Sat'][s.day_of_week];
      const t=`${s.start_time.slice(0,5)}–${s.end_time.slice(0,5)}`;
      return `<div class="form-check">
                <input class="form-check-input" type="radio" name="slot_id"
                       value="${s.id}" id="slot_${s.id}" required>
                <label class="form-check-label" for="slot_${s.id}">
                  <span class="slot-chip ${colors[i%colors.length]} me-2">${d}: ${t}</span>
                </label>
              </div>`;
  }).join('');
  if (slots.length===0)
      selDiv.innerHTML='<div class="alert alert-warning">No available slots for this doctor.</div>';
}

/* ───────────────────────── SUBMIT HANDLER ───────────────────────── */
function toggleBtnLoading(btn,loading){
  btn.disabled=loading;
  const spin=btn.querySelector('.spinner-border'),
        txt =btn.querySelector('.btn-text');
  if (spin){ spin.classList.toggle('d-none',!loading); txt.classList.toggle('d-none',loading); }
}

async function handleBookingSubmit(e){
  e.preventDefault();
  const btn=document.getElementById('bookingSubmitBtn');
  toggleBtnLoading(btn,true);
  const data=Object.fromEntries(new FormData(e.target));

  try{
      const res = await fetch('/api/bookings',{
          method:'POST',
          headers:{'Content-Type':'application/json','Authorization':`Bearer ${token}`},
          body:JSON.stringify(data)
      });
      const json= await res.json();
      if (res.status === 201) {
       // success …
       sliceCache[`${data.slot_id}|${data.appointment_date}`] = null;   // clear cache
       await loadSlices(data.slot_id, data.appointment_date);   
          bootstrap.Modal.getOrCreateInstance('#bookingFormModal').hide();
          Swal.fire({
              icon:'success',
              title:'Booking confirmed!',
              html:`Your booking token: <b>${json.booking_token}</b><br>
                    A confirmation e-mail has been sent.`
          }).then(()=> location.href='/');
      } else if (res.status === 409) {   // 👈 slice taken during the checkout
      Swal.fire({
          icon: 'warning',
         title: 'That time was just booked',
         text: json.message || 'Please choose another 30-minute slot.'
      });
      await loadSlices(data.slot_id, data.appointment_date);          // show new state
 } else {
          Swal.fire({icon:'error',title:'Booking Failed',text:json.message||'Please try again.'});
      }
  }catch{
      Swal.fire({icon:'error',title:'Error',text:'Network error. Try again.'});
  }finally{ toggleBtnLoading(btn,false); }
}

/* ─────────────────────────── AUTH HELPERS ───────────────────────── */
function setupAuthModals(){
  /* register */
  document.getElementById('registerForm').addEventListener('submit', async e=>{
      e.preventDefault();
      const btn=document.getElementById('registerSubmitBtn'); toggleBtnLoading(btn,true);
      const data=Object.fromEntries(new FormData(e.target));
      const res = await fetch('/api/users/register',{
          method:'POST',
          headers:{'Content-Type':'application/json','Accept':'application/json'},
          body:JSON.stringify(data)
      });
      const json=await res.json(); toggleBtnLoading(btn,false);
      if (res.status===201){
          sessionStorage.setItem('token',json.access_token);
          bootstrap.Modal.getOrCreateInstance('#registerModal').hide();
          Swal.fire({icon:'success',title:'Registered'}).then(()=>location.reload());
      }else{
          Swal.fire({icon:'error',title:'Register Failed',text:json.message||'Check details'});
      }
  });

  /* login */
  document.getElementById('loginForm').addEventListener('submit',async e=>{
      e.preventDefault();
      const btn=document.getElementById('loginSubmitBtn'); toggleBtnLoading(btn,true);
      const data=Object.fromEntries(new FormData(e.target));
      const res = await fetch('/api/users/login',{
          method:'POST',
          headers:{'Content-Type':'application/json','Accept':'application/json'},
          body:JSON.stringify(data)
      });
      const json=await res.json(); toggleBtnLoading(btn,false);
      if (res.status===200){
          sessionStorage.setItem('token',json.access_token);
          bootstrap.Modal.getOrCreateInstance('#loginModal').hide();
          Swal.fire({icon:'success',title:'Logged in'}).then(()=>location.reload());
      }else{
          Swal.fire({icon:'error',title:'Login Failed',text:json.message||'Wrong credentials'});
      }
  });
}

/* ─────────────────────── DATE HELPERS ───────────────────────────── */
function nextDateForDow(dow){
  const now=new Date();
  const diff=(dow-now.getDay()+7)%7||7; // if today==dow, pick next week
  const t=new Date(now); t.setDate(now.getDate()+diff);
  return t.toISOString().split('T')[0];
}
</script>
@endsection
