<?php
/**
 * DIOR MEDICAL - Appointment Reminder Email
 * Optional variables:
 * $recipient_role ('patient'|'doctor'), $recipient_name, $patient_name, $doctor_name,
 * $doctor_specialty, $appointment_date, $appointment_time, $appointment_timezone,
 * $appointment_type, $appointment_location, $telehealth_join_url,
 * $appointment_details_url, $site_url, $site_name.
 */
if ( ! defined( 'ABSPATH' ) ) { exit; }

$recipient_role = isset($recipient_role) ? strtolower((string)$recipient_role) : 'patient';
$recipient_role = in_array($recipient_role, array('patient','doctor'), true) ? $recipient_role : 'patient';
$patient_name = isset($patient_name) ? (string)$patient_name : 'Patient';
$doctor_name = isset($doctor_name) ? (string)$doctor_name : 'Attending Physician';
$doctor_specialty = isset($doctor_specialty) ? (string)$doctor_specialty : '';
$recipient_name = isset($recipient_name) ? (string)$recipient_name : '';
if ($recipient_name === '') { $recipient_name = ('doctor' === $recipient_role) ? $doctor_name : $patient_name; }

$appointment_date = isset($appointment_date) ? (string)$appointment_date : '';
$appointment_time = isset($appointment_time) ? (string)$appointment_time : '';
$appointment_timezone = isset($appointment_timezone) ? (string)$appointment_timezone : '';
$appointment_type = isset($appointment_type) ? (string)$appointment_type : 'Consultation';
$appointment_location = isset($appointment_location) ? (string)$appointment_location : '';
$telehealth_join_url = isset($telehealth_join_url) ? (string)$telehealth_join_url : '';
$appointment_details_url = isset($appointment_details_url) ? (string)$appointment_details_url : home_url('/patient-dashboard/#tab=appointments');
$site_url = isset($site_url) ? (string)$site_url : home_url('/');
$site_name = isset($site_name) ? (string)$site_name : 'DIOR MEDICAL';
$year = wp_date('Y');
$logo_id = (int)get_theme_mod('custom_logo');
$logo_url = $logo_id ? wp_get_attachment_image_url($logo_id, 'full') : '';
$appointment_datetime = trim($appointment_date . ($appointment_time !== '' ? ' · ' . $appointment_time : ''));
if ($appointment_datetime === '') { $appointment_datetime = 'Please view your appointment for the scheduled date and time.'; }
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8"><meta name="viewport" content="width=device-width, initial-scale=1.0">
<meta name="color-scheme" content="light">
<title><?php echo esc_html__('Appointment Reminder | DIOR MEDICAL', 'dior-medical-dashboard'); ?></title>
</head>
<body style="margin:0;padding:0;background:#f4f8fc;font-family:Arial,Helvetica,sans-serif;color:#102b60;">
<div style="display:none;font-size:1px;line-height:1px;max-height:0;max-width:0;opacity:0;overflow:hidden;">Reminder: you have an upcoming DIOR MEDICAL appointment. Review the details before your visit.</div>
<table role="presentation" width="100%" cellpadding="0" cellspacing="0" border="0" style="width:100%;background:#f4f8fc;">
<tr><td align="center" style="padding:17px 10px 24px;">
<table role="presentation" width="904" cellpadding="0" cellspacing="0" border="0" style="width:100%;max-width:904px;background:#fff;border:1px solid #e2ebf4;border-collapse:collapse;">
<tr><td style="padding:22px 45px;background:#2069ad;">
<table role="presentation" width="100%" cellpadding="0" cellspacing="0" border="0"><tr>
<td width="29%" valign="middle" style="padding:0 28px 0 0;">
<a href="<?php echo esc_url($site_url); ?>" style="text-decoration:none;">
<?php if ($logo_url) : ?><img src="<?php echo esc_url($logo_url); ?>" width="215" alt="<?php echo esc_attr($site_name); ?>" style="display:block;width:100%;max-width:215px;height:auto;border:0;">
<?php else : ?><span style="font-size:30px;font-weight:bold;color:#fff;"><?php echo esc_html($site_name); ?></span><?php endif; ?>
</a></td>
<td width="71%" valign="middle" style="padding-left:35px;border-left:1px solid #80add4;color:#fff;font-size:21px;line-height:1.5;">Concierge Telehealth &amp;<br>Clinical Medicine</td>
</tr></table></td></tr>
<tr><td style="padding:47px 57px 35px;">
<p style="margin:0 0 13px;font-size:13px;letter-spacing:2.5px;font-weight:700;color:#176dbb;">UPCOMING APPOINTMENT</p>
<h1 style="margin:0 0 25px;font-family:Arial,Helvetica,sans-serif;font-size:42px;line-height:1.2;font-weight:700;letter-spacing:-1.2px;color:#102b60;">A Friendly Reminder About Your Appointment</h1>
<p style="margin:0 0 20px;font-size:25px;line-height:1.5;color:#17366f;">Dear <?php echo esc_html($recipient_name); ?>,</p>
<p style="margin:0 0 30px;font-size:24px;line-height:1.5;color:#17366f;">This is a reminder of your upcoming appointment with DIOR MEDICAL. Please review the details below and make any necessary preparations before your scheduled time.</p>
<table role="presentation" width="100%" cellpadding="0" cellspacing="0" border="0" style="margin:0 0 28px;background:#eef6fd;border:1px solid #d8e9f8;border-radius:8px;"><tr>
<td width="94" align="center" valign="middle" style="padding:20px 10px;"><table role="presentation" width="66" height="66" cellpadding="0" cellspacing="0" border="0" style="width:66px;height:66px;background:#dceeff;border-radius:50%;"><tr><td align="center" valign="middle" style="font-size:31px;color:#176dbb;">&#128197;</td></tr></table></td>
<td valign="middle" style="padding:18px 18px 18px 3px;"><p style="margin:0 0 6px;font-size:12px;letter-spacing:1.5px;font-weight:700;color:#176dbb;">YOUR APPOINTMENT DATE &amp; TIME</p>
<p style="margin:0;font-size:23px;line-height:1.45;font-weight:700;color:#102b60;"><?php echo esc_html($appointment_datetime); ?></p>
<?php if ($appointment_timezone !== '') : ?><p style="margin:4px 0 0;font-size:15px;color:#6680a2;"><?php echo esc_html($appointment_timezone); ?></p><?php endif; ?>
</td></tr></table>
<table role="presentation" width="100%" cellpadding="0" cellspacing="0" border="0" style="background:#f7fbff;border:1px solid #dfebf7;border-radius:7px;">
<tr><td width="86" align="center" valign="middle" style="padding:16px 8px 14px 14px;"><table role="presentation" width="58" height="58" cellpadding="0" cellspacing="0" border="0" style="width:58px;height:58px;background:#eaf4fd;border-radius:50%;"><tr><td align="center" valign="middle" style="font-size:28px;color:#0870c8;">&#128100;</td></tr></table></td>
<td valign="middle" style="padding:15px 17px 14px 4px;border-bottom:1px solid #dce9f6;">
<?php if ('doctor' === $recipient_role) : ?><p style="margin:0 0 5px;font-size:12px;letter-spacing:1.3px;font-weight:700;color:#31527f;">PATIENT</p><p style="margin:0;font-size:21px;font-weight:700;color:#17366f;"><?php echo esc_html($patient_name); ?></p>
<?php else : ?><p style="margin:0 0 5px;font-size:12px;letter-spacing:1.3px;font-weight:700;color:#31527f;">DOCTOR</p><p style="margin:0;font-size:21px;font-weight:700;color:#17366f;"><?php echo esc_html($doctor_name); ?></p><?php if ($doctor_specialty !== '') : ?><p style="margin:3px 0 0;font-size:15px;color:#6680a2;"><?php echo esc_html($doctor_specialty); ?></p><?php endif; ?><?php endif; ?>
</td></tr>
<tr><td width="86" align="center" valign="middle" style="padding:14px 8px 16px 14px;"><table role="presentation" width="58" height="58" cellpadding="0" cellspacing="0" border="0" style="width:58px;height:58px;background:#eaf4fd;border-radius:50%;"><tr><td align="center" valign="middle" style="font-size:27px;color:#0870c8;">&#128249;</td></tr></table></td>
<td valign="middle" style="padding:14px 17px 16px 4px;"><p style="margin:0 0 5px;font-size:12px;letter-spacing:1.3px;font-weight:700;color:#31527f;">APPOINTMENT TYPE</p><p style="margin:0;font-size:20px;font-weight:700;color:#17366f;"><?php echo esc_html($appointment_type); ?></p></td></tr>
</table>
<?php if ($appointment_location !== '' || $telehealth_join_url !== '') : ?>
<table role="presentation" width="100%" cellpadding="0" cellspacing="0" border="0" style="margin:22px 0 0;background:#eff7ff;border:1px solid #dceafb;border-radius:7px;"><tr>
<td width="86" align="center" valign="middle" style="padding:16px 8px 16px 14px;"><table role="presentation" width="58" height="58" cellpadding="0" cellspacing="0" border="0" style="width:58px;height:58px;background:#eaf4fd;border-radius:50%;"><tr><td align="center" valign="middle" style="font-size:27px;color:#0870c8;"><?php echo $telehealth_join_url !== '' ? '&#128187;' : '&#128205;'; ?></td></tr></table></td>
<td valign="middle" style="padding:15px 17px 15px 4px;">
<?php if ($telehealth_join_url !== '') : ?><p style="margin:0 0 6px;font-size:12px;letter-spacing:1.3px;font-weight:700;color:#176dbb;">TELEHEALTH JOINING INSTRUCTIONS</p><p style="margin:0;font-size:16px;line-height:1.6;color:#17366f;">Join your video consultation at the scheduled time using the secure link below. Please check your camera and microphone before joining.</p><p style="margin:10px 0 0;"><a href="<?php echo esc_url($telehealth_join_url); ?>" style="font-size:16px;font-weight:700;color:#075fc1;text-decoration:underline;">Join Telehealth Appointment</a></p>
<?php else : ?><p style="margin:0 0 6px;font-size:12px;letter-spacing:1.3px;font-weight:700;color:#176dbb;">APPOINTMENT LOCATION</p><p style="margin:0;font-size:16px;line-height:1.6;color:#17366f;"><?php echo esc_html($appointment_location); ?></p><?php endif; ?>
</td></tr></table><?php endif; ?>
<p style="margin:32px 0 12px;font-size:24px;color:#17366f;">Please be ready for your appointment</p>
<p style="margin:0 0 35px;font-size:21px;line-height:1.5;color:#17366f;">You can view your appointment information and any additional instructions through your secure patient portal.</p>
<table role="presentation" align="center" cellpadding="0" cellspacing="0" border="0" style="margin:0 auto;"><tr><td align="center" bgcolor="#176dbb" style="background:#176dbb;border-radius:11px;"><a href="<?php echo esc_url($appointment_details_url); ?>" target="_blank" style="display:inline-block;padding:23px 48px;font-size:24px;line-height:1.35;font-weight:700;color:#fff;text-decoration:none;border-radius:11px;">View Appointment &nbsp;&#8594;</a></td></tr></table>
</td></tr>
<tr><td align="center" style="padding:29px 20px 31px;border-top:1px solid #d5e1ed;background:#fff;"><p style="margin:0;font-size:18px;line-height:1.6;color:#6684b3;">&copy; <?php echo esc_html($year); ?> <?php echo esc_html($site_name); ?>. All rights reserved. &nbsp;&bull;&nbsp; <a href="<?php echo esc_url($site_url); ?>" style="color:#075fc1;text-decoration:underline;">Visit Website</a></p></td></tr>
</table></td></tr></table>
</body></html>
