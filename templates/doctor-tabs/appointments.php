<?php
/**
 * Dior Medical — Doctor Appointment Calendar
 * Patient portal calendar layout and interactions, powered by doctor-scoped appointment records.
 */
if (!defined('ABSPATH')) { exit; }

$doctor_calendar_appointments = isset($appointments) && is_array($appointments) ? $appointments : [];
$doctor_calendar_events = [];
$doctor_calendar_total = 0;
$doctor_calendar_upcoming = 0;
$doctor_calendar_completed = 0;
$doctor_calendar_video = 0;
$doctor_calendar_today = current_time('Y-m-d');

foreach ($doctor_calendar_appointments as $index => $appointment) {
    $appointment_date = sanitize_text_field($appointment['date'] ?? ($appointment['appt_date'] ?? ''));
    if (!$appointment_date) continue;
    $date_timestamp = strtotime($appointment_date);
    if (!$date_timestamp) continue;
    $appointment_date = date('Y-m-d', $date_timestamp);

    $patient_id = absint($appointment['patient_id'] ?? 0);
    $patient_user = $patient_id ? get_userdata($patient_id) : false;
    $patient_name = sanitize_text_field($appointment['patient_name'] ?? '');
    if (!$patient_name && $patient_user) {
        $patient_name = trim($patient_user->first_name . ' ' . $patient_user->last_name);
        if (!$patient_name) $patient_name = $patient_user->display_name;
    }
    if (!$patient_name) $patient_name = 'Patient';

    $doctor_name = sanitize_text_field($appointment['provider'] ?? ($appointment['doctor_name'] ?? ''));
    if (!$doctor_name) $doctor_name = wp_get_current_user()->display_name;
    if (stripos($doctor_name, 'Dr.') === false && stripos($doctor_name, 'Dr ') === false) $doctor_name = 'Dr. ' . $doctor_name;

    $status = sanitize_text_field($appointment['status'] ?? 'Scheduled');
    $type = sanitize_text_field($appointment['type'] ?? ($appointment['visit_type'] ?? 'Video Visit (HD)'));
    $time = sanitize_text_field($appointment['time'] ?? ($appointment['appt_time'] ?? ''));
    $condition = sanitize_text_field($appointment['condition'] ?? ($appointment['condition_name'] ?? 'Consultation'));
    $appt_uid = sanitize_text_field($appointment['appt_uid'] ?? ($appointment['id'] ?? ('APT-' . ($index + 1))));
    $join_url = esc_url_raw($appointment['join_url'] ?? '');

    $status_lc = strtolower($status);
    $type_lc = strtolower($type);
    $doctor_calendar_total++;
    if (in_array($status_lc, ['confirmed','scheduled','pending','active','in-queue'], true)) $doctor_calendar_upcoming++;
    if (in_array($status_lc, ['completed','done','past'], true)) $doctor_calendar_completed++;
    if (strpos($type_lc, 'video') !== false || strpos($type_lc, 'telehealth') !== false) $doctor_calendar_video++;

    $doctor_calendar_events[] = [
        'id' => $appt_uid,
        'patient_name' => $patient_name,
        'doctor_name' => $doctor_name,
        'date' => $appointment_date,
        'time' => $time ?: 'Time not set',
        'status' => $status,
        'type' => $type,
        'condition' => $condition,
        'join_url' => $join_url,
        'patient_id' => $patient_id,
        'doctor_id' => absint($appointment['doctor_id'] ?? get_current_user_id()),
    ];
}
$doctor_calendar_json = wp_json_encode($doctor_calendar_events, JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP);
?>

