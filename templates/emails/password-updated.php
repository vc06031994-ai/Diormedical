<?php
/**
 * Dior Medical — Password Updated Email (Patient / Doctor)
 *
 * Expected variables:
 * $recipient_name, $recipient_role ('patient'|'doctor'),
 * $password_changed_at_display (optional), $dashboard_url,
 * $support_url, $site_name, $site_url.
 *
 * Security: never include, log, or email the new password or password hash.
 * Trigger only after the password update has succeeded.
 */
if (!defined('ABSPATH')) { exit; }

$recipient_name = isset($recipient_name) ? sanitize_text_field($recipient_name) : 'there';
$recipient_role = isset($recipient_role) ? strtolower(sanitize_key($recipient_role)) : 'patient';
if (!in_array($recipient_role, ['patient', 'doctor'], true)) {
    $recipient_role = 'patient';
}
$password_changed_at_display = isset($password_changed_at_display)
    ? sanitize_text_field($password_changed_at_display)
    : '';
$site_name = isset($site_name) ? sanitize_text_field($site_name) : get_bloginfo('name');
$site_url = isset($site_url) && filter_var($site_url, FILTER_VALIDATE_URL) ? $site_url : home_url('/');
$dashboard_url = isset($dashboard_url) && filter_var($dashboard_url, FILTER_VALIDATE_URL)
    ? $dashboard_url
    : ($recipient_role === 'doctor' ? home_url('/doctor-dashboard/') : home_url('/patient-dashboard/'));
$support_url = isset($support_url) && filter_var($support_url, FILTER_VALIDATE_URL)
    ? $support_url
    : home_url('/contact/');

$logo_id = get_theme_mod('custom_logo');
$logo_url = $logo_id ? wp_get_attachment_image_url($logo_id, 'full') : '';
?>
<!doctype html>
<html>
<head><meta charset="utf-8"><meta name="viewport" content="width=device-width, initial-scale=1"><title>Password Updated</title></head>
<body style="margin:0;padding:24px 12px;background:#f4f8fc;font-family:Arial,Helvetica,sans-serif;color:#24364b;">
<table role="presentation" width="100%" cellspacing="0" cellpadding="0" border="0"><tr><td align="center">
<table role="presentation" width="600" cellspacing="0" cellpadding="0" border="0" style="width:100%;max-width:600px;background:#ffffff;border-radius:12px;overflow:hidden;">
<tr><td style="background:#2069ad;padding:24px 32px;color:#ffffff;">
<table role="presentation" width="100%" cellspacing="0" cellpadding="0" border="0"><tr>
<td><?php if ($logo_url) : ?><img src="<?php echo esc_url($logo_url); ?>" alt="<?php echo esc_attr($site_name); ?>" style="max-width:145px;max-height:48px;display:block;"><?php else : ?><strong style="font-size:20px;letter-spacing:.4px;"><?php echo esc_html($site_name); ?></strong><?php endif; ?></td>
<td align="right" style="font-size:12px;line-height:18px;">Concierge Telehealth<br>&amp; Clinical Medicine</td>
</tr></table></td></tr>
<tr><td style="padding:34px 36px 12px;">
<p style="margin:0 0 10px;font-size:14px;color:#5b6b7d;">Hello <?php echo esc_html($recipient_name); ?>,</p>
<h1 style="margin:0 0 12px;font-size:25px;line-height:1.3;color:#102b60;">Your password has been updated</h1>
<p style="margin:0;font-size:15px;line-height:1.7;color:#526174;">This is a confirmation that the password for your <?php echo esc_html($recipient_role); ?> account at <?php echo esc_html($site_name); ?> was changed successfully.</p>
</td></tr>
<?php if ($password_changed_at_display !== '') : ?>
<tr><td style="padding:20px 36px 8px;"><table role="presentation" width="100%" cellspacing="0" cellpadding="0" style="background:#f4f8fc;border:1px solid #e2ebf4;border-radius:9px;"><tr><td style="padding:18px 22px;">
<p style="margin:0 0 6px;font-size:12px;letter-spacing:1px;text-transform:uppercase;color:#718198;">Password change time</p>
<p style="margin:0;font-size:16px;font-weight:bold;color:#102b60;"><?php echo esc_html($password_changed_at_display); ?></p>
</td></tr></table></td></tr>
<?php endif; ?>
<tr><td style="padding:18px 36px 8px;">
<h2 style="margin:0 0 8px;font-size:17px;color:#102b60;">Didn't make this change?</h2>
<p style="margin:0;font-size:14px;line-height:1.7;color:#526174;">If you did not update your password, contact our support team immediately so we can help secure your account. Do not share your password or verification codes with anyone.</p>
</td></tr>
<tr><td align="center" style="padding:24px 36px 12px;">
<a href="<?php echo esc_url($dashboard_url); ?>" style="display:inline-block;background:#176dbb;color:#ffffff;text-decoration:none;font-size:15px;font-weight:bold;padding:14px 28px;border-radius:6px;">Open Dashboard</a>
</td></tr>
<tr><td align="center" style="padding:8px 36px 32px;">
<a href="<?php echo esc_url($support_url); ?>" style="font-size:14px;font-weight:bold;color:#176dbb;text-decoration:underline;">Contact Support</a>
</td></tr>
<tr><td style="background:#f4f8fc;padding:20px 30px;text-align:center;font-size:12px;line-height:1.7;color:#7b8796;">© <?php echo esc_html(wp_date('Y')); ?> <?php echo esc_html($site_name); ?>. All rights reserved.<br><a href="<?php echo esc_url($site_url); ?>" style="color:#176dbb;text-decoration:none;">Visit Website</a></td></tr>
</table></td></tr></table>
</body></html>
