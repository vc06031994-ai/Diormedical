<section class="dior-tab-panel dior-appt-screen" id="tab-doc-patients-records" style="display: none;">
    <!-- Breadcrumb Header -->
    <div class="mb-4" style="display: flex; justify-content: space-between; align-items: center;">
        <div>
            <h4 class="mb-0 text-dark" style="font-size: 20px; font-weight: 700;">Patient Records</h4>
        </div>
        <div>
            <ul class="breadcrumb-list"
                style="display: flex; list-style: none; padding: 0; margin: 0; gap: 8px; align-items: center;">
                <li><a href="#"><i class="fas fa-house" style="font-size: 14px; color: #1A528E;"></i></a></li>
                <li><span style="color: #94A3B8;">/</span></li>
                <li><a href="javascript:void(0)"
                        style="font-size: 14px; color: #64748B; text-decoration: none; font-weight: 500;">Patients</a>
                </li>
                <li><span style="color: #94A3B8;">/</span></li>
                <li class="active"><span style="font-size: 14px; color: #1E293B; font-weight: 700;">Patient
                        Records</span></li>
            </ul>
        </div>
    </div>

    <!-- Subtab Navigation Bar -->
    <div class="dior-subtab-nav-bar px-1 mb-4">
        <button type="button" class="dior-subtab-pill btn" data-doc-tab="doc-patients-all" onclick="if(typeof diorDocSwitchTab==='function') diorDocSwitchTab('doc-patients-all');">
            <i class="fas fa-users"></i> All Patients
        </button>
        <button type="button" class="dior-subtab-pill btn" data-doc-tab="doc-patients-add" onclick="if(typeof diorDocSwitchTab==='function') diorDocSwitchTab('doc-patients-add');">
            <i class="fas fa-user-plus"></i> Add Patient
        </button>
        <button type="button" class="dior-subtab-pill btn" data-doc-tab="doc-patients-edit" onclick="if(typeof diorDocSwitchTab==='function') diorDocSwitchTab('doc-patients-edit');">
            <i class="fas fa-user-pen"></i> Edit Patient
        </button>
        <button type="button" class="dior-subtab-pill btn active" data-doc-tab="doc-patients-records" onclick="if(typeof diorDocSwitchTab==='function') diorDocSwitchTab('doc-patients-records');">
            <i class="fas fa-clipboard-user"></i> Patient Records
        </button>
        <button type="button" class="dior-subtab-pill btn" data-doc-tab="doc-patients-profile" onclick="if(typeof diorDocSwitchTab==='function') diorDocSwitchTab('doc-patients-profile');">
            <i class="fas fa-id-card"></i> Patient Profile
        </button>
    </div>

    <!-- Main Content -->
    <div class="view-appointment-card" style="margin-top: 10px;">
        <div class="va-header-container" style="border-bottom: 1px solid #E2E8F0; padding-bottom: 0;">
            <div class="va-title-container" style="position: relative; padding-bottom: 15px;">
                <h4 class="va-card-title"><i class="fas fa-clipboard-user text-primary me-2"></i>Patient Records</h4>
                <!-- Blue Accent Line -->
                <div
                    style="position: absolute; bottom: -1px; left: 0; width: 40px; height: 3px; background-color: #1A528E; border-radius: 3px 3px 0 0;">
                </div>
            </div>

            <div class="va-header-actions" style="margin-bottom: 15px; display: flex; align-items: center; gap: 10px;">
                <div class="input-group input-group-sm dior-bs-search-group" style="width: 240px;">

                    <input type="text" class="va-search-input form-control border-start-0 ps-0"
                        placeholder="Search records..." onkeyup="diorDocFilterPatientRecords(this.value)">
                </div>

                <div class="va-actions-group">
                    <button type="button" class="va-icon-btn btn" id="dior-pr-bulk-delete" style="display: none;" title="Delete Selected">
                        <i class="fas fa-trash"></i>
                    </button>
                    <button type="button" class="va-icon-btn btn" title="Add New Record">
                        <i class="fas fa-plus"></i>
                    </button>
                    <button type="button" class="va-icon-btn btn" title="Export to Excel">
                        <i class="fas fa-file-arrow-down"></i>
                    </button>
                    <button type="button" class="va-icon-btn btn" title="Refresh" onclick="window.location.reload()">
                        <i class="fas fa-rotate-right"></i>
                    </button>
                </div>
            </div>
        </div>

        <div class="va-table-wrapper">
            <table class="va-table dior-doc-table table table-hover" id="dior-patient-records-table">
                <thead>
                    <tr>
                        <th style="width: 40px; text-align: center;">
                            <input type="checkbox" id="pr-select-all" class="dior-checkbox form-check-input">
                        </th>
                        <th>PATIENT ID <i class="fas fa-sort"
                                style="color: #CBD5E1; margin-left: 5px; font-size: 10px;"></i></th>
                        <th>FULL NAME <i class="fas fa-sort"
                                style="color: #CBD5E1; margin-left: 5px; font-size: 10px;"></i></th>
                        <th>GENDER <i class="fas fa-sort"
                                style="color: #CBD5E1; margin-left: 5px; font-size: 10px;"></i></th>
                        <th>ADMISSION DATE <i class="fas fa-sort"
                                style="color: #CBD5E1; margin-left: 5px; font-size: 10px;"></i></th>
                        <th>DIAGNOSIS <i class="fas fa-sort"
                                style="color: #CBD5E1; margin-left: 5px; font-size: 10px;"></i></th>
                        <th>NEXT FOLLOW-UP <i class="fas fa-sort"
                                style="color: #CBD5E1; margin-left: 5px; font-size: 10px;"></i></th>
                        <th>STATUS <i class="fas fa-sort"
                                style="color: #CBD5E1; margin-left: 5px; font-size: 10px;"></i></th>
                        <th style="text-align: center;">ACTIONS</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $records = [
                        ['P001', 'John Doe', 'Male', '#3B82F6', 'Oct 1, 2024', 'Hypertension', 'Nov 1, 2024', 'Active', 'col-green'],
                        ['P002', 'Jane Smith', 'Female', '#EC4899', 'Oct 5, 2024', 'Type 2 Diabetes', 'Nov 10, 2024', 'Discharged', 'col-blue'],
                        ['P003', 'David Johnson', 'Male', '#3B82F6', 'Sep 15, 2024', 'Chronic Migraine', 'Oct 30, 2024', 'Discharged', 'col-blue'],
                        ['P004', 'Emily Davis', 'Female', '#EC4899', 'Oct 3, 2024', 'Asthma', 'Nov 3, 2024', 'Active', 'col-green'],
                        ['P005', 'Michael Brown', 'Male', '#3B82F6', 'Sep 20, 2024', 'Osteoarthritis', 'Oct 25, 2024', 'Discharged', 'col-blue'],
                        ['P006', 'Sophia Wilson', 'Female', '#EC4899', 'Oct 7, 2024', 'Hyperthyroidism', 'Oct 28, 2024', 'Discharged', 'col-blue'],
                        ['P007', 'Lucas Harris', 'Male', '#3B82F6', 'Aug 25, 2024', 'Chronic Obstructive Pulmonary...', 'Sep 30, 2024', 'Active', 'col-green'],
                        ['P008', 'Charlotte Moore', 'Female', '#EC4899', 'Oct 2, 2024', 'Psoriasis', 'Nov 5, 2024', 'Discharged', 'col-blue'],
                        ['P009', 'Ethan Taylor', 'Male', '#3B82F6', 'Oct 8, 2024', 'Anxiety Disorder', 'Nov 8, 2024', 'Discharged', 'col-blue'],
                        ['P010', 'Megan Clark', 'Female', '#EC4899', 'Sep 30, 2024', 'Depression', 'Oct 30, 2024', 'Observation', 'col-indigo']
                    ];

                    foreach ($records as $r):
                        ?>
                        <tr class="pr-row"
                            data-search="<?php echo strtolower(esc_attr($r[1] . ' ' . $r[0] . ' ' . $r[5])); ?>">
                            <td data-label="Select" style="text-align: center;">
                                <input type="checkbox" class="dior-checkbox pr-row-checkbox form-check-input">
                            </td>
                            <td data-label="Patient ID"><?php echo esc_html($r[0]); ?></td>
                            <td data-label="Patient" style="font-weight: 500; color: #1E293B;"><?php echo esc_html($r[1]); ?></td>
                            <td data-label="Gender">
                                <div style="display: flex; align-items: center; gap: 6px;">
                                    <i class="<?php echo $r[2] === 'Male' ? 'fas fa-mars' : 'fas fa-venus'; ?>"
                                        style="color: <?php echo esc_attr($r[3]); ?>;"></i>
                                    <span><?php echo esc_html($r[2]); ?></span>
                                </div>
                            </td>
                            <td data-label="Admission date">
                                <div style="display: flex; align-items: center; gap: 6px;">
                                    <i class="far fa-calendar" style="color: #3B82F6;"></i>
                                    <span><?php echo esc_html($r[4]); ?></span>
                                </div>
                            </td>
                            <td data-label="Diagnosis"><?php echo esc_html($r[5]); ?></td>
                            <td data-label="Follow-up date">
                                <div style="display: flex; align-items: center; gap: 6px;">
                                    <i class="far fa-calendar" style="color: #3B82F6;"></i>
                                    <span><?php echo esc_html($r[6]); ?></span>
                                </div>
                            </td>
                            <td data-label="Status">
                                <span class="dior-st <?php echo esc_attr($r[8]); ?>"><?php echo esc_html($r[7]); ?></span>
                            </td>
                            <td data-label="Actions">
                                <div class="va-row-actions" style="justify-content: center;">
                                    <button class="va-action-btn-sm va-btn-edit btn"><i class="fas fa-pen"></i></button>
                                    <button class="va-action-btn-sm va-btn-delete btn"><i class="fas fa-trash"></i></button>
                                </div>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</section>