<section class="dior-tab-panel" id="tab-doc-appointments" style="display:none;">
    <div class="mb-4 d-flex justify-content-between align-items-center flex-wrap gap-3">
        <div>
            <h4 class="mb-1 text-dark fw-bold fs-4 d-flex align-items-center gap-2">
                <span class="badge bg-primary-subtle text-primary border border-primary-subtle rounded-circle p-2 d-inline-flex align-items-center justify-content-center" style="width:36px;height:36px;">
                    <i class="fas fa-calendar-days fs-6"></i>
                </span>
                Appointment Calendar
            </h4>
            <p class="text-secondary small mb-0">View and manage your patient consultations and daily schedule.</p>
        </div>
        <button type="button" class="btn btn-primary rounded-3 px-3 py-2 fw-semibold shadow-sm d-inline-flex align-items-center gap-2"
            onclick="if(typeof diorOpenDocBookerModal==='function'){diorOpenDocBookerModal();}else if(typeof diorDocSwitchTab==='function'){diorDocSwitchTab('doc-appointments-book');}">
            <i class="fas fa-calendar-plus"></i> Book Appointment
        </button>
    </div>

    <div class="dior-subtab-nav-bar px-1 mb-4">
        <button type="button" class="dior-subtab-pill btn active" data-tab="doc-appointments" onclick="if(typeof diorDocSwitchTab==='function')diorDocSwitchTab('doc-appointments');"><i class="fas fa-calendar-days"></i> Appointment Calendar</button>
        <button type="button" class="dior-subtab-pill btn" data-tab="doc-appointments-view" onclick="if(typeof diorDocSwitchTab==='function')diorDocSwitchTab('doc-appointments-view');"><i class="fas fa-calendar-check"></i> View Appointment</button>
        <button type="button" class="dior-subtab-pill btn" data-tab="doc-appointments-book" onclick="if(typeof diorDocSwitchTab==='function')diorDocSwitchTab('doc-appointments-book');"><i class="fas fa-calendar-plus"></i> Book Appointment</button>
        <button type="button" class="dior-subtab-pill btn" data-tab="doc-appointments-edit" onclick="if(typeof diorDocSwitchTab==='function')diorDocSwitchTab('doc-appointments-edit');"><i class="fas fa-pen-to-square"></i> Edit Appointment</button>
    </div>

    <div class="row g-3 mb-4" id="dior-doc-cal-stats">
        <div class="col-xl-3 col-sm-6 col-12"><div class="card border-0 shadow-sm rounded-4 h-100 bg-white p-3"><div class="d-flex align-items-center justify-content-between"><div><span class="text-muted small fw-semibold text-uppercase">Total Consultations</span><h3 class="fw-bold mb-0 text-dark mt-1"><?php echo esc_html($doctor_calendar_total); ?></h3><span class="badge bg-primary-subtle text-primary rounded-pill mt-2 px-2 py-1 small"><i class="fas fa-calendar-check me-1"></i> Patient Records</span></div><div class="rounded-4 d-flex align-items-center justify-content-center" style="width:52px;height:52px;background:rgba(79,70,229,.1);color:#4f46e5;font-size:22px;"><i class="fas fa-calendar-check"></i></div></div></div></div>
        <div class="col-xl-3 col-sm-6 col-12"><div class="card border-0 shadow-sm rounded-4 h-100 bg-white p-3"><div class="d-flex align-items-center justify-content-between"><div><span class="text-muted small fw-semibold text-uppercase">Upcoming Visits</span><h3 class="fw-bold mb-0 text-dark mt-1"><?php echo esc_html($doctor_calendar_upcoming); ?></h3><span class="badge bg-info-subtle text-info rounded-pill mt-2 px-2 py-1 small"><i class="fas fa-clock me-1"></i> Scheduled</span></div><div class="rounded-4 d-flex align-items-center justify-content-center" style="width:52px;height:52px;background:rgba(14,165,233,.1);color:#0ea5e9;font-size:22px;"><i class="fas fa-clock"></i></div></div></div></div>
        <div class="col-xl-3 col-sm-6 col-12"><div class="card border-0 shadow-sm rounded-4 h-100 bg-white p-3"><div class="d-flex align-items-center justify-content-between"><div><span class="text-muted small fw-semibold text-uppercase">Telehealth Video</span><h3 class="fw-bold mb-0 text-dark mt-1"><?php echo esc_html($doctor_calendar_video); ?></h3><span class="badge bg-success-subtle text-success rounded-pill mt-2 px-2 py-1 small"><i class="fas fa-video me-1"></i> HD Live Room</span></div><div class="rounded-4 d-flex align-items-center justify-content-center" style="width:52px;height:52px;background:rgba(16,185,129,.1);color:#10b981;font-size:22px;"><i class="fas fa-video"></i></div></div></div></div>
        <div class="col-xl-3 col-sm-6 col-12"><div class="card border-0 shadow-sm rounded-4 h-100 bg-white p-3"><div class="d-flex align-items-center justify-content-between"><div><span class="text-muted small fw-semibold text-uppercase">Completed Visits</span><h3 class="fw-bold mb-0 text-dark mt-1"><?php echo esc_html($doctor_calendar_completed); ?></h3><span class="badge bg-secondary-subtle text-secondary rounded-pill mt-2 px-2 py-1 small"><i class="fas fa-circle-check me-1"></i> Finalized</span></div><div class="rounded-4 d-flex align-items-center justify-content-center" style="width:52px;height:52px;background:rgba(100,116,139,.1);color:#64748b;font-size:22px;"><i class="fas fa-circle-check"></i></div></div></div></div>
    </div>

    <div class="card border-0 shadow-sm rounded-4 mb-4 bg-white overflow-hidden" id="dior-doc-calendar-card">
        <div class="card-header bg-white border-bottom py-3 px-3 px-lg-4">
            <div class="d-flex flex-wrap justify-content-between align-items-center gap-3">
                <div class="d-flex align-items-center gap-2">
                    <button type="button" class="btn btn-outline-secondary btn-sm rounded-circle d-flex align-items-center justify-content-center" id="dior-doc-bs-cal-prev" style="width:36px;height:36px;" title="Previous Month"><i class="fas fa-chevron-left"></i></button>
                    <h3 class="mb-0 fw-bold text-dark fs-5 px-2 text-center" id="dior-doc-bs-cal-title" style="min-width:150px;"></h3>
                    <button type="button" class="btn btn-outline-secondary btn-sm rounded-circle d-flex align-items-center justify-content-center" id="dior-doc-bs-cal-next" style="width:36px;height:36px;" title="Next Month"><i class="fas fa-chevron-right"></i></button>
                    <button type="button" class="btn btn-outline-primary btn-sm rounded-pill px-3 fw-semibold ms-1" id="dior-doc-bs-cal-today">Today</button>
                </div>
                <div class="d-flex flex-wrap align-items-center gap-2">
                    <div class="input-group input-group-sm" style="width:auto;"><span class="input-group-text bg-light border-end-0 text-muted"><i class="fas fa-filter"></i></span><select id="dior-doc-bs-cal-type-filter" class="form-select form-select-sm border-start-0 ps-1" style="min-width:125px;"><option value="all">All Visits</option><option value="video">Video (HD)</option><option value="phone">Phone Visit</option><option value="clinic">In-Clinic</option></select></div>
                    <div class="input-group input-group-sm" style="width:auto;"><span class="input-group-text bg-light border-end-0 text-muted"><i class="fas fa-tag"></i></span><select id="dior-doc-bs-cal-status-filter" class="form-select form-select-sm border-start-0 ps-1" style="min-width:125px;"><option value="all">All Status</option><option value="confirmed">Confirmed</option><option value="completed">Completed</option><option value="cancelled">Cancelled</option></select></div>
                    <div class="btn-group btn-group-sm gap-2" role="group"><button type="button" class="btn btn-primary active fw-semibold" id="dior-doc-bs-cal-view-month"><i class="fas fa-calendar-days me-1"></i> Month</button><button type="button" class="btn btn-outline-secondary fw-semibold" id="dior-doc-bs-cal-view-agenda"><i class="fas fa-list-ul me-1"></i> Agenda</button></div>
                    <button type="button" class="btn btn-primary btn-sm rounded-pill px-3 fw-bold d-inline-flex align-items-center gap-1 shadow-sm" onclick="if(typeof diorOpenDocBookerModal==='function'){diorOpenDocBookerModal();}else if(typeof diorDocSwitchTab==='function'){diorDocSwitchTab('doc-appointments-book');}"><i class="fas fa-calendar-plus"></i> Book Visit</button>
                </div>
            </div>
        </div>
        <div class="card-body p-3 p-lg-4">
            <div class="row g-4">
                <div class="col-lg-8 col-12">
                    <div id="dior-doc-bs-cal-month-wrap" class="dior-bs-calendar-container"><div class="table-responsive rounded-3 border"><table class="table table-bordered mb-0 dior-bs-cal-table"><thead><tr class="text-center text-uppercase text-secondary bg-light" style="font-size:11px;letter-spacing:.8px;"><th style="width:14.28%;padding:10px 4px;">Sun</th><th style="width:14.28%;padding:10px 4px;">Mon</th><th style="width:14.28%;padding:10px 4px;">Tue</th><th style="width:14.28%;padding:10px 4px;">Wed</th><th style="width:14.28%;padding:10px 4px;">Thu</th><th style="width:14.28%;padding:10px 4px;">Fri</th><th style="width:14.28%;padding:10px 4px;">Sat</th></tr></thead><tbody id="dior-doc-bs-cal-grid-body"></tbody></table></div></div>
                    <div id="dior-doc-bs-cal-agenda-wrap" style="display:none;"><div id="dior-doc-bs-cal-agenda-list" class="d-flex flex-column gap-3"></div></div>
                </div>
                <div class="col-lg-4 col-12">
                    <div class="card border border-light-subtle rounded-4 shadow-none bg-light h-100 d-flex flex-column">
                        <div class="card-header bg-white border-bottom py-3 px-3 rounded-top-4 d-flex align-items-center justify-content-between"><div><span class="text-muted small fw-bold text-uppercase" style="font-size:11px;">Selected Date</span><h5 class="mb-0 fw-bold text-dark fs-6 mt-1" id="dior-doc-bs-cal-selected-header"></h5></div><span class="badge bg-primary-subtle text-primary rounded-pill px-2 py-1 fw-bold" id="dior-doc-bs-cal-selected-count">0 Visits</span></div>
                        <div class="card-body p-3 flex-grow-1 overflow-auto" id="dior-doc-bs-cal-selected-body" style="max-height:480px;"></div>
                        <div class="card-footer bg-white border-top py-3 px-3 rounded-bottom-4"><div class="d-flex flex-wrap align-items-center justify-content-between gap-2 small text-muted" style="font-size:11px;"><div class="d-flex align-items-center gap-1"><span class="dior-doc-cal-dot" style="background:#4f46e5;"></span><span>Video</span></div><div class="d-flex align-items-center gap-1"><span class="dior-doc-cal-dot" style="background:#0ea5e9;"></span><span>Phone</span></div><div class="d-flex align-items-center gap-1"><span class="dior-doc-cal-dot" style="background:#10b981;"></span><span>Confirmed</span></div><div class="d-flex align-items-center gap-1"><span class="dior-doc-cal-dot" style="background:#ef4444;"></span><span>Cancelled</span></div></div></div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<style>
