{{-- ───────────────────────────────────────────────────────────────
    Manage Departments ‒ single file
    Extends users.admin.components.structure (header, sidebar, etc.)
   ─────────────────────────────────────────────────────────────── --}}
@extends('users.admin.components.structure')

@section('title', 'Manage Departments')

@push('styles')
<link rel="stylesheet" href="{{ asset('css/pages/department/department.css') }}">
<style>
  .exam-img    { width:60px; height:60px; object-fit:cover; border-radius:6px; }
  .modal-header{ background-color:var(--primary-color); color:#fff }
</style>
@endpush


@section('content')
{{-- ───────── Breadcrumb + “Create” button ───────── --}}
<div class="rounded-3 p-4 mb-4">
  <div class="d-flex flex-column flex-md-row justify-content-between align-items-center gap-3">
    <ol class="breadcrumb mb-0">
      <li class="breadcrumb-item">
        <a href="#"><i class="fas fa-building me-1"></i>Department</a>
      </li>
      <li class="breadcrumb-item active">Manage</li>
    </ol>

    <div class="d-flex align-items-center gap-3">
      <div class="d-none d-sm-flex align-items-center gap-2 px-3 py-2 bg-white rounded-3 text-muted">
        <i class="far fa-calendar-alt"></i>
        <span id="currentDate"></span>
      </div>
      <button id="openCreateBtn" class="btn gradient-btn text-white hover-lift">
        <i class="fas fa-plus me-2"></i>Create Department
      </button>
    </div>
  </div>
  <hr>
</div>

{{-- ───────── Stats cards ───────── --}}
<div class="row mb-4">
  <div class="col-md-4 stats-card">
    <div class="glass-effect rounded-3 p-4 hover-lift h-100">
      <div class="d-flex justify-content-between align-items-center">
        <div>
          <p class="text-muted small mb-1">Total</p>
          <h3 id="totalDepts" class="fw-bold mb-0">0</h3>
        </div>
        <div class="p-3 bg-blue-100 rounded-3">
          <i class="fas fa-database text-primary text-30"></i>
        </div>
      </div>
    </div>
  </div>

  <div class="col-md-4 stats-card">
    <div class="glass-effect rounded-3 p-4 hover-lift h-100">
      <div class="d-flex justify-content-between align-items-center">
        <div>
          <p class="text-muted small mb-1">Active</p>
          <h3 id="activeDepts" class="fw-bold text-success mb-0">0</h3>
        </div>
        <div class="p-3 bg-green-100 rounded-3">
          <i class="fas fa-check-circle text-success text-30"></i>
        </div>
      </div>
    </div>
  </div>

  <div class="col-md-4 stats-card">
    <div class="glass-effect rounded-3 p-4 hover-lift h-100">
      <div class="d-flex justify-content-between align-items-center">
        <div>
          <p class="text-muted small mb-1">Inactive</p>
          <h3 id="inactiveDepts" class="fw-bold text-warning mb-0">0</h3>
        </div>
        <div class="p-3 bg-amber-100 rounded-3">
          <i class="fas fa-pause-circle text-warning text-30"></i>
        </div>
      </div>
    </div>
  </div>
</div>

{{-- ───────── Filters ───────── --}}
<div class="glass-effect rounded-3 p-4 mb-4 shadow-sm">
  <div class="row filter-section">
    <div class="col-md-8 mb-3 mb-md-0">
      <div class="position-relative">
        <i class="fas fa-search position-absolute top-50 start-0 translate-middle-y ms-3 text-muted"></i>
        <input id="searchInput" type="text" class="form-control ps-5"
               placeholder="Search department by name…">
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

{{-- ───────── Table ───────── --}}
<div class="glass-effect rounded-3 shadow-sm overflow-hidden">
  <div class="table-responsive">
    <table class="table table-hover mb-0">
      <thead class="table-light">
        <tr>
          <th>Name</th><th>Description</th><th>Image</th>
          <th>Status</th><th class="text-center">Actions</th>
        </tr>
      </thead>
      <tbody id="tbody"></tbody>
      <tbody id="spinnerRow">
        <tr>
          <td colspan="5" class="py-4 text-center">
            <div class="d-flex flex-column align-items-center gap-3">
              <div class="spinner-border text-primary"></div>
              <p class="text-muted fw-medium">Loading departments…</p>
            </div>
          </td>
        </tr>
      </tbody>
    </table>
  </div>
</div>

{{-- ───────── Create modal ───────── --}}
<div class="modal fade" id="createModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">Create Department</h5>
        <button type="button" class="btn-close btn-close-white"
                data-bs-dismiss="modal"></button>
      </div>
      <form id="createForm">
        <div class="modal-body">
          <div class="mb-3">
            <label class="form-label">Name</label>
            <input id="createName" type="text" class="form-control" required>
          </div>
          <div class="mb-3">
            <label class="form-label">Description</label>
            <textarea id="createDesc" class="form-control" rows="3"></textarea>
          </div>
          <div class="mb-3">
            <label class="form-label">Image</label>
            <input id="createImg" type="file" accept="image/*" class="form-control">
          </div>
        </div>
        <div class="modal-footer">
          <button class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
          <button id="createSaveBtn" type="button" class="btn btn-primary">Create</button>
        </div>
      </form>
    </div>
  </div>
</div>

{{-- ───────── Edit modal ───────── --}}
<div class="modal fade" id="editModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      <div class="modal-header">
        <h5 id="modalTitle" class="modal-title">Edit Department</h5>
        <button type="button" class="btn-close btn-close-white"
                data-bs-dismiss="modal"></button>
      </div>
      <form id="editForm">
        <div class="modal-body">
          <input id="editId" type="hidden">
          <div class="mb-3">
            <label class="form-label">Name</label>
            <input id="editName" type="text" class="form-control" required>
          </div>
          <div class="mb-3">
            <label class="form-label">Description</label>
            <textarea id="editDesc" class="form-control" rows="3"></textarea>
          </div>
          <div class="mb-3">
            <label class="form-label">Image</label>
            <input id="editImg" type="file" accept="image/*" class="form-control">
            <img id="imgPreview"
                 class="img-fluid mt-3 rounded-3 shadow-sm d-none"
                 style="max-height:200px">
          </div>
          <div class="mb-3">
            <label class="form-label">Status</label>
            <select id="editStatus" class="form-select">
              <option value="active">Active</option>
              <option value="inactive">Inactive</option>
            </select>
          </div>
        </div>
        <div class="modal-footer">
          <button class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
          <button id="editSaveBtn" type="button" class="btn btn-primary">Save</button>
        </div>
      </form>
    </div>
  </div>
</div>
@endsection


@push('scripts')
<script>
/* ───── constant helpers ───── */
const TOKEN   = sessionStorage.getItem('token');
const HEADERS = { Accept: 'application/json',
                  Authorization: `Bearer ${TOKEN}` };

/* CSRF header if meta tag exists (avoids null error) */
const CSRF = document.querySelector('meta[name="csrf-token"]')?.content;

/* current date in header */
document.getElementById('currentDate').textContent =
  new Date().toLocaleDateString('en-US',
  { weekday:'short', month:'short', day:'numeric' });

/* ───── state ───── */
let departments = [];
const tbody   = document.getElementById('tbody');
const spinner = document.getElementById('spinnerRow');


/* ───── stats & render ───── */
function drawStats () {
  const total   = departments.length;
  const active  = departments.filter(d => d.status === 'active').length;
  document.getElementById('totalDepts').textContent   = total;
  document.getElementById('activeDepts').textContent  = active;
  document.getElementById('inactiveDepts').textContent= total - active;
}

function drawTable () {
  tbody.innerHTML = departments.map(d => {
    const badge = d.status === 'active'
      ? '<span class="badge bg-success-subtle text-success">Active</span>'
      : '<span class="badge bg-warning-subtle text-warning">Inactive</span>';
    return `
      <tr data-name="${d.title.toLowerCase()}" data-status="${d.status}">
        <td class="align-middle fw-semibold">${d.title}</td>
        <td class="align-middle text-muted">${d.description ?? '-'}</td>
        <td class="align-middle">${
          d.image_url
            ? `<img src="{{ asset('') }}${d.image_url}" class="exam-img">`
            : '—'
        }</td>
        <td class="align-middle">${badge}</td>
        <td class="align-middle">
          <div class="d-flex justify-content-center gap-2">
            <button class="btn btn-sm btn-outline-primary"
                    onclick="openEdit(${d.id})">
              <i class="fas fa-edit"></i>
            </button>
            <button class="btn btn-sm btn-outline-danger"
                    onclick="deleteDept(${d.id})">
              <i class="fas fa-trash-alt"></i>
            </button>
          </div>
        </td>
      </tr>`;
  }).join('');
  spinner.classList.add('d-none');
}


/* ───── load list ───── */
async function loadList () {
  try {
    const r = await fetch('/api/departments', { headers: HEADERS });
    const j = await r.json();
    if (!r.ok || !j.success) throw new Error(j.message || 'Load failed');
    departments = j.departments;
    drawTable(); drawStats();
  } catch (e) {
    spinner.innerHTML =
      `<tr><td colspan="5" class="py-4 text-center text-danger">${e.message}</td></tr>`;
  }
}

/* ───── filtering ───── */
document.getElementById('searchInput').addEventListener('input', filterRows);
document.getElementById('statusFilter').addEventListener('change', filterRows);

function filterRows () {
  const q  = document.getElementById('searchInput').value.toLowerCase();
  const st = document.getElementById('statusFilter').value;
  tbody.querySelectorAll('tr').forEach(tr => {
    const okName = tr.dataset.name.includes(q);
    const okStat = st === 'all' || tr.dataset.status === st;
    tr.classList.toggle('d-none', !(okName && okStat));
  });
}

/* ───── create modal ───── */
const createModal = new bootstrap.Modal('#createModal');
document.getElementById('openCreateBtn').onclick = () => createModal.show();

document.getElementById('createSaveBtn').onclick = async e => {
  const fd = new FormData();
  fd.append('title',       document.getElementById('createName').value);
  fd.append('description', document.getElementById('createDesc').value);
  const img = document.getElementById('createImg').files[0];
  if (img) fd.append('image', img);

  const btn = e.currentTarget;
  btn.disabled = true;
  btn.innerHTML =
    '<span class="spinner-border spinner-border-sm me-2"></span>Creating…';

  try {
    const r = await fetch('/api/departments', {
      method: 'POST',
      headers: CSRF ? { ...HEADERS, 'X-CSRF-TOKEN': CSRF } : HEADERS,
      body: fd
    });
    const j = await r.json();
    if (!r.ok || !j.success) throw new Error(j.message || 'Create failed');

    await Swal.fire({ icon:'success', title:'Created', timer:1200,
                      showConfirmButton:false });
    createModal.hide();
    document.getElementById('createForm').reset();
    loadList();
  } catch (err) {
    Swal.fire('Error', err.message, 'error');
  } finally {
    btn.disabled = false;
    btn.textContent = 'Create';
  }
};

/* ───── delete ───── */
async function deleteDept (id) {
  const ok = await Swal.fire({
    title: 'Delete?', text:'This cannot be undone.', icon:'warning',
    showCancelButton:true, confirmButtonColor:'#ef4444'
  });
  if (!ok.isConfirmed) return;

  try {
    const r = await fetch(`/api/departments/${id}`, {
      method: 'DELETE',
      headers: CSRF ? { ...HEADERS, 'X-CSRF-TOKEN': CSRF } : HEADERS
    });
    const j = await r.json();
    if (!r.ok || !j.success) throw new Error(j.message || 'Delete failed');

    departments = departments.filter(d => d.id !== id);
    drawTable(); drawStats();
    Swal.fire('Deleted!', 'Department removed.', 'success');
  } catch (err) {
    Swal.fire('Error', err.message, 'error');
  }
}

/* ───── edit modal ───── */
const editModal = new bootstrap.Modal('#editModal');

function openEdit (id) {
  const d = departments.find(x => x.id === id);
  if (!d) return;

  document.getElementById('modalTitle').textContent = `Edit: ${d.title}`;
  document.getElementById('editId').value           = id;
  document.getElementById('editName').value         = d.title;
  document.getElementById('editDesc').value         = d.description ?? '';
  document.getElementById('editStatus').value       = d.status;

  const prev = document.getElementById('imgPreview');
  if (d.image_url) {
    prev.src = `{{ asset('') }}${d.image_url}`;
    prev.classList.remove('d-none');
  } else prev.classList.add('d-none');

  editModal.show();
}

document.getElementById('editImg').onchange = e => {
  const f = e.target.files[0];
  const prev = document.getElementById('imgPreview');
  if (f) { prev.src = URL.createObjectURL(f); prev.classList.remove('d-none'); }
};

document.getElementById('editSaveBtn').onclick = async e => {
  const id = document.getElementById('editId').value;
  const fd = new FormData();
  fd.append('_method', 'PUT');
  fd.append('title',       document.getElementById('editName').value);
  fd.append('description', document.getElementById('editDesc').value);
  fd.append('status',      document.getElementById('editStatus').value);
  const img = document.getElementById('editImg').files[0];
  if (img) fd.append('image', img);

  const btn = e.currentTarget;
  btn.disabled = true;
  btn.innerHTML =
    '<span class="spinner-border spinner-border-sm me-2"></span>Saving…';

  try {
    const r = await fetch(`/api/departments/${id}`, {
      method: 'POST',
      headers: CSRF ? { ...HEADERS, 'X-CSRF-TOKEN': CSRF } : HEADERS,
      body: fd
    });
    const j = await r.json();
    if (!r.ok || !j.success) throw new Error(j.message || 'Update failed');

    await Swal.fire({ icon:'success', title:'Updated', timer:1200,
                      showConfirmButton:false });
    editModal.hide();
    loadList();
  } catch (err) {
    Swal.fire('Error', err.message, 'error');
  } finally {
    btn.disabled = false;
    btn.textContent = 'Save';
  }
};

/* ───── init ───── */
loadList();
</script>
@endpush
