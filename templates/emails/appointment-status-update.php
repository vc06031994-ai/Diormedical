<?php
/**
 * Dior Medical — Appointment Status Update Email (Patient)
 *
 * Send this only after an EXISTING appointment's status changes.
 * Do not call from the booking-created/booking-confirmation workflow.
 *
 * Expected variables:
 * $patient_name, $appointment_status (confirmed|completed|missed|no-show),
 * $appointment_date_display, $doctor_name, $appointment_url,
 * $next_steps (optional; plain text or safe HTML), $site_name, $site_url.
 */

if (!defined('ABSPATH')) {
    exit;
}

$status = strtolower(trim((string) ($appointment_status ?? '')));
$status_map = [
    'confirmed' => [
        'label' => 'Confirmed',
        'heading' => 'Your appointment is confirmed',
        'message' => 'Your appointment has been confirmed. Please review the details below and be ready at the scheduled time.',
        'steps' => 'Please arrive a few minutes early, or use your secure dashboard link if this is a telehealth appointment.',
    ],
    'completed' => [
        'label' => 'Completed',
        'heading' => 'Your appointment is complete',
        'message' => 'Your appointment has been marked as completed. You can visit your dashboard for any available follow-up information.',
        'steps' => 'Check your dashboard for consultation notes, prescriptions, or follow-up instructions if your doctor has provided them.',
    ],
    'missed' => [
        'label' => 'Missed / No-show',
        'heading' => 'Your appointment was marked as missed',
        'message' => 'Our records show that the scheduled appointment was not attended.',
        'steps' => 'Please contact the clinic or use your dashboard to arrange another appointment. If you believe this status is incorrect, contact our team.',
    ],
    'no-show' => [
        'label' => 'Missed / No-show',
        'heading' => 'Your appointment was marked as missed',
        'message' => 'Our records show that the scheduled appointment was not attended.',
        'steps' => 'Please contact the clinic or use your dashboard to arrange another appointment. If you believe this status is incorrect, contact our team.',
    ],
];

if (!isset($status_map[$status])) {
    return; // Never send an email for an unsupported status.
}

$cfg = $status_map[$status];
$patient_name = $patient_name ?? 'Patient';
$doctor_name = $doctor_name ?? 'Your doctor';
$appointment_date_display = $appointment_date_display ?? '';
$appointment_url = (isset($appointment_url) && filter_var($appointment_url, FILTER_VALIDATE_URL)) ? $appointment_url : ($patient_dashboard_url ?? home_url('/patient-dashboard/'));
$site_name = $site_name ?? get_bloginfo('name');
$site_url = $site_url ?? home_url('/');
$next_steps_text = (isset($next_steps) && trim(wp_strip_all_tags((string) $next_steps)) !== '') ? $next_steps : $cfg['steps'];

$esc_next_steps = wp_kses_post(nl2br(esc_html(wp_strip_all_tags((string) $next_steps_text))));
$logo_id = function_exists('get_theme_mod') ? get_theme_mod('custom_logo') : 0;
$logo_url = $logo_id ? wp_get_attachment_image_url($logo_id, 'full') : '';
$status_color = ($status === 'missed' || $status === 'no-show') ? '#a65b27' : '#176dbb';
?>
<!doctype html>
<html>
<head><meta charset="utf-8"><meta name="viewport" content="width=device-width, initial-scale=1"></head>
<body style="margin:0;padding:24px 12px;background:#f4f8fc;font-family:Arial,Helvetica,sans-serif;color:#24364b;">
<table role="presentation" width="100%" cellspacing="0" cellpadding="0" border="0"><tr><td align="center">
<table role="presentation" width="600" cellspacing="0" cellpadding="0" border="0" style="width:100%;max-width:600px;background:#ffffff;border-radius:12px;overflow:hidden;">
<tr><td style="background:#2069ad;padding:24px 32px;color:#ffffff;">
<table role="presentation" width="100%"><tr>
<td><?php if ($logo_url) : ?><img src="<?php echo esc_url($logo_url); ?>" alt="<?php echo esc_attr($site_name); ?>" style="max-width:145px;max-height:48px;display:block;"><?php else : ?><strong style="font-size:20px;letter-spacing:.4px;"><?php echo esc_html($site_name); ?></strong><?php endif; ?></td>
<td align="right" style="font-size:12px;line-height:18px;">Concierge Telehealth<br>&amp; Clinical Medicine</td>
</tr></table>
</td></tr>
<tr><td style="padding:34px 36px 12px;">
<p style="margin:0 0 10px;font-size:14px;color:#5b6b7d;">Hello <?php echo esc_html($patient_name); ?>,</p>
<h1 style="margin:0 0 12px;font-size:25px;line-height:1.3;color:#102b60;"><?php echo esc_html($cfg['heading']); ?></h1>
<p style="margin:0;font-size:15px;line-height:1.7;color:#526174;"><?php echo esc_html($cfg['message']); ?></p>
</td></tr>
<tr><td style="padding:20px 36px 8px;">
<table role="presentation" width="100%" cellspacing="0" cellpadding="0" style="background:#f4f8fc;border:1px solid #e2ebf4;border-radius:9px;">
<tr><td style="padding:20px 22px;">
<p style="margin:0 0 14px;font-size:12px;letter-spacing:1px;text-transform:uppercase;color:#718198;">Appointment update</p>
<p style="margin:0 0 6px;font-size:13px;color:#718198;">Current status</p>
<p style="margin:0 0 16px;font-size:17px;font-weight:bold;color:<?php echo esc_attr($status_color); ?>;"><?php echo esc_html($cfg['label']); ?></p>
<p style="margin:0 0 6px;font-size:13px;color:#718198;">Appointment date</p>
<p style="margin:0 0 16px;font-size:15px;font-weight:bold;color:#102b60;"><?php echo esc_html($appointment_date_display); ?></p>
<p style="margin:0 0 6px;font-size:13px;color:#718198;">Doctor</p>
<p style="margin:0;font-size:15px;font-weight:bold;color:#102b60;"><?php echo esc_html($doctor_name); ?></p>
</td></tr></table>
</td></tr>
<tr><td style="padding:18px 36px 8px;">
<h2 style="margin:0 0 8px;font-size:17px;color:#102b60;">What happens next?</h2>
<p style="margin:0;font-size:14px;line-height:1.7;color:#526174;"><?php echo $esc_next_steps; ?></p>
</td></tr>
<tr><td align="center" style="padding:26px 36px 32px;">
<a href="<?php echo esc_url($appointment_url); ?>" style="display:inline-block;background:#176dbb;color:#ffffff;text-decoration:none;font-size:15px;font-weight:bold;padding:14px 28px;border-radius:6px;">View Appointment</a>
</td></tr>
<tr><td style="background:#f4f8fc;padding:20px 30px;text-align:center;font-size:12px;line-height:1.7;color:#7b8796;">
© <?php echo esc_html(wp_date('Y')); ?> <?php echo esc_html($site_name); ?>. All rights reserved.<br>
<a href="<?php echo esc_url($site_url); ?>" style="color:#176dbb;text-decoration:none;">Visit Website</a>
</td></tr>
</table>
</td></tr></table>
</body>
</html>
