{{-- ───────────────────────────────────────────────────────────────
     Create Appointment Slot ‧ Admin   (weekly – day + HH:MM)
     URL  : /admin/appointment/create?doctor_id=#
     Uses : AppointmentController (API) + SweetAlert2
──────────────────────────────────────────────────────────────── --}}
@extends('users.admin.components.structure')

@section('title','Create Appointment Slot')

@push('styles')
<link rel="stylesheet"
      href="{{ asset('css/pages/appointment/createAppointment.css') }}">
<style>
  .doc-avatar{width:70px;height:70px;object-fit:cover;border-radius:50%}
  .modal-header{background:var(--primary-color);color:#fff}
</style>
@endpush

@php
    /* Professional time list: 30-minute steps, 06:00–22:00 */
    $times = [];
    for ($h = 6; $h <= 22; $h++) {
        $times[] = sprintf('%02d:00', $h);
        $times[] = sprintf('%02d:30', $h);
    }
@endphp

@section('content')
{{-- ───── Header & breadcrumb ───── --}}
<div class="rounded-3 p-4 mb-4">
  <div class="d-flex flex-column flex-md-row justify-content-between align-items-center gap-3">
    <ol class="breadcrumb mb-0">
      <li class="breadcrumb-item"><a href="/admin/doctor/manage">
        <i class="fas fa-user-md me-1"></i>Doctor</a></li>
      <li class="breadcrumb-item"><a id="docCrumb" href="#">Profile</a></li>
      <li class="breadcrumb-item active">Create Slot</li>
    </ol>
    <div class="d-none d-sm-flex align-items-center gap-2 px-3 py-2 bg-white rounded-3 text-muted">
      <i class="far fa-calendar-alt"></i>
      <span id="today"></span>
    </div>
  </div><hr>
</div>

{{-- ───── Doctor card ───── --}}
<div id="docCard" class="glass-effect card p-4 mb-4 shadow-sm d-none">
  <div class="d-flex align-items-center gap-4">
    <img id="docImg" class="doc-avatar shadow-sm" src="">
    <div>
      <h5 id="docName" class="mb-1 fw-semibold">Doctor</h5>
      <div id="docDept"  class="text-muted small"></div>
      <div class="text-muted small">Charges ₹<span id="docCharge">0</span></div>
    </div>
  </div>
</div>

{{-- ───── New-slot form ───── --}}
<div class="glass-effect rounded-3 p-4 mb-4">
  <h5 class="fw-semibold mb-3"><i class="fas fa-plus me-2"></i>New Slot</h5>
  <form id="slotForm" class="row g-3 needs-validation" novalidate>
    <div class="col-md-3">
      <label class="form-label">Day *</label>
      <select id="day" class="form-select" required>
        <option value="" disabled selected>Select day</option>
        <option value="0">Sunday</option><option value="1">Monday</option>
        <option value="2">Tuesday</option><option value="3">Wednesday</option>
        <option value="4">Thursday</option><option value="5">Friday</option>
        <option value="6">Saturday</option>
      </select>
    </div>
    <div class="col-md-3">
      <label class="form-label">Start *</label>
      <select id="start" class="form-select" required>
        <option value="" disabled selected>HH:MM</option>
        @foreach($times as $t)
          <option value="{{ $t }}">{{ $t }}</option>
        @endforeach
      </select>
    </div>
    <div class="col-md-3">
      <label class="form-label">End *</label>
      <select id="end" class="form-select" required>
        <option value="" disabled selected>HH:MM</option>
        @foreach($times as $t)
          <option value="{{ $t }}">{{ $t }}</option>
        @endforeach
      </select>
    </div>
    <div class="col-md-3">
      <label class="form-label">Type</label>
      <input id="type" class="form-control" placeholder="Follow-up">
    </div>
    <div class="col-md-3">
      <label class="form-label">Location</label>
      <input id="loc" class="form-control" placeholder="Clinic A">
    </div>
    <div class="col-12">
      <button id="addBtn" type="button" class="btn btn-primary">
        <i class="fas fa-save me-2"></i>Add Slot
      </button>
    </div>
  </form>
</div>

{{-- ───── Slots table ───── --}}
<div class="glass-effect rounded-3 shadow-sm overflow-hidden">
  <div class="table-responsive">
    <table class="table table-hover mb-0">
      <thead class="table-light">
        <tr>
          <th>Day</th><th>Start</th><th>End</th>
          <th>Type</th><th>Location</th>
          <th class="text-center">Actions</th>
        </tr>
      </thead>
      <tbody id="slotBody"></tbody>
      <tbody id="spinRow">
        <tr><td colspan="6" class="py-4 text-center">
          <div class="spinner-border text-primary"></div>
        </td></tr>
      </tbody>
    </table>
  </div>
</div>

{{-- ───── Edit-slot modal ───── --}}
<div class="modal fade" id="editModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog"><div class="modal-content">
    <div class="modal-header"><h5 class="modal-title">Edit Slot</h5>
      <button type="button" class="btn-close btn-close-white"
              data-bs-dismiss="modal"></button>
    </div>
    <form id="editForm" novalidate>
      <div class="modal-body row g-3">
        <input id="eId" type="hidden">
        <div class="col-md-4">
          <label class="form-label">Day *</label>
          <select id="eDay" class="form-select" required>
            <option value="0">Sunday</option><option value="1">Monday</option>
            <option value="2">Tuesday</option><option value="3">Wednesday</option>
            <option value="4">Thursday</option><option value="5">Friday</option>
            <option value="6">Saturday</option>
          </select>
        </div>
        <div class="col-md-4">
          <label class="form-label">Start *</label>
          <select id="eStart" class="form-select" required>
            @foreach($times as $t)
              <option value="{{ $t }}">{{ $t }}</option>
            @endforeach
          </select>
        </div>
        <div class="col-md-4">
          <label class="form-label">End *</label>
          <select id="eEnd" class="form-select" required>
            @foreach($times as $t)
              <option value="{{ $t }}">{{ $t }}</option>
            @endforeach
          </select>
        </div>
        <div class="col-12"><label class="form-label">Type</label><input id="eType" class="form-control"></div>
        <div class="col-12"><label class="form-label">Location</label><input id="eLoc" class="form-control"></div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary"
                data-bs-dismiss="modal">Cancel</button>
        <button id="saveBtn" type="button"
                class="btn btn-primary">Save</button>
      </div>
    </form>
  </div></div>
</div>
@endsection



@push('scripts')
<script>
/* ═══ constants ══ */
const token = sessionStorage.getItem('token'),
      HEAD  = {Accept:'application/json',Authorization:`Bearer ${token}`},
      CSRF  = document.querySelector('meta[name="csrf-token"]')?.content,
      docId = new URLSearchParams(location.search).get('doctor_id');
if(!docId){ Swal.fire('Error','doctor_id missing','error'); throw 'id'; }

/* helpers */
const days = ['Sun','Mon','Tue','Wed','Thu','Fri','Sat'];
const fmt   = t => t.slice(0,5);             // '08:30:00' → '08:30'

/* prevent unintended submits */
['slotForm','editForm'].forEach(id =>
  document.getElementById(id).addEventListener('submit', e=>e.preventDefault()));

/* ═══ doctor info ══ */
async function loadDoctor(){
  const r=await fetch(`/api/doctors/${docId}`,{headers:HEAD}),
        j=await r.json();
  if(!r.ok||!j.success){ Swal.fire('Error','Cannot load doctor','error');return;}
  const d=j.doctor;
  document.getElementById('docCard').classList.remove('d-none');
  document.getElementById('docImg').src='{{ asset('') }}'+(d.image_url??'assets/images/placeholder.png');
  document.getElementById('docName').textContent=`${d.first_name} ${d.last_name}`;
  document.getElementById('docDept').textContent=d.specialty??'';
  document.getElementById('docCharge').textContent=d.visiting_charge??0;
  document.getElementById('docCrumb').href=`/admin/doctor/manage?doctor_id=${docId}`;
}

/* ═══ slots ══ */
let slots=[];
async function loadSlots(){
  const r=await fetch(`/api/appointments/doctor/${docId}`,{headers:HEAD}),
        j=await r.json();
  if(!r.ok||!j.success) return;
  slots=j.appointments; renderSlots();
}
function renderSlots(){
  const body=document.getElementById('slotBody');
  body.innerHTML=slots.map(s=>`
    <tr>
      <td>${days[s.day_of_week]}</td>
      <td>${fmt(s.start_time)}</td>
      <td>${fmt(s.end_time)}</td>
      <td>${s.appointment_type??'-'}</td>
      <td>${s.location??'-'}</td>
      <td class="text-center">
        <button class="btn btn-sm btn-outline-primary me-1"
          onclick="openEdit(${s.id})"><i class="fas fa-edit"></i></button>
        <button class="btn btn-sm btn-outline-danger"
          onclick="delSlot(${s.id})"><i class="fas fa-trash-alt"></i></button>
      </td>
    </tr>`).join('');
  document.getElementById('spinRow').classList.add('d-none');
}

/* ═══ add slot ══ */
document.getElementById('addBtn').onclick=async()=>{
  const fd=new FormData();
  fd.append('doctor_id',   docId);
  fd.append('day_of_week', document.getElementById('day').value);
  fd.append('start_time',  document.getElementById('start').value);
  fd.append('end_time',    document.getElementById('end').value);
  fd.append('appointment_type',document.getElementById('type').value);
  fd.append('location',         document.getElementById('loc').value);

  const btn=document.getElementById('addBtn');
  btn.disabled=true;btn.innerHTML='<span class="spinner-border spinner-border-sm me-2"></span>Save';
  try{
    const r=await fetch('/api/appointments',{
          method:'POST',
          headers:CSRF?{...HEAD,'X-CSRF-TOKEN':CSRF}:HEAD,
          body:fd}),
          j=await r.json();
    if(!r.ok||!j.success) throw Error(j.message||'Insert failed');
    await Swal.fire({icon:'success',title:'Slot added',timer:1200,showConfirmButton:false});
    document.getElementById('slotForm').reset(); loadSlots();
  }catch(e){Swal.fire('Error',e.message,'error');}
  finally{btn.disabled=false;btn.innerHTML='<i class="fas fa-save me-2"></i>Add Slot';}
};

/* ═══ delete slot ══ */
async function delSlot(id){
  const ok=await Swal.fire({title:'Delete?',icon:'warning',
        showCancelButton:true,confirmButtonColor:'#ef4444'});
  if(!ok.isConfirmed) return;
  try{
    const r=await fetch(`/api/appointments/${id}`,{
          method:'DELETE',
          headers:CSRF?{...HEAD,'X-CSRF-TOKEN':CSRF}:HEAD}),
          j=await r.json();
    if(!r.ok||!j.success) throw Error(j.message||'Delete failed');
    slots=slots.filter(s=>s.id!==id); renderSlots();
  }catch(e){Swal.fire('Error',e.message,'error');}
}

/* ═══ edit slot ══ */
const editModal=new bootstrap.Modal('#editModal');
function openEdit(id){
  const s=slots.find(x=>x.id===id); if(!s) return;
  document.getElementById('eId').value   =id;
  document.getElementById('eDay').value  =s.day_of_week;
  document.getElementById('eStart').value=s.start_time.slice(0,5);
  document.getElementById('eEnd').value  =s.end_time.slice(0,5);
  document.getElementById('eType').value =s.appointment_type??'';
  document.getElementById('eLoc').value  =s.location??'';
  editModal.show();
}
document.getElementById('saveBtn').onclick=async()=>{
  const id=document.getElementById('eId').value,
        fd=new FormData();
  fd.append('_method','PUT');
  fd.append('day_of_week',document.getElementById('eDay').value);
  fd.append('start_time', document.getElementById('eStart').value);
  fd.append('end_time',   document.getElementById('eEnd').value);
  fd.append('appointment_type',document.getElementById('eType').value);
  fd.append('location',         document.getElementById('eLoc').value);

  const btn=document.getElementById('saveBtn');
  btn.disabled=true;btn.innerHTML='<span class="spinner-border spinner-border-sm me-2"></span>Save';
  try{
    const r=await fetch(`/api/appointments/${id}`,{
          method:'POST',                   // FormData needs POST+_method=PUT
          headers:CSRF?{...HEAD,'X-CSRF-TOKEN':CSRF}:HEAD,
          body:fd}),
          j=await r.json();
    if(!r.ok||!j.success) throw Error(j.message||'Update failed');
    await Swal.fire({icon:'success',title:'Updated',timer:1200,showConfirmButton:false});
    editModal.hide(); loadSlots();
  }catch(e){Swal.fire('Error',e.message,'error');}
  finally{btn.disabled=false;btn.textContent='Save';}
};

/* ═══ init ══ */
document.getElementById('today').textContent=
  new Date().toLocaleDateString('en-US',{weekday:'short',month:'short',day:'numeric'});
loadDoctor(); loadSlots();
</script>
@endpush
