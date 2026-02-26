@extends('users.admin.components.structure')

@section('title','Manage Doctors')

@push('styles')
<link rel="stylesheet" href="{{ asset('css/pages/doctor/manageDoctor.css') }}">

<style>
  .doc-img{width:60px;height:60px;object-fit:cover;border-radius:50%}
  .modal-header{background:var(--primary-color);color:#fff}
  .toggle-password{cursor:pointer;color:#6b7280}
  .input-group>.form-control{padding-right:2.75rem}  /* space for eye */
</style>
@endpush



@section('content')
{{-- ───── Header ───── --}}
<div class="rounded-3 p-4 mb-4">
  <div class="d-flex flex-column flex-md-row justify-content-between align-items-center gap-3">
    <ol class="breadcrumb mb-0">
      <li class="breadcrumb-item"><a href="#"><i class="fas fa-user-md me-1"></i>Doctor</a></li>
      <li class="breadcrumb-item active">Manage</li>
    </ol>

    <div class="d-flex align-items-center gap-3">
      <div class="d-none d-sm-flex align-items-center gap-2 px-3 py-2 bg-white rounded-3 text-muted">
        <i class="far fa-calendar-alt"></i><span id="currentDate"></span>
      </div>
      <button id="openCreateBtn" class="btn gradient-btn text-white hover-lift">
        <i class="fas fa-plus me-2"></i>Add Doctor
      </button>
    </div>
  </div><hr>
</div>

{{-- ───── Stats ───── --}}
<div class="row mb-4">
  <div class="col-md-4"><div class="glass-effect rounded-3 p-4 h-100 hover-lift">
    <div class="d-flex justify-content-between align-items-center">
      <div><p class="small text-muted mb-1">Total</p><h3 id="totalDocs" class="fw-bold mb-0">0</h3></div>
      <div class="p-3 bg-blue-100 rounded-3"><i class="fas fa-user-md text-primary text-30"></i></div>
    </div></div></div>

  <div class="col-md-4"><div class="glass-effect rounded-3 p-4 h-100 hover-lift">
    <div class="d-flex justify-content-between align-items-center">
      <div><p class="small text-muted mb-1">Active</p><h3 id="activeDocs" class="fw-bold text-success mb-0">0</h3></div>
      <div class="p-3 bg-green-100 rounded-3"><i class="fas fa-check-circle text-success text-30"></i></div>
    </div></div></div>

  <div class="col-md-4"><div class="glass-effect rounded-3 p-4 h-100 hover-lift">
    <div class="d-flex justify-content-between align-items-center">
      <div><p class="small text-muted mb-1">Inactive</p><h3 id="inactiveDocs" class="fw-bold text-warning mb-0">0</h3></div>
      <div class="p-3 bg-amber-100 rounded-3"><i class="fas fa-pause-circle text-warning text-30"></i></div>
    </div></div></div>
</div>

{{-- ───── Filters ───── --}}
<div class="glass-effect rounded-3 p-4 mb-4 shadow-sm">
  <div class="row">
    <div class="col-md-8 mb-3 mb-md-0">
      <div class="position-relative">
        <i class="fas fa-search position-absolute top-50 start-0 translate-middle-y ms-3 text-muted"></i>
        <input id="searchInput" class="form-control ps-5" placeholder="Search doctor by name…">
      </div>
    </div>
    <div class="col-md-4">
      <select id="statusFilter" class="form-select">
        <option value="all">All Status</option>
        <option value="active">Active</option>
        <option value="inactive">Inactive</option>
      </select>
    </div>
  </div>
</div>

{{-- ───── Table ───── --}}
<div class="glass-effect rounded-3 shadow-sm overflow-hidden">
  <div class="table-responsive">
    <table class="table table-hover mb-0">
      <thead class="table-light">
        <tr><th>Image</th><th>Name</th><th>Charges (₹)</th><th>Status</th><th class="text-center">Actions</th></tr>
      </thead>
      <tbody id="tbody"></tbody>
      <tbody id="spinnerRow">
        <tr><td colspan="5" class="py-4 text-center">
          <div class="d-flex flex-column align-items-center gap-3">
            <div class="spinner-border text-primary"></div>
            <p class="text-muted fw-medium">Loading doctors…</p>
          </div>
        </td></tr>
      </tbody>
    </table>
  </div>
</div>

{{-- ───── Add Doctor Modal ───── --}}
<div class="modal fade" id="createModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-xl"><div class="modal-content">
    <div class="modal-header"><h5 class="modal-title">Add Doctor</h5>
      <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
    </div>

    <form id="createForm" novalidate>
      <div class="modal-body row g-3">
        {{-- column 1 --}}
        <div class="col-md-4"><label class="form-label">Department *</label><select id="cDept" class="form-select" required></select></div>
        <div class="col-md-4"><label class="form-label">First Name *</label><input id="cFirst" class="form-control" required></div>
        <div class="col-md-4"><label class="form-label">Last Name *</label><input id="cLast" class="form-control" required></div>

        <div class="col-md-4"><label class="form-label">Email *</label><input id="cEmail" type="email" class="form-control" required></div>
        <div class="col-md-4">
          <label class="form-label">Password *</label>
          <div class="input-group">
            <input id="cPass" type="password" class="form-control" required>
            <span class="input-group-text bg-white border-start-0">
              <i class="fas fa-eye-slash toggle-password" data-target="cPass"></i>
            </span>
          </div>
        </div>
        <div class="col-md-4"><label class="form-label">Phone</label><input id="cPhone" class="form-control"></div>

        <div class="col-md-4"><label class="form-label">Sex</label>
          <select id="cSex" class="form-select"><option value="" selected>-</option><option value="male">Male</option><option value="female">Female</option><option value="other">Other</option></select>
        </div>
        <div class="col-md-4"><label class="form-label">Specialty</label><input id="cSpecialty" class="form-control"></div>
        <div class="col-md-4"><label class="form-label">Degree</label><input id="cDegree" class="form-control"></div>

        <div class="col-md-4"><label class="form-label">Home Town</label><input id="cHome" class="form-control"></div>
        <div class="col-md-4"><label class="form-label">Visiting Charge (₹)</label><input id="cVCharge" type="number" min="0" class="form-control"></div>
        <div class="col-md-4"><label class="form-label">Consultation Charge (₹)</label><input id="cCCharge" type="number" min="0" class="form-control"></div>

        <div class="col-md-6"><label class="form-label">Address</label><textarea id="cAddr" rows="2" class="form-control"></textarea></div>
        <div class="col-md-6"><label class="form-label">Office Address</label><textarea id="cOffAddr" rows="2" class="form-control"></textarea></div>

        <div class="col-md-12"><label class="form-label">Profile Image</label><input id="cImg" type="file" accept="image/*" class="form-control"></div>
      </div>

      <div class="modal-footer">
        <button class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
        <button id="createSaveBtn" type="button" class="btn btn-primary">Create</button>
      </div>
    </form>
  </div></div>
</div>

{{-- ───── Edit Doctor Modal ───── --}}
<div class="modal fade" id="editModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-xl"><div class="modal-content">
    <div class="modal-header"><h5 id="eTitle" class="modal-title">Edit Doctor</h5>
      <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
    </div>

    <form id="editForm" novalidate>
      <div class="modal-body row g-3">
        <input id="eId" type="hidden">

        <div class="col-md-4"><label class="form-label">Department *</label><select id="eDept" class="form-select" required></select></div>
        <div class="col-md-4"><label class="form-label">First Name *</label><input id="eFirst" class="form-control" required></div>
        <div class="col-md-4"><label class="form-label">Last Name *</label><input id="eLast" class="form-control" required></div>

        <div class="col-md-4"><label class="form-label">Email *</label><input id="eEmail" type="email" class="form-control" required></div>
        <div class="col-md-4">
          <label class="form-label">Password (leave blank to keep)</label>
          <div class="input-group">
            <input id="ePass" type="password" class="form-control">
            <span class="input-group-text bg-white border-start-0">
              <i class="fas fa-eye-slash toggle-password" data-target="ePass"></i>
            </span>
          </div>
        </div>
        <div class="col-md-4"><label class="form-label">Phone</label><input id="ePhone" class="form-control"></div>

        <div class="col-md-4"><label class="form-label">Sex</label>
          <select id="eSex" class="form-select"><option value="">-</option><option value="male">Male</option><option value="female">Female</option><option value="other">Other</option></select>
        </div>
        <div class="col-md-4"><label class="form-label">Specialty</label><input id="eSpecialty" class="form-control"></div>
        <div class="col-md-4"><label class="form-label">Degree</label><input id="eDegree" class="form-control"></div>

        <div class="col-md-4"><label class="form-label">Home Town</label><input id="eHome" class="form-control"></div>
        <div class="col-md-4"><label class="form-label">Visiting Charge (₹)</label><input id="eVCharge" type="number" min="0" class="form-control"></div>
        <div class="col-md-4"><label class="form-label">Consultation Charge (₹)</label><input id="eCCharge" type="number" min="0" class="form-control"></div>

        <div class="col-md-6"><label class="form-label">Address</label><textarea id="eAddr" rows="2" class="form-control"></textarea></div>
        <div class="col-md-6"><label class="form-label">Office Address</label><textarea id="eOffAddr" rows="2" class="form-control"></textarea></div>

        <div class="col-md-4"><label class="form-label">Status</label>
          <select id="eStatus" class="form-select"><option value="yes">Active</option><option value="no">Inactive</option></select>
        </div>

        <div class="col-md-8">
          <label class="form-label">Profile Image</label>
          <input id="eImg" type="file" accept="image/*" class="form-control">
          <img id="ePreview" class="img-fluid mt-3 rounded-3 shadow-sm d-none" style="max-height:200px">
        </div>
      </div>

      <div class="modal-footer">
        <button class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
        <button id="editSaveBtn" type="button" class="btn btn-primary">Save</button>
      </div>
    </form>
  </div></div>
</div>
@endsection



@push('scripts')

<script>
/* ═════════════════ CONSTANTS & DATE ════════════════ */
const TOKEN   = sessionStorage.getItem('token');
const HEADERS = { Accept : 'application/json',
                  Authorization : `Bearer ${TOKEN}` };
const CSRF    = document.querySelector('meta[name="csrf-token"]')?.content;

document.getElementById('currentDate').textContent =
  new Date().toLocaleDateString('en-US',
    { weekday:'short', month:'short', day:'numeric' });

/* ═════════════════ STATE ════════════════ */
let doctors = [], departments = [];
const tbody   = document.getElementById('tbody');
const spinner = document.getElementById('spinnerRow');

/* ═════════════════ PASSWORD EYE TOGGLE ═════════════ */
document.addEventListener('click', e => {
  if (!e.target.classList.contains('toggle-password')) return;
  const inp   = document.getElementById(e.target.dataset.target);
  const show  = inp.type === 'password';
  inp.type    = show ? 'text' : 'password';
  e.target.classList.toggle('fa-eye',       show);
  e.target.classList.toggle('fa-eye-slash', !show);
});

/* ═════════════════ LOADERS ═════════════ */
async function loadDepartments () {
  const res = await fetch('/api/departments', { headers: HEADERS });
  departments = (await res.json()).departments ?? [];

  const opts = departments
      .map(d => `<option value="${d.id}">${d.title}</option>`)
      .join('');
  document.getElementById('cDept').innerHTML = '<option disabled selected>Choose Department</option>' + opts;
  document.getElementById('eDept').innerHTML = opts;
}

async function loadDoctors () {
  const res = await fetch('/api/doctors', { headers: HEADERS });
  doctors = (await res.json()).doctors ?? [];
  render();
}

/* ═════════════════ RENDER TABLE & STATS ═══════════ */
const fullName = d => `${d.first_name} ${d.last_name}`.trim();

function rowHTML (d) {
  const dept   = departments.find(x => x.id === d.department_id)?.title ?? '';
  const active = d.is_active;

  return `<tr data-name="${fullName(d).toLowerCase()}"
              data-status="${active ? 'active' : 'inactive'}">
    <td class="align-middle">
      <img src="{{ asset('') }}${d.image_url ?? 'assets/images/placeholder.png'}"
           class="doc-img">
    </td>

    <td class="align-middle fw-semibold">
      ${fullName(d)}
      <br><small class="text-muted">${dept}</small>
    </td>

    <td class="align-middle">₹${d.visiting_charge ?? 0}
        / ₹${d.consultation_charge ?? 0}</td>

    <td class="align-middle">
      ${active
        ? '<span class="badge bg-success-subtle text-success">Active</span>'
        : '<span class="badge bg-warning-subtle text-warning">Inactive</span>'}
    </td>

    <td class="align-middle">
      <div class="d-flex justify-content-center gap-2">
        <button class="btn btn-sm btn-outline-info"
                title="Info" onclick="showInfo(${d.id})">
          <i class="fas fa-info"></i></button>

        <button class="btn btn-sm btn-outline-primary"
                title="Edit" onclick="openEdit(${d.id})">
          <i class="fas fa-edit"></i></button>

        <button class="btn btn-sm btn-outline-secondary"
                title="Toggle" onclick="toggleStatus(${d.id})">
          <i class="fas ${active
                ? 'fa-toggle-on text-success'
                : 'fa-toggle-off text-warning'}"></i></button>

        <button class="btn btn-sm btn-outline-danger"
                title="Delete" onclick="deleteDoc(${d.id})">
          <i class="fas fa-trash-alt"></i></button>

        <a href="/admin/appointment/create?doctor_id=${d.id}"
           class="btn btn-sm btn-outline-success"
           title="Schedule">
          <i class="fas fa-calendar-plus"></i></a>
      </div>
    </td>
  </tr>`;
}

function render () {
  tbody.innerHTML = doctors.map(rowHTML).join('');
  spinner.classList.add('d-none');

  const total = doctors.length,
        act   = doctors.filter(d => d.is_active).length;

  document.getElementById('totalDocs').textContent    = total;
  document.getElementById('activeDocs').textContent   = act;
  document.getElementById('inactiveDocs').textContent = total - act;
}

/* ═════════════════ FILTERS ═════════════ */
document.getElementById('searchInput').oninput   = filterRows;
document.getElementById('statusFilter').onchange = filterRows;

function filterRows () {
  const q = document.getElementById('searchInput').value.toLowerCase();
  const s = document.getElementById('statusFilter').value;

  tbody.querySelectorAll('tr').forEach(tr => {
    const ok = tr.dataset.name.includes(q) &&
              (s === 'all' || tr.dataset.status === s);
    tr.classList.toggle('d-none', !ok);
  });
}

/* ═════════════════ INFO POP-UP ═════════════ */
function showInfo (id) {
  const d = doctors.find(x => x.id === id);  if (!d) return;
  const dept = departments.find(x => x.id === d.department_id)?.title ?? '';

  Swal.fire({
    title      : fullName(d),
    imageUrl   : `{{ asset('') }}${d.image_url ?? 'assets/images/placeholder.png'}`,
    imageWidth : 100,
    imageHeight: 100,
    html : `<div class="text-start">
      <p><strong>Department:</strong> ${dept}</p>
      <p><strong>Email:</strong> ${d.email}</p>
      <p><strong>Phone:</strong> ${d.phone ?? '-'}</p>
      <p><strong>Visiting Charge:</strong> ₹${d.visiting_charge ?? 0}</p>
      <p><strong>Consult. Charge:</strong> ₹${d.consultation_charge ?? 0}</p>
      <p><strong>Specialty:</strong> ${d.specialty ?? '-'}</p>
      <p><strong>Degree:</strong> ${d.degree ?? '-'}</p>
      <p><strong>Sex:</strong> ${d.sex ?? '-'}</p>
      <p><strong>Home Town:</strong> ${d.home_town ?? '-'}</p>
      <p><strong>Address:</strong> ${d.address ?? '-'}</p>
      <p><strong>Office Address:</strong> ${d.office_address ?? '-'}</p>
      <p><strong>Status:</strong> ${d.is_active ? 'Active' : 'Inactive'}</p>
    </div>`
  });
}

/* ═════════════════ TOGGLE STATUS ═════════════ */
async function toggleStatus (id) {
  const d = doctors.find(x => x.id === id); if (!d) return;

  const ok = await Swal.fire({
    title: 'Change status?',
    text : `Doctor will become ${d.is_active ? 'inactive' : 'active'}.`,
    icon : 'question',
    showCancelButton : true,
    confirmButtonColor: '#0d9488'
  });
  if (!ok.isConfirmed) return;

  try {
    const r = await fetch(`/api/doctors/${id}/toggle-active`, {
      method : 'PATCH',
      headers: CSRF ? { ...HEADERS, 'X-CSRF-TOKEN': CSRF } : HEADERS
    });
    const j = await r.json();
    if (!r.ok || !j.success) throw new Error(j.message || 'Toggle failed');

    d.is_active = !d.is_active;
    render();
  }
  catch (err) { Swal.fire('Error', err.message, 'error'); }
}

/* ═════════════════ DELETE ═════════════ */
async function deleteDoc (id) {
  const ok = await Swal.fire({
    title: 'Delete?',
    text : 'This action cannot be undone',
    icon : 'warning',
    showCancelButton: true,
    confirmButtonColor: '#ef4444'
  });
  if (!ok.isConfirmed) return;

  try {
    const r = await fetch(`/api/doctors/${id}`, {
      method : 'DELETE',
      headers: CSRF ? { ...HEADERS, 'X-CSRF-TOKEN': CSRF } : HEADERS
    });
    const j = await r.json();
    if (!r.ok || !j.success) throw new Error(j.message || 'Delete failed');

    doctors = doctors.filter(x => x.id !== id);
    render();
    Swal.fire('Deleted!', 'Doctor removed.', 'success');
  }
  catch (err) { Swal.fire('Error', err.message, 'error'); }
}

/* ═════════════════ HELPER: append only when value present ═════════ */
function addIf (fd, key, val) {
  if (val !== '' && val !== null && val !== undefined) fd.append(key, val);
}

/* ═════════════════ Build FormData ═════════════ */
function buildFD (pref) {                 // pref = 'c' or 'e'
  const F = id => document.getElementById(pref + id);
  const fd = new FormData();

  addIf(fd, 'department_id',        F('Dept').value);
  addIf(fd, 'first_name',           F('First').value);
  addIf(fd, 'last_name',            F('Last').value);
  addIf(fd, 'email',                F('Email').value);

  if (pref === 'c' || F('Pass').value)
      addIf(fd, 'password', F('Pass').value);

  addIf(fd, 'phone',                F('Phone').value);
  addIf(fd, 'sex',                  F('Sex').value);
  addIf(fd, 'specialty',            F('Specialty').value);
  addIf(fd, 'degree',               F('Degree').value);
  addIf(fd, 'home_town',            F('Home').value);
  addIf(fd, 'address',              F('Addr').value);
  addIf(fd, 'office_address',       F('OffAddr').value);

  if (F('VCharge').value)
      addIf(fd, 'visiting_charge',      Number(F('VCharge').value));
  if (F('CCharge').value)
      addIf(fd, 'consultation_charge',  Number(F('CCharge').value));

  if (pref === 'e')
      addIf(fd, 'is_active', F('Status').value === 'yes' ? 1 : 0);

  const img = F('Img').files[0];  if (img) fd.append('image', img);

  return fd;
}

/* ═════════════════ CREATE ═════════════ */
const createModal = new bootstrap.Modal('#createModal');
document.getElementById('openCreateBtn').onclick = () => createModal.show();

document.getElementById('createSaveBtn').onclick = async e => {
  const fd  = buildFD('c');
  const btn = e.currentTarget;
  btn.disabled = true;
  btn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Saving…';

  try {
    const r = await fetch('/api/doctors/signup', {
      method : 'POST',
      headers: CSRF ? { ...HEADERS, 'X-CSRF-TOKEN': CSRF } : HEADERS,
      body   : fd
    });
    const j = await r.json();
    if (!r.ok || !j.success) throw new Error(j.message || 'Create failed');

    await Swal.fire({ icon:'success', title:'Doctor added', timer:1200, showConfirmButton:false });
    createModal.hide();
    document.getElementById('createForm').reset();
    loadDoctors();
  }
  catch (err) { Swal.fire('Error', err.message, 'error'); }
  finally { btn.disabled = false; btn.textContent = 'Create'; }
};

/* ═════════════════ OPEN EDIT ═════════════ */
const editModal = new bootstrap.Modal('#editModal');

function openEdit (id) {
  const d = doctors.find(x => x.id === id); if (!d) return;

  document.getElementById('eId').value        = id;
  document.getElementById('eDept').value      = d.department_id;
  document.getElementById('eFirst').value     = d.first_name;
  document.getElementById('eLast').value      = d.last_name;
  document.getElementById('eEmail').value     = d.email;
  document.getElementById('ePhone').value     = d.phone ?? '';
  document.getElementById('eSex').value       = d.sex ?? '';
  document.getElementById('eSpecialty').value = d.specialty ?? '';
  document.getElementById('eDegree').value    = d.degree ?? '';
  document.getElementById('eHome').value      = d.home_town ?? '';
  document.getElementById('eAddr').value      = d.address ?? '';
  document.getElementById('eOffAddr').value   = d.office_address ?? '';
  document.getElementById('eVCharge').value   = d.visiting_charge ?? '';
  document.getElementById('eCCharge').value   = d.consultation_charge ?? '';
  document.getElementById('eStatus').value    = d.is_active ? 'yes' : 'no';

  const prev = document.getElementById('ePreview');
  if (d.image_url) {
    prev.src = '{{ asset('') }}' + d.image_url;
    prev.classList.remove('d-none');
  } else
    prev.classList.add('d-none');

  editModal.show();
}

/* show preview on image change */
document.getElementById('eImg').onchange = e => {
  const f   = e.target.files[0],
        img = document.getElementById('ePreview');
  if (f) {
    img.src = URL.createObjectURL(f);
    img.classList.remove('d-none');
  }
};

/* ═════════════════ SAVE EDIT ═════════════ */
document.getElementById('editSaveBtn').onclick = async e => {
  const id  = document.getElementById('eId').value;
  const fd  = buildFD('e');  fd.append('_method', 'PUT');

  const btn = e.currentTarget;
  btn.disabled = true;
  btn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Saving…';

  try {
    const r = await fetch(`/api/doctors/${id}`, {
      method : 'POST',
      headers: CSRF ? { ...HEADERS, 'X-CSRF-TOKEN': CSRF } : HEADERS,
      body   : fd
    });
    const j = await r.json();
    if (!r.ok || !j.success) throw new Error(j.message || 'Update failed');

    await Swal.fire({ icon:'success', title:'Updated', timer:1200, showConfirmButton:false });
    editModal.hide();
    loadDoctors();
  }
  catch (err) { Swal.fire('Error', err.message, 'error'); }
  finally { btn.disabled = false; btn.textContent = 'Save'; }
};

/* ═════════════════ INIT ═════════════ */
(async () => {
  await loadDepartments();
  await loadDoctors();
})();
</script>
@endpush
