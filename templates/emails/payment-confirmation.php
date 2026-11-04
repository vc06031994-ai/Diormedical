<?php
/**
 * DIOR MEDICAL - Payment Confirmation Email
 *
 * Patient-facing email. Populate these variables from a verified Stripe payment
 * event using the companion Stripe mapper / your existing webhook handler.
 *
 * Expected variables:
 * $patient_name, $payment_amount_display, $payment_currency,
 * $payment_reference, $payment_service_name, $payment_appointment_reference,
 * $payment_date_display, $receipt_url, $patient_dashboard_url,
 * $site_url, $site_name.
 */
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

$patient_name = isset($patient_name) ? (string)$patient_name : 'Patient';
$payment_amount_display = isset($payment_amount_display) ? (string)$payment_amount_display : '';
$payment_currency = isset($payment_currency) ? strtoupper((string)$payment_currency) : '';
$payment_reference = isset($payment_reference) ? (string)$payment_reference : '';
$payment_service_name = isset($payment_service_name) ? (string)$payment_service_name : 'DIOR MEDICAL Consultation';
$payment_appointment_reference = isset($payment_appointment_reference) ? (string)$payment_appointment_reference : '';
$payment_date_display = isset($payment_date_display) ? (string)$payment_date_display : '';
$receipt_url = isset($receipt_url) ? (string)$receipt_url : '';
$patient_dashboard_url = isset($patient_dashboard_url) ? (string)$patient_dashboard_url : home_url('/patient-dashboard/');
$site_url = isset($site_url) ? (string)$site_url : home_url('/');
$site_name = isset($site_name) ? (string)$site_name : 'DIOR MEDICAL';
$year = wp_date('Y');

$logo_id = (int)get_theme_mod('custom_logo');
$logo_url = $logo_id ? wp_get_attachment_image_url($logo_id, 'full') : '';

$receipt_destination = $receipt_url !== '' ? $receipt_url : $patient_dashboard_url;
$receipt_button_label = $receipt_url !== '' ? 'View Receipt' : 'View Payment Details';
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<meta name="color-scheme" content="light">
<title><?php echo esc_html__('Payment Confirmation | DIOR MEDICAL', 'dior-medical-dashboard'); ?></title>
</head>
<body style="margin:0;padding:0;background:#f4f8fc;font-family:Arial,Helvetica,sans-serif;color:#102b60;">
<div style="display:none;font-size:1px;line-height:1px;max-height:0;max-width:0;opacity:0;overflow:hidden;">Your DIOR MEDICAL payment was successful. View your payment details and receipt.</div>
<table role="presentation" width="100%" cellpadding="0" cellspacing="0" border="0" style="width:100%;background:#f4f8fc;">
<tr><td align="center" style="padding:17px 10px 24px;">
<table role="presentation" width="904" cellpadding="0" cellspacing="0" border="0" style="width:100%;max-width:904px;background:#fff;border:1px solid #e2ebf4;border-collapse:collapse;">

<!-- Same approved DIOR MEDICAL header -->
<tr><td style="padding:22px 45px;background:#2069ad;">
<table role="presentation" width="100%" cellpadding="0" cellspacing="0" border="0"><tr>
<td width="29%" valign="middle" style="padding:0 28px 0 0;">
<a href="<?php echo esc_url($site_url); ?>" style="text-decoration:none;">
<?php if ($logo_url) : ?>
<img src="<?php echo esc_url($logo_url); ?>" width="215" alt="<?php echo esc_attr($site_name); ?>" style="display:block;width:100%;max-width:215px;height:auto;border:0;">
<?php else : ?>
<span style="font-size:30px;line-height:1.2;font-weight:bold;color:#fff;"><?php echo esc_html($site_name); ?></span>
<?php endif; ?>
</a></td>
<td width="71%" valign="middle" style="padding-left:35px;border-left:1px solid #80add4;color:#fff;font-size:21px;line-height:1.5;">Concierge Telehealth &amp;<br>Clinical Medicine</td>
</tr></table></td></tr>

