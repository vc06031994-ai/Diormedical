<?php
/**
 * Dior Medical — New Prescription Email (Patient)
 * Privacy-first: do not include prescription contents or sensitive clinical details.
 *
 * Expected variables:
 * $patient_name, $doctor_name, $prescription_issue_date,
 * $secure_prescription_url, $site_name, $site_url.
 *
 * The destination portal MUST authenticate the patient and verify prescription
 * ownership before displaying or downloading any prescription.
 */
if (!defined('ABSPATH')) { exit; }

$patient_name = isset($patient_name) ? sanitize_text_field($patient_name) : 'Patient';
$doctor_name = isset($doctor_name) ? sanitize_text_field($doctor_name) : 'Your doctor';
$prescription_issue_date = isset($prescription_issue_date) ? sanitize_text_field($prescription_issue_date) : '';
$site_name = isset($site_name) ? sanitize_text_field($site_name) : get_bloginfo('name');
$site_url = isset($site_url) && filter_var($site_url, FILTER_VALIDATE_URL) ? $site_url : home_url('/');
$secure_prescription_url = isset($secure_prescription_url) ? esc_url_raw($secure_prescription_url) : '';
if (!$secure_prescription_url || stripos($secure_prescription_url, 'https://') !== 0) {
    $secure_prescription_url = home_url('/patient-dashboard/prescriptions/');
}
$logo_id = get_theme_mod('custom_logo');
$logo_url = $logo_id ? wp_get_attachment_image_url($logo_id, 'full') : '';
?>
<!doctype html>
<html><head><meta charset="utf-8"><meta name="viewport" content="width=device-width, initial-scale=1"></head>
<body style="margin:0;padding:24px 12px;background:#f4f8fc;font-family:Arial,Helvetica,sans-serif;color:#24364b;">
<table role="presentation" width="100%" cellspacing="0" cellpadding="0"><tr><td align="center">
<table role="presentation" width="600" cellspacing="0" cellpadding="0" style="width:100%;max-width:600px;background:#fff;border-radius:12px;overflow:hidden;">
<tr><td style="background:#2069ad;padding:24px 32px;color:#fff;"><table role="presentation" width="100%"><tr>
<td><?php if ($logo_url) : ?><img src="<?php echo esc_url($logo_url); ?>" alt="<?php echo esc_attr($site_name); ?>" style="max-width:145px;max-height:48px;display:block;"><?php else : ?><strong style="font-size:20px;"><?php echo esc_html($site_name); ?></strong><?php endif; ?></td>
<td align="right" style="font-size:12px;line-height:18px;">Concierge Telehealth<br>&amp; Clinical Medicine</td>
</tr></table></td></tr>
<tr><td style="padding:34px 36px 12px;">
<p style="margin:0 0 10px;font-size:14px;color:#5b6b7d;">Hello <?php echo esc_html($patient_name); ?>,</p>
<h1 style="margin:0 0 12px;font-size:25px;line-height:1.3;color:#102b60;">A new prescription is available</h1>
<p style="margin:0;font-size:15px;line-height:1.7;color:#526174;">Your doctor has made a prescription available in your secure patient portal. Sign in to view it.</p>
</td></tr>
<tr><td style="padding:20px 36px 8px;"><table role="presentation" width="100%" cellspacing="0" cellpadding="0" style="background:#f4f8fc;border:1px solid #e2ebf4;border-radius:9px;"><tr><td style="padding:20px 22px;">
<p style="margin:0 0 14px;font-size:12px;letter-spacing:1px;text-transform:uppercase;color:#718198;">Prescription notification</p>
<p style="margin:0 0 6px;font-size:13px;color:#718198;">Prescribing doctor</p>
<p style="margin:0 0 16px;font-size:16px;font-weight:bold;color:#102b60;"><?php echo esc_html($doctor_name); ?></p>
<p style="margin:0 0 6px;font-size:13px;color:#718198;">Issue date</p>
<p style="margin:0;font-size:15px;font-weight:bold;color:#102b60;"><?php echo esc_html($prescription_issue_date); ?></p>
</td></tr></table></td></tr>
<tr><td style="padding:18px 36px 8px;"><p style="margin:0;font-size:14px;line-height:1.7;color:#526174;">For your privacy, prescription details are not included in this email. Use the secure button below and sign in to your patient portal to access your prescription.</p></td></tr>
<tr><td align="center" style="padding:26px 36px 32px;"><a href="<?php echo esc_url($secure_prescription_url); ?>" style="display:inline-block;background:#176dbb;color:#fff;text-decoration:none;font-size:15px;font-weight:bold;padding:14px 30px;border-radius:6px;">View Prescription</a></td></tr>
<tr><td style="background:#f4f8fc;padding:20px 30px;text-align:center;font-size:12px;line-height:1.7;color:#7b8796;">© <?php echo esc_html(wp_date('Y')); ?> <?php echo esc_html($site_name); ?>. All rights reserved.<br><a href="<?php echo esc_url($site_url); ?>" style="color:#176dbb;text-decoration:none;">Visit Website</a></td></tr>
</table></td></tr></table>
</body></html>
