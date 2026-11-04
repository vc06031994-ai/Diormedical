<?php
/**
 * Dior Medical — Doctor-Created Patient Account Welcome Email
 *
 * Send ONLY when the doctor explicitly selected the "Send welcome email"
 * option on the patient-creation form.
 *
 * Expected variables:
 * $patient_name, $patient_email, $login_url, $activation_url (optional),
 * $site_name, $site_url.
 *
 * Security:
 * - Never include a password in this email.
 * - Use a one-time, expiring activation URL when account activation is required.
 * - Otherwise link to the normal login page.
 * - The destination must enforce authentication and account ownership.
 */

if (!defined('ABSPATH')) {
    exit;
}

$patient_name = isset($patient_name) ? sanitize_text_field($patient_name) : 'Patient';
$site_name = isset($site_name) ? sanitize_text_field($site_name) : get_bloginfo('name');
$site_url = isset($site_url) && filter_var($site_url, FILTER_VALIDATE_URL) ? $site_url : home_url('/');
$login_url = isset($login_url) ? esc_url_raw($login_url) : wp_login_url();

// Prefer a one-time activation URL if the account requires activation.
// The activation endpoint should validate a random, single-use, expiring token.
$activation_url = isset($activation_url) ? esc_url_raw($activation_url) : '';
$account_access_url = ($activation_url && stripos($activation_url, 'https://') === 0)
    ? $activation_url
    : $login_url;

$logo_id = get_theme_mod('custom_logo');
$logo_url = $logo_id ? wp_get_attachment_image_url($logo_id, 'full') : '';
?>
<!doctype html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Your Patient Account Is Ready</title>
</head>
<body style="margin:0;padding:24px 12px;background:#f4f8fc;font-family:Arial,Helvetica,sans-serif;color:#24364b;">
<table role="presentation" width="100%" cellspacing="0" cellpadding="0" border="0">
<tr><td align="center">
<table role="presentation" width="600" cellspacing="0" cellpadding="0" border="0" style="width:100%;max-width:600px;background:#ffffff;border-radius:12px;overflow:hidden;">
    <tr>
        <td style="background:#2069ad;padding:24px 32px;color:#ffffff;">
            <table role="presentation" width="100%" cellspacing="0" cellpadding="0" border="0"><tr>
                <td>
                    <?php if ($logo_url) : ?>
                        <img src="<?php echo esc_url($logo_url); ?>" alt="<?php echo esc_attr($site_name); ?>" style="max-width:145px;max-height:48px;display:block;">
                    <?php else : ?>
                        <strong style="font-size:20px;letter-spacing:.4px;"><?php echo esc_html($site_name); ?></strong>
                    <?php endif; ?>
                </td>
                <td align="right" style="font-size:12px;line-height:18px;">Concierge Telehealth<br>&amp; Clinical Medicine</td>
            </tr></table>
        </td>
    </tr>
    <tr>
        <td style="padding:34px 36px 12px;">
            <p style="margin:0 0 10px;font-size:14px;color:#5b6b7d;">Hello <?php echo esc_html($patient_name); ?>,</p>
            <h1 style="margin:0 0 12px;font-size:25px;line-height:1.3;color:#102b60;">Your patient account has been created</h1>
            <p style="margin:0;font-size:15px;line-height:1.7;color:#526174;">
                A doctor has created a patient account for you at <?php echo esc_html($site_name); ?>. You can now access your patient portal using the secure link below.
            </p>
        </td>
    </tr>
    <tr>
        <td style="padding:20px 36px 8px;">
            <table role="presentation" width="100%" cellspacing="0" cellpadding="0" border="0" style="background:#f4f8fc;border:1px solid #e2ebf4;border-radius:9px;">
                <tr><td style="padding:20px 22px;">
                    <p style="margin:0 0 14px;font-size:12px;letter-spacing:1px;text-transform:uppercase;color:#718198;">Account information</p>
                    <p style="margin:0 0 6px;font-size:13px;color:#718198;">Patient name</p>
                    <p style="margin:0;font-size:16px;font-weight:bold;color:#102b60;"><?php echo esc_html($patient_name); ?></p>
                </td></tr>
            </table>
        </td>
    </tr>
    <tr>
        <td style="padding:18px 36px 8px;">
            <h2 style="margin:0 0 8px;font-size:17px;color:#102b60;">How to access your account</h2>
            <?php if ($activation_url && stripos($activation_url, 'https://') === 0) : ?>
                <p style="margin:0;font-size:14px;line-height:1.7;color:#526174;">Use the secure activation link below to activate your account and complete the setup. The link may expire after a limited time.</p>
            <?php else : ?>
                <p style="margin:0;font-size:14px;line-height:1.7;color:#526174;">Open your patient portal and sign in using the email address provided to the clinic and the password you set. If you have not set a password yet, use the password setup or reset option on the login page.</p>
            <?php endif; ?>
            <p style="margin:12px 0 0;font-size:13px;line-height:1.7;color:#718198;">For your security, your password is not included in this email. Never share your login details with anyone.</p>
        </td>
    </tr>
    <tr>
        <td align="center" style="padding:26px 36px 32px;">
            <a href="<?php echo esc_url($account_access_url); ?>" style="display:inline-block;background:#176dbb;color:#ffffff;text-decoration:none;font-size:15px;font-weight:bold;padding:14px 30px;border-radius:6px;">Access My Account</a>
        </td>
    </tr>
    <tr>
        <td style="background:#f4f8fc;padding:20px 30px;text-align:center;font-size:12px;line-height:1.7;color:#7b8796;">
            © <?php echo esc_html(wp_date('Y')); ?> <?php echo esc_html($site_name); ?>. All rights reserved.<br>
            <a href="<?php echo esc_url($site_url); ?>" style="color:#176dbb;text-decoration:none;">Visit Website</a>
        </td>
    </tr>
</table>
</td></tr></table>
</body>
</html>