<!-- Main content -->
<tr><td style="padding:47px 57px 35px;">
<p style="margin:0 0 13px;font-size:13px;line-height:1.4;letter-spacing:2.5px;font-weight:700;color:#176dbb;">PAYMENT CONFIRMATION</p>
<h1 style="margin:0 0 25px;font-family:Arial,Helvetica,sans-serif;font-size:42px;line-height:1.2;font-weight:700;letter-spacing:-1.2px;color:#102b60;">Your Payment Was Successful</h1>
<p style="margin:0 0 20px;font-size:25px;line-height:1.5;color:#17366f;">Dear <?php echo esc_html($patient_name); ?>,</p>
<p style="margin:0 0 30px;font-size:24px;line-height:1.5;color:#17366f;">Thank you for your payment. Your transaction has been successfully processed by DIOR MEDICAL. Please keep this email for your records.</p>

<!-- Success panel -->
<table role="presentation" width="100%" cellpadding="0" cellspacing="0" border="0" style="margin:0 0 28px;background:#eef6fd;border:1px solid #d8e9f8;border-radius:8px;"><tr>
<td width="94" align="center" valign="middle" style="padding:20px 10px;">
<table role="presentation" width="66" height="66" cellpadding="0" cellspacing="0" border="0" style="width:66px;height:66px;background:#dceeff;border-radius:50%;"><tr><td align="center" valign="middle" style="font-size:31px;line-height:1;color:#176dbb;">&#10003;</td></tr></table>
</td>
<td valign="middle" style="padding:18px 18px 18px 3px;">
<p style="margin:0 0 6px;font-size:12px;line-height:1.4;letter-spacing:1.5px;font-weight:700;color:#176dbb;">PAYMENT STATUS</p>
<p style="margin:0;font-size:23px;line-height:1.45;font-weight:700;color:#102b60;">Payment Successful</p>
<p style="margin:4px 0 0;font-size:15px;line-height:1.4;color:#6680a2;">Your payment has been confirmed.</p>
</td></tr></table>

<!-- Amount panel -->
<table role="presentation" width="100%" cellpadding="0" cellspacing="0" border="0" style="margin:0 0 28px;background:#fff;border:1px solid #dce9f6;border-radius:8px;"><tr>
<td align="center" style="padding:25px 20px 27px;">
<p style="margin:0 0 9px;font-size:12px;line-height:1.4;letter-spacing:1.7px;font-weight:700;color:#6680a2;">TOTAL AMOUNT PAID</p>
<p style="margin:0;font-size:38px;line-height:1.25;font-weight:700;color:#176dbb;"><?php echo esc_html($payment_amount_display !== '' ? $payment_amount_display : 'Amount unavailable'); ?></p>
<?php if ($payment_currency !== '') : ?><p style="margin:6px 0 0;font-size:14px;line-height:1.4;color:#6680a2;"><?php echo esc_html($payment_currency); ?></p><?php endif; ?>
</td></tr></table>