<style>
    /* Custom status badges for this specific table */
    .dior-st.col-green {
        background-color: #DCFCE7;
        color: #16A34A;
    }

    .dior-st.col-blue {
        background-color: #DBEAFE;
        color: #2563EB;
    }

    .dior-st.col-indigo {
        background-color: #E0E7FF;
        color: #1A528E;
    }

    .dior-st {
        padding: 4px 10px;
        border-radius: 20px;
        font-size: 12px;
        font-weight: 600;
    }

    @media (max-width: 767.98px) {
        #dior-doctor-app #dior-doc-content #tab-doc-patients-records,
        #dior-doctor-app #dior-doc-content #tab-doc-patients-records *,
        #dior-doctor-app #dior-doc-content #tab-doc-patients-records *::before,
        #dior-doctor-app #dior-doc-content #tab-doc-patients-records *::after {
            box-sizing: border-box !important;
        }

        #dior-doctor-app #dior-doc-content #tab-doc-patients-records.dior-tab-panel {
            width: 100% !important;
            max-width: 100% !important;
            min-width: 0 !important;
            margin: 0 !important;
            padding: 14px 0 24px !important;
            overflow-x: clip;
        }

        #dior-doctor-app #dior-doc-content #tab-doc-patients-records .view-appointment-card,
        #dior-doctor-app #dior-doc-content #tab-doc-patients-records .va-header-container {
            width: 100% !important;
            max-width: 100% !important;
            box-sizing: border-box !important;
            margin-left: 0 !important;
            margin-right: 0 !important;
        }

        #dior-doctor-app #dior-doc-content #tab-doc-patients-records .view-appointment-card {
            padding-left: 0 !important;
            padding-right: 0 !important;
            overflow: hidden;
        }

        #tab-doc-patients-records > .mb-4:first-child {
            align-items: flex-start !important;
        }

        #tab-doc-patients-records > .mb-4:first-child .breadcrumb-list {
            display: none !important;
        }

        #tab-doc-patients-records .dior-subtab-nav-bar {
            display: grid;
            grid-template-columns: repeat(2, minmax(0, 1fr));
            gap: 8px;
            padding: 0 !important;
        }

        #tab-doc-patients-records .dior-subtab-pill {
            width: 100%;
            min-height: 40px;
            margin: 0 !important;
            font-size: 12px;
            white-space: nowrap;
        }

        #tab-doc-patients-records .dior-subtab-pill:last-child {
            grid-column: 1 / -1;
        }

        #tab-doc-patients-records .va-header-container {
            align-items: stretch !important;
            flex-direction: column;
            padding: 12px 12px 10px !important;
        }

        #tab-doc-patients-records .va-title-container {
            padding-bottom: 10px !important;
        }

        #tab-doc-patients-records .va-header-actions,
        #tab-doc-patients-records .dior-bs-search-group {
            width: 100% !important;
            margin: 0 !important;
        }

        #tab-doc-patients-records .va-header-actions {
            flex-wrap: wrap;
        }

        #tab-doc-patients-records .va-actions-group {
            width: 100%;
        }

        #tab-doc-patients-records .va-actions-group .va-icon-btn {
            flex: 1;
        }

        #tab-doc-patients-records .va-table-wrapper {
            overflow: hidden !important;
            width: 100% !important;
            max-width: 100% !important;
        }

        #tab-doc-patients-records #dior-patient-records-table {
            display: block;
            width: 100% !important;
            max-width: 100% !important;
            min-width: 0 !important;
            table-layout: fixed !important;
            background: transparent !important;
        }

        #tab-doc-patients-records #dior-patient-records-table thead {
            display: none;
        }

        #tab-doc-patients-records #dior-patient-records-table tbody {
            display: grid;
            gap: 10px;
            width: 100% !important;
            max-width: 100% !important;
            min-width: 0 !important;
        }

        #tab-doc-patients-records #dior-patient-records-table tbody tr.pr-row {
            position: relative;
            display: grid;
            grid-template-columns: repeat(2, minmax(0, 1fr));
            gap: 10px 14px;
            padding: 14px 12px 12px 44px;
            border: 1px solid #E2E8F0;
            border-radius: 14px;
            background: #fff;
            box-shadow: 0 1px 3px rgba(15, 23, 42, .06);
            width: 100% !important;
            max-width: 100% !important;
            min-width: 0 !important;
        }

        #tab-doc-patients-records #dior-patient-records-table tbody tr.pr-row[data-filter-hidden="true"] {
            display: none;
        }

        #tab-doc-patients-records #dior-patient-records-table tbody td {
            min-width: 0;
            padding: 0 !important;
            border: 0 !important;
            background: transparent !important;
            white-space: normal !important;
        }

        #tab-doc-patients-records #dior-patient-records-table tbody td:nth-child(1) {
            position: absolute;
            left: 12px;
            top: 16px;
        }

        #tab-doc-patients-records #dior-patient-records-table tbody td:nth-child(2) {
            grid-column: 1 / 2;
            grid-row: 2;
            font-size: 11px !important;
            color: #64748B !important;
        }

        #tab-doc-patients-records #dior-patient-records-table tbody td:nth-child(2)::before {
            content: attr(data-label) ": ";
            font-weight: 600;
        }

        #tab-doc-patients-records #dior-patient-records-table tbody td:nth-child(3) {
            grid-column: 1 / -1;
            grid-row: 1;
            padding-right: 88px !important;
            font-size: 15px !important;
        }

        #tab-doc-patients-records #dior-patient-records-table tbody td:nth-child(4) {
            grid-column: 2;
            grid-row: 2;
            justify-self: end;
            color: #64748B !important;
        }

        #tab-doc-patients-records #dior-patient-records-table tbody td:nth-child(5),
        #tab-doc-patients-records #dior-patient-records-table tbody td:nth-child(6),
        #tab-doc-patients-records #dior-patient-records-table tbody td:nth-child(7) {
            display: block;
            font-size: 12px !important;
        }

        #tab-doc-patients-records #dior-patient-records-table tbody td:nth-child(5)::before,
        #tab-doc-patients-records #dior-patient-records-table tbody td:nth-child(6)::before,
        #tab-doc-patients-records #dior-patient-records-table tbody td:nth-child(7)::before {
            content: attr(data-label);
            display: block;
            margin-bottom: 4px;
            color: #94A3B8;
            font-size: 10px;
            font-weight: 700;
            text-transform: uppercase;
        }

        #tab-doc-patients-records #dior-patient-records-table tbody td:nth-child(6) {
            grid-column: 1 / -1;
        }

        #tab-doc-patients-records #dior-patient-records-table tbody td:nth-child(8) {
            position: absolute;
            top: 13px;
            right: 12px;
        }

        #tab-doc-patients-records #dior-patient-records-table tbody td:nth-child(9) {
            grid-column: 1 / -1;
            display: flex;
            justify-content: flex-end;
            border-top: 1px solid #F1F5F9 !important;
            padding-top: 10px !important;
        }

        #tab-doc-patients-records .va-row-actions {
            gap: 8px;
        }
    }
