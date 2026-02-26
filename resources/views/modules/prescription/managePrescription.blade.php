@extends('users.admin.components.structure')

@section('title','Manage Prescriptions')

@push('styles')
<link rel="stylesheet" href="{{ asset('css/pages/prescription/managePrescription.css') }}">
<style>
  .doc-img { width:60px; height:60px; object-fit:cover; border-radius:50% }
  .accordion-button:not(.collapsed) { background:var(--primary-color); color:#fff }
  .accordion-body { background:#f9fafb }
  .badge-prescribed { background:#16a34a; }
  .badge-pending { background:#f59e0b; }

  /* Prescription Preview */
  .ps-prescription { font-family: Arial, sans-serif; color:#111; text-align:left; }
  .ps-header { border-bottom:2px solid #222; padding-bottom:10px; margin-bottom:20px; }
  .ps-header img { height:60px; }
  .ps-title { font-size:22px; font-weight:bold; margin:5px 0 0; }
  .ps-sub { font-size:13px; color:#666; }

  .ps-section { margin-bottom:18px; }
  .ps-section h6 { font-size:15px; font-weight:bold; margin-bottom:6px; color:#333; }
  .ps-section p { margin:0 0 5px; font-size:14px; }

  .ps-details-row { display:flex; gap:30px; }
  .ps-col { flex:1; }

  .ps-booking-row { display:flex; gap:40px; flex-wrap:wrap; }
  .ps-booking-row p { margin:0; font-size:14px; }

  .ps-medicine-table { width:100%; border-collapse: collapse; margin-top:10px; }
  .ps-medicine-table th, .ps-medicine-table td {
    border:1px solid #ddd; padding:8px; font-size:14px;
  }
  .ps-medicine-table th { background:#f1f1f1; }
  .ps-medicine-table td { vertical-align:middle; }
  .ps-medicine-table td:nth-child(2) { text-align:center; }

  .ps-footer { border-top:1px dashed #888; padding-top:12px; font-size:13px; color:#666; margin-top:20px; }

  /* Print Styles */
  @media print {
    .swal2-container .swal2-actions,
    .swal2-container .swal2-close,
    .swal2-container .ps-no-print {
      display: none !important;
    }
    .swal2-container { position: static !important; }
    .swal2-popup {
      box-shadow: none !important;
      border: none !important;
      width: 100% !important;
      max-width: 100% !important;
      margin: 0 !important;
      padding: 0 !important;
    }
    #ps-printArea { width: 100% !important; }
  }
</style>
@endpush

@section('content')
{{-- ───── Header ───── --}}
<div class="rounded-3 p-4 mb-4">
  <div class="d-flex flex-column flex-md-row justify-content-between align-items-center gap-3">
    <ol class="breadcrumb mb-0">
      <li class="breadcrumb-item"><a href="#"><i class="fas fa-file-prescription me-1"></i>Prescriptions</a></li>
      <li class="breadcrumb-item active">Manage</li>
    </ol>
    <div class="d-none d-sm-flex align-items-center gap-2 px-3 py-2 bg-white rounded-3 text-muted">
      <i class="far fa-calendar-alt"></i><span id="currentDate"></span>
    </div>
  </div><hr>
</div>

{{-- ───── Accordion (Doctors) ───── --}}
<div class="accordion" id="doctorAccordion"></div>

{{-- ───── Assign Prescription Modal ───── --}}
<div class="modal fade" id="assignModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-xl"><div class="modal-content">
    <div class="modal-header">
      <h5 class="modal-title">Assign Prescription</h5>
      <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
    </div>

    <form id="assignForm">
      <input type="hidden" id="bookingId">
      <input type="hidden" id="doctorId">
      <input type="hidden" id="userId">

      <div class="modal-body">
        <div class="mb-3">
          <label class="form-label">Notes</label>
          <textarea id="notes" class="form-control" rows="2"></textarea>
        </div>
        <div class="mb-3">
          <label class="form-label">Follow Up Date</label>
          <input type="date" id="followUpDate" class="form-control">
        </div>

        <h6>Medicines</h6>
        <div id="medicineList"></div>
        <button type="button" class="btn btn-outline-primary btn-sm mt-2" onclick="addMedicineRow()">
          <i class="fas fa-plus"></i> Add Medicine
        </button>
      </div>

      <div class="modal-footer">
        <button class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
        <button id="savePrescriptionBtn" type="button" class="btn btn-primary">Save Prescription</button>
      </div>
    </form>
  </div></div>
</div>
@endsection

@push('scripts')
<script>
const TOKEN   = sessionStorage.getItem('token');
const HEADERS = { Accept:'application/json', Authorization:`Bearer ${TOKEN}` };

document.getElementById('currentDate').textContent =
  new Date().toLocaleDateString('en-US',{ weekday:'short', month:'short', day:'numeric' });

const accordion = document.getElementById('doctorAccordion');

/* ═════════════════ LOAD DOCTORS + PRESCRIPTIONS ═════════════ */
async function loadPrescriptions() {
  accordion.innerHTML = `<div class="text-center p-5"><div class="spinner-border text-primary"></div></div>`;
  const res = await fetch('/api/prescriptions', { headers: HEADERS });
  const j   = await res.json();
  if (!j.data) { accordion.innerHTML = `<p class="text-muted text-center">No data</p>`; return; }

  const grouped = {};
  j.data.forEach(item => {
    const docId = item.booking.doctor_id || item.prescription?.doctor_id;
    if (!grouped[docId]) grouped[docId] = [];
    grouped[docId].push(item);
  });

  accordion.innerHTML = Object.entries(grouped).map(([docId, records], i) => {
    const firstRec   = records[0];
    const pres       = firstRec.prescription;
    const booking    = firstRec.booking;

    const doctorName  = booking.doctor_name ?? pres?.doctor_name ?? "Doctor";
    const dept        = booking.specialty   ?? pres?.specialty   ?? "";
    const img         = `{{ asset('assets/images/placeholder.png') }}`;

    return `
    <div class="accordion-item">
      <h2 class="accordion-header" id="heading${i}">
        <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse"
                data-bs-target="#collapse${i}">
          <img src="${img}" class="doc-img me-3">
          <div>
            <strong>${doctorName}</strong><br>
            <small class="text-muted">${dept}</small>
          </div>
        </button>
      </h2>
      <div id="collapse${i}" class="accordion-collapse collapse" data-bs-parent="#doctorAccordion">
        <div class="accordion-body">
          <table class="table table-hover">
            <thead>
              <tr><th>Patient</th><th>Appointment</th><th>Status</th><th>Actions</th></tr>
            </thead>
            <tbody>
              ${records.map(r => {
                const b    = r.booking;
                const pres = r.prescription;
                const status = pres==='Prescription not assigned'
                  ? `<span class="badge badge-pending">Pending</span>`
                  : `<span class="badge badge-prescribed">Prescribed</span>`;
                return `
                  <tr>
                    <td>${b.patient_name} <br><small class="text-muted">${b.patient_email}</small></td>
                    <td>${b.appointment_date} ${b.slice_start}</td>
                    <td>${status}</td>
                    <td>
                      ${pres==='Prescription not assigned'
                        ? `<button class="btn btn-sm btn-success" onclick="openAssign(${b.booking_id},${b.patient_id},${b.doctor_id})">Assign</button>`
                        : `<button class="btn btn-sm btn-info" onclick="viewPrescription(${pres.id})">View</button>`
                      }
                    </td>
                  </tr>
                `;
              }).join('')}
            </tbody>
          </table>
        </div>
      </div>
    </div>`;
  }).join('');
}

/* ═════════════════ ASSIGN PRESCRIPTION ═════════════ */
const assignModal = new bootstrap.Modal('#assignModal');
function openAssign(bookingId, userId, doctorId) {
  document.getElementById('bookingId').value = bookingId;
  document.getElementById('userId').value = userId;
  document.getElementById('doctorId').value = doctorId;
  document.getElementById('medicineList').innerHTML = '';
  document.getElementById('notes').value = '';
  document.getElementById('followUpDate').value = '';
  addMedicineRow();
  assignModal.show();
}

function addMedicineRow() {
  const container = document.getElementById('medicineList');
  container.insertAdjacentHTML('beforeend', `
    <div class="row g-2 mb-2 medicine-row align-items-center">
      <div class="col-md-3"><input class="form-control" placeholder="Medicine" data-field="medicine_name"></div>
      <div class="col-md-2"><input class="form-control text-center" placeholder="Dosage" data-field="dosage"></div>
      <div class="col-md-3 d-flex gap-2">
        <div class="form-check"><input class="form-check-input" type="checkbox" data-field="morning"><label class="form-check-label small">Morning</label></div>
        <div class="form-check"><input class="form-check-input" type="checkbox" data-field="afternoon"><label class="form-check-label small">Afternoon</label></div>
        <div class="form-check"><input class="form-check-input" type="checkbox" data-field="evening"><label class="form-check-label small">Evening</label></div>
      </div>
      <div class="col-md-2"><input type="date" class="form-control" data-field="duration"></div>
      <div class="col-md-2">
        <select class="form-select" data-field="instructions">
          <option value="Before Meals">Before Meals</option>
          <option value="After Meals">After Meals</option>
        </select>
      </div>
    </div>
  `);
}

document.getElementById('savePrescriptionBtn').onclick = async () => {
  const bookingId = document.getElementById('bookingId').value;
  const doctorId  = document.getElementById('doctorId').value;
  const userId    = document.getElementById('userId').value;
  const notes     = document.getElementById('notes').value;
  const follow    = document.getElementById('followUpDate').value;

  const meds = [...document.querySelectorAll('.medicine-row')].map(row => {
    const m = {};
    row.querySelectorAll('[data-field]').forEach(inp => {
      if (inp.type === 'checkbox') {
        m[inp.dataset.field] = inp.checked ? 1 : 0;
      } else {
        m[inp.dataset.field] = inp.value;
      }
    });
    m.frequency = `${m.morning||0}-${m.afternoon||0}-${m.evening||0}`;
    delete m.morning; delete m.afternoon; delete m.evening;
    return m;
  });

  const payload = {
    booking_id: bookingId,
    doctor_id : doctorId,
    user_id   : userId,
    notes     : notes,
    follow_up_date: follow,
    medicines : JSON.stringify(meds)
  };

  const res = await fetch('/api/prescriptions', {
    method:'POST',
    headers:{ ...HEADERS, 'Content-Type':'application/json' },
    body: JSON.stringify(payload)
  });
  const j = await res.json();
  if (j.status==='success') {
    Swal.fire('Success','Prescription assigned','success');
    assignModal.hide();
    loadPrescriptions();
  } else {
    Swal.fire('Error', j.message||'Failed','error');
  }
};

/* ═════════════════ VIEW PRESCRIPTION ═════════════ */
function frequencyToText(freq) {
  if (!freq) return "-";
  const [m,a,e] = freq.split('-').map(Number);
  const times = [];
  if (m) times.push("Morning");
  if (a) times.push("Afternoon");
  if (e) times.push("Evening");
  return times.length ? times.join(", ") : "-";
}

async function viewPrescription(id) {
  const res = await fetch(`/api/prescriptions/${id}`, { headers:HEADERS });
  const j   = await res.json();
  if (!j.data) return Swal.fire('Error','Not found','error');

  const p = j.data;
  const meds = JSON.parse(p.medicines || '[]');

  Swal.fire({
    width: 850,
    showConfirmButton: false,
    html: `
      <div id="ps-printArea" class="ps-prescription">
        <div class="ps-header d-flex justify-content-between align-items-center">
          <div><img src="{{ asset('assets/images/web_assets/logo.jpg') }}"></div>
          <div class="text-end">
            <div class="ps-title">Prescription</div>
            <div class="ps-sub">Generated: ${new Date().toLocaleDateString()}</div>
          </div>
        </div>

        <div class="ps-details-row">
          <div class="ps-col ps-section">
            <h6>Doctor Details</h6>
            <p><strong>${p.doctor_name}</strong></p>
            <p>${p.specialty || ''}, ${p.degree || ''}</p>
            <p>${p.doctor_email} | ${p.doctor_phone || ''}</p>
          </div>
          <div class="ps-col ps-section">
            <h6>Patient Details</h6>
            <p><strong>${p.patient_name}</strong></p>
            <p>${p.patient_email}</p>
            <p>${p.patient_phone || ''}</p>
            <p>${p.patient_address || ''}</p>
          </div>
        </div>
        <hr>

        <div class="ps-section">
          <h6>Booking Details</h6>
          <div class="ps-booking-row">
            <p><strong>Date:</strong> ${p.appointment_date} ${p.slice_start}</p>
            <p><strong>Token:</strong> ${p.booking_token || '-'}</p>
            <p><strong>Slot:</strong> ${p.slice_start || '-'}</p>
          </div>
        </div>
        <hr>

        <div class="ps-section">
          <h6>Prescription Notes</h6>
          <p>${p.notes || '-'}</p>
          <p><strong>Follow-up Date:</strong> ${p.follow_up_date || '-'}</p>
        </div>
        <hr>

        <div class="ps-section">
          <h6>Medicines</h6>
          <table class="ps-medicine-table">
            <thead><tr><th>Medicine</th><th>Frequency & Duration</th><th>Instruction</th></tr></thead>
            <tbody>
              ${meds.map(m=>`
                <tr>
                  <td><strong>${m.medicine_name||''}</strong> ${m.dosage ? '('+m.dosage+')' : ''}</td>
                  <td>${frequencyToText(m.frequency)}<br><small>${m.duration||''}</small></td>
                  <td>${m.instructions||''}</td>
                </tr>`).join('')}
            </tbody>
          </table>
        </div>

        <div class="ps-footer">
          <p>Signature: ________________________</p>
        </div>

        <div class="text-end mt-3 ps-no-print">
          <button class="btn btn-outline-primary" onclick="window.print()">
            <i class="fas fa-print me-1"></i> Print / Save PDF
          </button>
        </div>
      </div>
    `
  });
}

/* INIT */
loadPrescriptions();
</script>
@endpush