<p style="margin:0 0 17px;font-size:24px;line-height:1.45;color:#17366f;">Payment details</p>
<table role="presentation" width="100%" cellpadding="0" cellspacing="0" border="0" style="background:#f7fbff;border:1px solid #dfebf7;border-radius:7px;">
<tr>
<td width="86" align="center" valign="middle" style="padding:15px 8px 13px 14px;"><table role="presentation" width="58" height="58" cellpadding="0" cellspacing="0" border="0" style="width:58px;height:58px;background:#eaf4fd;border-radius:50%;"><tr><td align="center" valign="middle" style="font-size:27px;color:#0870c8;">&#128179;</td></tr></table></td>
<td valign="middle" style="padding:14px 17px 13px 4px;border-bottom:1px solid #dce9f6;">
<p style="margin:0 0 5px;font-size:12px;letter-spacing:1.3px;font-weight:700;color:#31527f;">TRANSACTION / PAYMENT REFERENCE</p>
<p style="margin:0;font-size:17px;line-height:1.45;font-weight:700;color:#17366f;word-break:break-word;"><?php echo esc_html($payment_reference !== '' ? $payment_reference : 'Not available'); ?></p>
</td></tr>
<tr>
<td width="86" align="center" valign="middle" style="padding:13px 8px 13px 14px;"><table role="presentation" width="58" height="58" cellpadding="0" cellspacing="0" border="0" style="width:58px;height:58px;background:#eaf4fd;border-radius:50%;"><tr><td align="center" valign="middle" style="font-size:27px;color:#0870c8;">&#128203;</td></tr></table></td>
<td valign="middle" style="padding:13px 17px 13px 4px;border-bottom:1px solid #dce9f6;">
<p style="margin:0 0 5px;font-size:12px;letter-spacing:1.3px;font-weight:700;color:#31527f;">RELATED APPOINTMENT / SERVICE</p>
<p style="margin:0;font-size:19px;line-height:1.45;font-weight:700;color:#17366f;"><?php echo esc_html($payment_service_name); ?></p>
<?php if ($payment_appointment_reference !== '') : ?><p style="margin:3px 0 0;font-size:14px;color:#6680a2;">Appointment reference: <?php echo esc_html($payment_appointment_reference); ?></p><?php endif; ?>
</td></tr>
<tr>
<td width="86" align="center" valign="middle" style="padding:13px 8px 15px 14px;"><table role="presentation" width="58" height="58" cellpadding="0" cellspacing="0" border="0" style="width:58px;height:58px;background:#eaf4fd;border-radius:50%;"><tr><td align="center" valign="middle" style="font-size:27px;color:#0870c8;">&#128197;</td></tr></table></td>
<td valign="middle" style="padding:13px 17px 15px 4px;">
<p style="margin:0 0 5px;font-size:12px;letter-spacing:1.3px;font-weight:700;color:#31527f;">PAYMENT DATE</p>
<p style="margin:0;font-size:19px;line-height:1.45;font-weight:700;color:#17366f;"><?php echo esc_html($payment_date_display !== '' ? $payment_date_display : 'Not available'); ?></p>
</td></tr>
</table>

<p style="margin:30px 0 12px;font-size:24px;line-height:1.4;color:#17366f;">Your payment receipt</p>
<p style="margin:0 0 35px;font-size:21px;line-height:1.5;color:#17366f;">
<?php if ($receipt_url !== '') : ?>
Your payment receipt is available. Use the button below to view your receipt securely.
<?php else : ?>
Your payment is confirmed. You can view the payment details in your secure patient dashboard.
<?php endif; ?>
</p>

<table role="presentation" align="center" cellpadding="0" cellspacing="0" border="0" style="margin:0 auto;"><tr>
<td align="center" bgcolor="#176dbb" style="background:#176dbb;border-radius:11px;">
<a href="<?php echo esc_url($receipt_destination); ?>" target="_blank" rel="noopener noreferrer" style="display:inline-block;padding:23px 48px;font-family:Arial,Helvetica,sans-serif;font-size:24px;line-height:1.35;font-weight:700;color:#fff;text-decoration:none;border-radius:11px;"><?php echo esc_html($receipt_button_label); ?> &nbsp;&#8594;</a>
</td></tr></table>
</td></tr>

<!-- Same approved footer -->
<tr><td align="center" style="padding:29px 20px 31px;border-top:1px solid #d5e1ed;background:#fff;">
<p style="margin:0;font-size:18px;line-height:1.6;color:#6684b3;">&copy; <?php echo esc_html($year); ?> <?php echo esc_html($site_name); ?>. All rights reserved. &nbsp;&bull;&nbsp; <a href="<?php echo esc_url($site_url); ?>" style="color:#075fc1;text-decoration:underline;">Visit Website</a></p>
</td></tr>
</table></td></tr></table>
</body></html>