#dior-doc-calendar-card .dior-bs-cal-table{table-layout:fixed;min-width:0;}
#dior-doc-calendar-card .dior-bs-cal-table th{font-weight:700;}
#dior-doc-calendar-card .dior-doc-cal-dot{width:8px;height:8px;border-radius:50%;display:inline-block;flex:0 0 8px;}
#dior-doc-calendar-card .dior-doc-bs-cal-cell{height:104px;vertical-align:top;padding:7px 6px;cursor:pointer;background:#fff;transition:background .15s,border-color .15s;}
#dior-doc-calendar-card .dior-doc-bs-cal-cell:hover{background:#f8fafc;}
#dior-doc-calendar-card .dior-doc-bs-cal-cell.is-other{background:#f8fafc;color:#94a3b8;}
#dior-doc-calendar-card .dior-doc-bs-cal-cell.is-today .dior-doc-bs-cal-day-num{background:#e0e7ff;color:#3730a3;}
#dior-doc-calendar-card .dior-doc-bs-cal-cell.is-selected{box-shadow:inset 0 0 0 2px #4f46e5;background:#f5f3ff;}
#dior-doc-calendar-card .dior-doc-bs-cal-day-head{display:flex;align-items:center;justify-content:space-between;margin-bottom:5px;font-size:12px;font-weight:700;}
#dior-doc-calendar-card .dior-doc-bs-cal-day-num{display:inline-flex;align-items:center;justify-content:center;width:24px;height:24px;border-radius:50%;}
#dior-doc-calendar-card .dior-doc-bs-cal-event{display:block;overflow:hidden;text-overflow:ellipsis;white-space:nowrap;border-radius:4px;padding:3px 5px;margin:3px 0;font-size:10px;font-weight:600;color:#fff;background:#4f46e5;}
#dior-doc-calendar-card .dior-doc-bs-cal-event.completed{background:#10b981;}
#dior-doc-calendar-card .dior-doc-bs-cal-event.cancelled{background:#ef4444;}
#dior-doc-calendar-card .dior-doc-bs-cal-event.phone{background:#0ea5e9;}
#dior-doc-calendar-card .dior-doc-cal-appointment{border:1px solid #e2e8f0;border-radius:12px;padding:12px;background:#fff;}
@media(max-width:767px){#dior-doc-calendar-card .card-header{padding:12px!important;}#dior-doc-calendar-card .dior-doc-bs-cal-cell{height:78px;padding:4px 2px;}#dior-doc-calendar-card .dior-doc-bs-cal-event{font-size:9px;padding:2px;}#dior-doc-calendar-card .dior-bs-cal-table th{font-size:10px;padding:7px 1px!important;}#dior-doc-calendar-card .card-body{padding:12px!important;}#dior-doc-cal-stats .card{min-height:112px;}#dior-doc-calendar-card .d-flex.flex-wrap.justify-content-between{align-items:flex-start!important;}}

/* Mobile layout: use the full available dashboard width and prevent calendar content from wrapping vertically. */
@media (max-width: 767.98px) {
    #tab-doc-appointments {
        box-sizing: border-box !important;
        width: calc(100% + 56px) !important;
        max-width: none !important;
        margin: -24px -28px 0 !important;
        padding: 12px 10px 24px !important;
        overflow-x: hidden;
    }
    #tab-doc-appointments > .mb-4:first-child {
        margin: 0 0 14px !important;
        padding: 0 2px;
        align-items: flex-start !important;
    }
    #tab-doc-appointments > .mb-4:first-child h4 { font-size: 18px !important; }
    #tab-doc-appointments > .mb-4:first-child p { font-size: 12px !important; }
    #tab-doc-appointments > .mb-4:first-child > button { width: 100%; justify-content: center; }
    #tab-doc-appointments .dior-subtab-nav-bar {
        display: flex;
        flex-wrap: nowrap;
        overflow-x: auto;
        gap: 6px;
        margin: 0 -10px 14px !important;
        padding: 0 10px 6px !important;
        -webkit-overflow-scrolling: touch;
    }
    #tab-doc-appointments .dior-subtab-pill {
        flex: 0 0 auto;
        white-space: nowrap;
        font-size: 12px;
        padding: 8px 10px;
    }
    #dior-doc-cal-stats {
        margin: 0 0 12px !important;
        --bs-gutter-x: 8px;
        --bs-gutter-y: 8px;
    }
    #dior-doc-cal-stats > div { width: 50%; }
    #dior-doc-cal-stats .card { min-height: 100px !important; padding: 12px !important; }
    #dior-doc-cal-stats .card span { font-size: 9px !important; }
    #dior-doc-cal-stats .card h3 { font-size: 22px !important; }
    #dior-doc-cal-stats .card > .d-flex > div:last-child { width: 38px !important; height: 38px !important; font-size: 16px !important; }
    #dior-doc-calendar-card { margin-bottom: 12px !important; border-radius: 12px !important; }
    #dior-doc-calendar-card > .card-header { padding: 12px 10px !important; }
    #dior-doc-calendar-card > .card-header > .d-flex {
        display: flex;
        flex-direction: column;
        align-items: stretch !important;
        gap: 12px !important;
    }
    #dior-doc-calendar-card > .card-header > .d-flex > .d-flex:first-child {
        justify-content: space-between;
        width: 100%;
        gap: 5px !important;
    }
    #dior-doc-bs-cal-title { min-width: 0 !important; flex: 1; font-size: 17px !important; }
    #dior-doc-bs-cal-today { margin-left: 0 !important; padding-left: 10px !important; padding-right: 10px !important; }
    #dior-doc-calendar-card > .card-header > .d-flex > .d-flex:last-child {
        display: grid !important;
        grid-template-columns: minmax(0,1fr) minmax(0,1fr);
        gap: 8px !important;
        width: 100%;
    }
    #dior-doc-calendar-card .input-group { width: 100% !important; min-width: 0; }
    #dior-doc-calendar-card .input-group .form-select { min-width: 0 !important; width: 1% !important; }
    #dior-doc-calendar-card .btn-group { width: 100%; }
    #dior-doc-calendar-card .btn-group .btn { flex: 1 1 50%; padding: 8px 5px; }
    #dior-doc-calendar-card > .card-header > .d-flex > .d-flex:last-child > button { grid-column: 1 / -1; width: 100%; justify-content: center; }
    #dior-doc-calendar-card > .card-body { padding: 10px !important; }
    #dior-doc-calendar-card > .card-body > .row { --bs-gutter-x: 0; --bs-gutter-y: 14px; }
    #dior-doc-calendar-card .col-lg-8,
    #dior-doc-calendar-card .col-lg-4 { padding-left: 0; padding-right: 0; }
    #dior-doc-calendar-card .table-responsive { width: 100%; overflow: hidden; border-radius: 8px !important; }
    #dior-doc-calendar-card .dior-bs-cal-table { width: 100% !important; min-width: 0 !important; table-layout: fixed; }
    #dior-doc-calendar-card .dior-bs-cal-table th { width: 14.285% !important; padding: 9px 0 !important; font-size: 9px !important; letter-spacing: 0 !important; }
    #dior-doc-calendar-card .dior-doc-bs-cal-cell { height: 68px !important; padding: 4px 2px !important; overflow: hidden; }
    #dior-doc-calendar-card .dior-doc-bs-cal-day-head { justify-content: center; margin-bottom: 3px; font-size: 10px; }
    #dior-doc-calendar-card .dior-doc-bs-cal-day-num { width: 21px; height: 21px; font-size: 10px; }
    #dior-doc-calendar-card .dior-doc-bs-cal-day-head .badge { font-size: 8px; padding: 2px 4px; }
    #dior-doc-calendar-card .dior-doc-bs-cal-event {
        display: block;
        max-width: 100%;
        margin: 2px 0;
        padding: 2px 1px;
        border-radius: 3px;
        font-size: 8px !important;
        line-height: 1.2;
        white-space: nowrap !important;
        overflow: hidden;
        text-overflow: ellipsis;
        overflow-wrap: normal;
        word-break: normal;
    }
    #dior-doc-calendar-card .dior-doc-bs-cal-cell > .small { font-size: 8px !important; line-height: 1.2; white-space: nowrap; }
    #dior-doc-calendar-card #dior-doc-bs-cal-selected-body { max-height: 360px !important; }
    #dior-doc-calendar-card .card-footer > .d-flex { justify-content: flex-start !important; gap: 10px 14px !important; }
}
@media (max-width: 380px) {
    #tab-doc-appointments { width: calc(100% + 48px) !important; margin-left: -24px !important; margin-right: -24px !important; padding-left: 8px !important; padding-right: 8px !important; }
    #dior-doc-calendar-card .dior-doc-bs-cal-cell { height: 62px !important; }
    #dior-doc-calendar-card .dior-doc-bs-cal-event { font-size: 7px !important; }
}