</style>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        const prSelectAll = document.getElementById('pr-select-all');
        const prCheckboxes = document.querySelectorAll('.pr-row-checkbox');
        const prBulkDelete = document.getElementById('dior-pr-bulk-delete');

        function checkPRSelection() {
            if (!prBulkDelete) return;
            const anyChecked = document.querySelector('.pr-row-checkbox:checked');
            if (anyChecked) {
                prBulkDelete.style.display = 'flex';
            } else {
                prBulkDelete.style.display = 'none';
            }

            // Update master checkbox
            const allChecked = prCheckboxes.length > 0 && document.querySelectorAll('.pr-row-checkbox:checked').length === prCheckboxes.length;
            if (prSelectAll) prSelectAll.checked = allChecked;
        }

        if (prSelectAll) {
            prSelectAll.addEventListener('change', function () {
                prCheckboxes.forEach(cb => cb.checked = this.checked);
                checkPRSelection();
            });
        }

        prCheckboxes.forEach(cb => {
            cb.addEventListener('change', checkPRSelection);
        });
    });

    window.diorDocFilterPatientRecords = function (val) {
        val = val.toLowerCase().trim();
        const table = document.getElementById('dior-patient-records-table');
        if (!table) return;
        const rows = table.querySelectorAll('tbody tr.pr-row');
        rows.forEach(row => {
            const text = row.getAttribute('data-search') || '';
            if (val === '' || text.includes(val)) {
                row.removeAttribute('data-filter-hidden');
            } else {
                row.setAttribute('data-filter-hidden', 'true');
            }
        });
        if (table._diorRenderPage) {
            table._diorRenderPage(1);
        }
    };
</script>