</style>

<script>
(function(){
    'use strict';
    var root=document.getElementById('tab-doc-appointments');
    if(!root||root.dataset.bootstrapCalendarReady==='1')return;
    root.dataset.bootstrapCalendarReady='1';
    var events=<?php echo $doctor_calendar_json ?: '[]'; ?>;
    var currentDate=new Date(), selectedDate='', viewMode='month', typeFilter='all', statusFilter='all';
    var monthNames=['January','February','March','April','May','June','July','August','September','October','November','December'];
    var dayNames=['Sunday','Monday','Tuesday','Wednesday','Thursday','Friday','Saturday'];
    var $=function(id){return root.querySelector('#'+id);};
    function ymd(d){return d.getFullYear()+'-'+String(d.getMonth()+1).padStart(2,'0')+'-'+String(d.getDate()).padStart(2,'0');}
    function dateLabel(value){if(!value)return '';var p=value.split('-').map(Number);var d=new Date(p[0],p[1]-1,p[2]);return dayNames[d.getDay()]+', '+monthNames[d.getMonth()].slice(0,3)+' '+d.getDate()+', '+d.getFullYear();}
    function filtered(){return events.filter(function(a){var t=(a.type||'').toLowerCase(),s=(a.status||'').toLowerCase();if(typeFilter==='video'&&t.indexOf('video')<0&&t.indexOf('telehealth')<0)return false;if(typeFilter==='phone'&&t.indexOf('phone')<0&&t.indexOf('audio')<0)return false;if(typeFilter==='clinic'&&t.indexOf('clinic')<0&&t.indexOf('person')<0)return false;if(statusFilter==='confirmed'&&['confirmed','scheduled','pending','active','in-queue'].indexOf(s)<0)return false;if(statusFilter==='completed'&&['completed','done','past'].indexOf(s)<0)return false;if(statusFilter==='cancelled'&&s!=='cancelled'&&s!=='canceled')return false;return true;});}
    function safe(value){return String(value==null?'':value).replace(/[&<>"']/g,function(c){return {'&':'&amp;','<':'&lt;','>':'&gt;','"':'&quot;',"'":'&#39;'}[c];});}
    function statusClass(s){s=(s||'').toLowerCase();return s==='completed'||s==='done'?'completed':(s==='cancelled'||s==='canceled'?'cancelled':'');}
    function showDetails(a){var html='<div class="text-start"><p><strong>Patient:</strong> '+safe(a.patient_name)+'</p><p><strong>Doctor:</strong> '+safe(a.doctor_name)+'</p><p><strong>Condition:</strong> '+safe(a.condition)+'</p><p><strong>Date &amp; Time:</strong> '+safe(dateLabel(a.date))+' at '+safe(a.time)+'</p><p><strong>Visit Type:</strong> '+safe(a.type)+'</p><p><strong>Status:</strong> '+safe(a.status)+'</p>';if(a.join_url&&/^https:\/\//i.test(a.join_url))html+='<a class="btn btn-primary w-100 mt-2" href="'+safe(a.join_url)+'" target="_blank" rel="noopener noreferrer" data-dior-meeting-join data-appt-uid="'+safe(a.id)+'"><i class="fas fa-video me-1"></i> Join Video Consultation</a>';html+='</div>';if(window.Swal&&typeof Swal.fire==='function'){Swal.fire({title:'Appointment Details',html:html,showConfirmButton:true,confirmButtonText:'Close',confirmButtonColor:'#2C6CB1',customClass:{popup:'rounded-4'}});}else{window.alert(a.patient_name+'\n'+dateLabel(a.date)+' '+a.time+'\n'+a.status);}}
    function appointmentCard(a){var s=statusClass(a.status),video=(a.type||'').toLowerCase().indexOf('video')>=0||(a.type||'').toLowerCase().indexOf('telehealth')>=0;var html='<div class="dior-doc-cal-appointment"><div class="d-flex justify-content-between align-items-center gap-2 mb-2"><strong class="text-dark">'+safe(a.patient_name)+'</strong><span class="badge rounded-pill '+(s==='completed'?'bg-success-subtle text-success':s==='cancelled'?'bg-danger-subtle text-danger':'bg-primary-subtle text-primary')+'">'+safe(a.status)+'</span></div><div class="small text-muted mb-1"><i class="far fa-clock me-1 text-primary"></i>'+safe(a.time)+'</div><div class="small text-secondary mb-2">'+safe(a.condition)+' · '+safe(a.type)+'</div><button type="button" class="btn btn-outline-primary btn-sm w-100" data-cal-details="'+safe(a.id)+'"><i class="fas fa-eye me-1"></i> View Details</button>';if(video&&a.join_url&&/^https:\/\//i.test(a.join_url))html+='<a class="btn btn-success btn-sm w-100 mt-2" href="'+safe(a.join_url)+'" target="_blank" rel="noopener noreferrer" data-dior-meeting-join data-appt-uid="'+safe(a.id)+'"><i class="fas fa-video me-1"></i> Join Consultation</a>';return html+'</div>';}
    function renderSelected(){var body=$('dior-doc-bs-cal-selected-body'),head=$('dior-doc-bs-cal-selected-header'),count=$('dior-doc-bs-cal-selected-count');if(!body)return;head.textContent=dateLabel(selectedDate);var list=filtered().filter(function(a){return a.date===selectedDate;});count.textContent=list.length+' Visit'+(list.length===1?'':'s');body.innerHTML=list.length?'<div class="d-flex flex-column gap-3">'+list.map(appointmentCard).join('')+'</div>':'<div class="text-center py-4 text-muted"><div class="mb-3" style="font-size:2.2rem;color:#cbd5e1;"><i class="far fa-calendar-xmark"></i></div><h6 class="text-dark fw-bold mb-1">No Consultations</h6><p class="small text-muted mb-3">There are no appointments scheduled for this date.</p><button type="button" class="btn btn-outline-primary btn-sm rounded-pill px-3" data-cal-book><i class="fas fa-calendar-plus me-1"></i> Schedule Consultation</button></div>';}
    function renderMonth(){var body=$('dior-doc-bs-cal-grid-body'),title=$('dior-doc-bs-cal-title');if(!body)return;var y=currentDate.getFullYear(),m=currentDate.getMonth();title.textContent=monthNames[m]+' '+y;var map={};filtered().forEach(function(a){if(!map[a.date])map[a.date]=[];map[a.date].push(a);});var first=new Date(y,m,1).getDay(),days=new Date(y,m+1,0).getDate(),prevDays=new Date(y,m,0).getDate(),total=first+days>35?42:35,today=ymd(new Date()),html='';for(var i=0;i<total;i++){if(i%7===0)html+='<tr>';var day,cellDate,other=false;if(i<first){day=prevDays-first+i+1;var pm=m===0?11:m-1,py=m===0?y-1:y;cellDate=ymd(new Date(py,pm,day));other=true;}else if(i<first+days){day=i-first+1;cellDate=ymd(new Date(y,m,day));}else{day=i-first-days+1;var nm=m===11?0:m+1,ny=m===11?y+1:y;cellDate=ymd(new Date(ny,nm,day));other=true;}var items=map[cellDate]||[];html+='<td class="dior-doc-bs-cal-cell'+(other?' is-other':'')+(cellDate===today?' is-today':'')+(cellDate===selectedDate?' is-selected':'')+'" data-cal-date="'+cellDate+'"><div class="dior-doc-bs-cal-day-head"><span class="dior-doc-bs-cal-day-num">'+day+'</span>'+(items.length?'<span class="badge bg-primary rounded-pill">'+items.length+'</span>':'')+'</div>';items.slice(0,2).forEach(function(a){var c=statusClass(a.status),type=(a.type||'').toLowerCase();var cls=c||((type.indexOf('phone')>=0||type.indexOf('audio')>=0)?'phone':'');html+='<div class="dior-doc-bs-cal-event '+cls+'" data-cal-details="'+safe(a.id)+'" title="'+safe(a.patient_name+' · '+a.time)+'">'+safe((a.time||'').split('-')[0].trim())+' '+safe(a.patient_name)+'</div>';});if(items.length>2)html+='<div class="small text-muted fw-bold">+'+(items.length-2)+' more</div>';html+='</td>';if(i%7===6)html+='</tr>';}body.innerHTML=html;renderSelected();}
    function renderAgenda(){var wrap=$('dior-doc-bs-cal-agenda-list');if(!wrap)return;var list=filtered().slice().sort(function(a,b){return (a.date+' '+a.time).localeCompare(b.date+' '+b.time);});if(!list.length){wrap.innerHTML='<div class="card border-0 bg-light p-4 text-center text-muted rounded-4"><i class="far fa-calendar-xmark fs-2 mb-2"></i><p class="mb-2 fw-medium">No appointments match the selected filters.</p><button type="button" class="btn btn-primary btn-sm rounded-pill" data-cal-book><i class="fas fa-calendar-plus me-1"></i> Book New Consultation</button></div>';return;}var group='';wrap.innerHTML=list.map(function(a){var label='';if(group!==a.date){group=a.date;label='<div class="d-flex align-items-center gap-2 mt-2 pt-2 border-top"><span class="badge bg-primary rounded-pill px-3 py-2">'+safe(dateLabel(a.date))+'</span></div>';}return label+appointmentCard(a);}).join('');}
    function render(){if(viewMode==='month'){ $('dior-doc-bs-cal-month-wrap').style.display='block';$('dior-doc-bs-cal-agenda-wrap').style.display='none';renderMonth();}else{$('dior-doc-bs-cal-month-wrap').style.display='none';$('dior-doc-bs-cal-agenda-wrap').style.display='block';renderAgenda();}}
    root.addEventListener('click',function(e){var dateCell=e.target.closest('[data-cal-date]');if(dateCell){selectedDate=dateCell.getAttribute('data-cal-date');renderMonth();return;}var detail=e.target.closest('[data-cal-details]');if(detail){var item=events.find(function(a){return String(a.id)===String(detail.getAttribute('data-cal-details'));});if(item)showDetails(item);return;}if(e.target.closest('[data-cal-book]')){if(typeof window.diorOpenDocBookerModal==='function')window.diorOpenDocBookerModal();else if(typeof window.diorDocSwitchTab==='function')window.diorDocSwitchTab('doc-appointments-book');}});
    $('dior-doc-bs-cal-prev').addEventListener('click',function(){currentDate.setMonth(currentDate.getMonth()-1);render();});
    $('dior-doc-bs-cal-next').addEventListener('click',function(){currentDate.setMonth(currentDate.getMonth()+1);render();});
    $('dior-doc-bs-cal-today').addEventListener('click',function(){currentDate=new Date();selectedDate=ymd(currentDate);render();});
    $('dior-doc-bs-cal-type-filter').addEventListener('change',function(){typeFilter=this.value;render();});
    $('dior-doc-bs-cal-status-filter').addEventListener('change',function(){statusFilter=this.value;render();});
    $('dior-doc-bs-cal-view-month').addEventListener('click',function(){viewMode='month';this.classList.add('btn-primary','active');this.classList.remove('btn-outline-secondary');$('dior-doc-bs-cal-view-agenda').classList.remove('btn-primary','active');$('dior-doc-bs-cal-view-agenda').classList.add('btn-outline-secondary');render();});
    $('dior-doc-bs-cal-view-agenda').addEventListener('click',function(){viewMode='agenda';this.classList.add('btn-primary','active');this.classList.remove('btn-outline-secondary');$('dior-doc-bs-cal-view-month').classList.remove('btn-primary','active');$('dior-doc-bs-cal-view-month').classList.add('btn-outline-secondary');render();});
    root.addEventListener('dior:calendar-refresh',render);
    selectedDate=ymd(new Date());render();
    if(window.MutationObserver){new MutationObserver(function(){if(root.style.display!=='none'&&root.classList.contains('active'))render();}).observe(root,{attributes:true,attributeFilter:['style','class']});}
})();
</script>
