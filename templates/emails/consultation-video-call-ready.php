<?php
/**
 * DIOR MEDICAL - Consultation / Video Call Ready Email
 *
 * Patient-facing notification.
 *
 * Expected optional variables:
 * $recipient_role ('patient'), $recipient_is_authorized (boolean),
 * $patient_name, $doctor_name, $doctor_specialty,
 * $appointment_date, $appointment_time, $appointment_timezone,
 * $appointment_type, $telehealth_join_url, $patient_dashboard_url,
 * $site_url, $site_name.
 *
 * SECURITY:
 * - This template is intended for the patient recipient only.
 * - A direct video URL is rendered only when the caller explicitly sets
 *   $recipient_is_authorized = true and supplies a valid HTTPS URL.
 * - Otherwise the CTA goes to the patient dashboard, where access must be
 *   checked again server-side before allowing the consultation to be joined.
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

$recipient_role = isset( $recipient_role ) ? strtolower( (string) $recipient_role ) : 'patient';
$recipient_role = in_array( $recipient_role, array( 'patient', 'doctor' ), true ) ? $recipient_role : 'patient';

$recipient_is_authorized = isset( $recipient_is_authorized ) && true === $recipient_is_authorized;
$patient_name = isset( $patient_name ) ? (string) $patient_name : 'Patient';
$doctor_name  = isset( $doctor_name ) ? (string) $doctor_name : 'Attending Physician';
$doctor_specialty = isset( $doctor_specialty ) ? (string) $doctor_specialty : '';

$appointment_date = isset( $appointment_date ) ? (string) $appointment_date : '';
$appointment_time = isset( $appointment_time ) ? (string) $appointment_time : '';
$appointment_timezone = isset( $appointment_timezone ) ? (string) $appointment_timezone : '';
$appointment_type = isset( $appointment_type ) ? (string) $appointment_type : 'Video Consultation';

$telehealth_join_url = isset( $telehealth_join_url ) ? esc_url_raw( (string) $telehealth_join_url ) : '';
$patient_dashboard_url = isset( $patient_dashboard_url )
    ? (string) $patient_dashboard_url
    : home_url( '/patient-dashboard/#tab=appointments' );

$site_url  = isset( $site_url ) ? (string) $site_url : home_url( '/' );
$site_name = isset( $site_name ) ? (string) $site_name : 'DIOR MEDICAL';
$year      = wp_date( 'Y' );

$logo_id  = (int) get_theme_mod( 'custom_logo' );
$logo_url = $logo_id ? wp_get_attachment_image_url( $logo_id, 'full' ) : '';

$appointment_datetime = trim( $appointment_date . ( $appointment_time !== '' ? ' · ' . $appointment_time : '' ) );
if ( $appointment_datetime === '' ) {
    $appointment_datetime = 'Please check your patient dashboard for the appointment time.';
}

// Only patient recipients may receive a direct meeting link.
// Require explicit authorization from the sending workflow and HTTPS.
$can_send_direct_join_link = (
    'patient' === $recipient_role
    && $recipient_is_authorized
    && $telehealth_join_url !== ''
    && 0 === stripos( $telehealth_join_url, 'https://' )
);

$join_destination = $can_send_direct_join_link ? $telehealth_join_url : $patient_dashboard_url;
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="color-scheme" content="light">
    <title><?php echo esc_html__( 'Consultation Ready | DIOR MEDICAL', 'dior-medical-dashboard' ); ?></title>
</head>
<body style="margin:0;padding:0;background-color:#f4f8fc;font-family:Arial,Helvetica,sans-serif;color:#102b60;">
    <div style="display:none;font-size:1px;line-height:1px;max-height:0;max-width:0;opacity:0;overflow:hidden;">
        Your DIOR MEDICAL video consultation is ready. Review your appointment details and join securely.
    </div>

    <table role="presentation" width="100%" cellpadding="0" cellspacing="0" border="0" style="width:100%;background-color:#f4f8fc;">
        <tr>
            <td align="center" style="padding:17px 10px 24px;">
                <table role="presentation" width="904" cellpadding="0" cellspacing="0" border="0" style="width:100%;max-width:904px;background-color:#ffffff;border:1px solid #e2ebf4;border-collapse:collapse;">

                    <!-- Same approved DIOR MEDICAL header -->
                    <tr>
                        <td style="padding:22px 45px;background-color:#2069ad;">
                            <table role="presentation" width="100%" cellpadding="0" cellspacing="0" border="0">
                                <tr>
                                    <td width="29%" valign="middle" style="padding:0 28px 0 0;">
                                        <a href="<?php echo esc_url( $site_url ); ?>" style="text-decoration:none;">
                                            <?php if ( $logo_url ) : ?>
                                                <img src="<?php echo esc_url( $logo_url ); ?>" width="215" alt="<?php echo esc_attr( $site_name ); ?>" style="display:block;width:100%;max-width:215px;height:auto;border:0;outline:none;text-decoration:none;">
                                            <?php else : ?>
                                                <span style="font-size:30px;line-height:1.2;font-weight:bold;color:#ffffff;"><?php echo esc_html( $site_name ); ?></span>
                                            <?php endif; ?>
                                        </a>
                                    </td>
                                    <td width="71%" valign="middle" style="padding-left:35px;border-left:1px solid #80add4;color:#ffffff;font-size:21px;line-height:1.5;font-weight:normal;">
                                        Concierge Telehealth &amp;<br>Clinical Medicine
                                    </td>
                                </tr>
                            </table>
                        </td>
                    </tr>

                    <!-- Main content -->
                    <tr>
                        <td style="padding:47px 57px 35px;">
                            <p style="margin:0 0 13px;font-size:13px;line-height:1.4;letter-spacing:2.5px;font-weight:700;color:#176dbb;">CONSULTATION READY</p>
                            <h1 style="margin:0 0 25px;font-family:Arial,Helvetica,sans-serif;font-size:42px;line-height:1.2;font-weight:700;letter-spacing:-1.2px;color:#102b60;">
                                Your Video Consultation Is Ready
                            </h1>
                            <p style="margin:0 0 20px;font-size:25px;line-height:1.5;color:#17366f;">
                                Dear <?php echo esc_html( $patient_name ); ?>,
                            </p>
                            <p style="margin:0 0 30px;font-size:24px;line-height:1.5;color:#17366f;">
                                Your DIOR MEDICAL consultation is ready. You can join your scheduled video consultation securely using the button below.
                            </p>

                            <!-- Ready status -->
                            <table role="presentation" width="100%" cellpadding="0" cellspacing="0" border="0" style="margin:0 0 28px;background-color:#eef6fd;border:1px solid #d8e9f8;border-radius:8px;">
                                <tr>
                                    <td width="94" align="center" valign="middle" style="padding:20px 10px;">
                                        <table role="presentation" width="66" height="66" cellpadding="0" cellspacing="0" border="0" style="width:66px;height:66px;background-color:#dceeff;border-radius:50%;">
                                            <tr><td align="center" valign="middle" style="font-family:Arial,Helvetica,sans-serif;font-size:31px;line-height:1;color:#176dbb;">&#10003;</td></tr>
                                        </table>
                                    </td>
                                    <td valign="middle" style="padding:18px 18px 18px 3px;">
                                        <p style="margin:0 0 6px;font-size:12px;line-height:1.4;letter-spacing:1.5px;font-weight:700;color:#176dbb;">CONSULTATION STATUS</p>
                                        <p style="margin:0;font-size:23px;line-height:1.45;font-weight:700;color:#102b60;">Ready to Join</p>
                                        <p style="margin:4px 0 0;font-size:15px;line-height:1.4;color:#6680a2;">Your secure consultation access is available.</p>
                                    </td>
                                </tr>
                            </table>

                            <p style="margin:0 0 17px;font-size:24px;line-height:1.45;color:#17366f;">Appointment details</p>

                            <table role="presentation" width="100%" cellpadding="0" cellspacing="0" border="0" style="background-color:#f7fbff;border:1px solid #dfebf7;border-radius:7px;">
                                <tr>
                                    <td width="86" align="center" valign="middle" style="padding:16px 8px 14px 14px;">
                                        <table role="presentation" width="58" height="58" cellpadding="0" cellspacing="0" border="0" style="width:58px;height:58px;background-color:#eaf4fd;border-radius:50%;"><tr><td align="center" valign="middle" style="font-size:28px;color:#0870c8;">&#128100;</td></tr></table>
                                    </td>
                                    <td valign="middle" style="padding:15px 17px 14px 4px;border-bottom:1px solid #dce9f6;">
                                        <p style="margin:0 0 5px;font-size:12px;line-height:1.4;letter-spacing:1.3px;font-weight:700;color:#31527f;">DOCTOR NAME</p>
                                        <p style="margin:0;font-size:21px;line-height:1.4;font-weight:700;color:#17366f;"><?php echo esc_html( $doctor_name ); ?></p>
                                        <?php if ( $doctor_specialty !== '' ) : ?><p style="margin:3px 0 0;font-size:15px;line-height:1.4;color:#6680a2;"><?php echo esc_html( $doctor_specialty ); ?></p><?php endif; ?>
                                    </td>
                                </tr>
                                <tr>
                                    <td width="86" align="center" valign="middle" style="padding:14px 8px 16px 14px;">
                                        <table role="presentation" width="58" height="58" cellpadding="0" cellspacing="0" border="0" style="width:58px;height:58px;background-color:#eaf4fd;border-radius:50%;"><tr><td align="center" valign="middle" style="font-size:27px;color:#0870c8;">&#128197;</td></tr></table>
                                    </td>
                                    <td valign="middle" style="padding:14px 17px 16px 4px;">
                                        <p style="margin:0 0 5px;font-size:12px;line-height:1.4;letter-spacing:1.3px;font-weight:700;color:#31527f;">APPOINTMENT DATE &amp; TIME</p>
                                        <p style="margin:0;font-size:21px;line-height:1.45;font-weight:700;color:#17366f;"><?php echo esc_html( $appointment_datetime ); ?></p>
                                        <?php if ( $appointment_timezone !== '' ) : ?><p style="margin:3px 0 0;font-size:14px;line-height:1.4;color:#6680a2;"><?php echo esc_html( $appointment_timezone ); ?></p><?php endif; ?>
                                    </td>
                                </tr>
                                <tr>
                                    <td width="86" align="center" valign="middle" style="padding:14px 8px 16px 14px;">
                                        <table role="presentation" width="58" height="58" cellpadding="0" cellspacing="0" border="0" style="width:58px;height:58px;background-color:#eaf4fd;border-radius:50%;"><tr><td align="center" valign="middle" style="font-size:27px;color:#0870c8;">&#128249;</td></tr></table>
                                    </td>
                                    <td valign="middle" style="padding:14px 17px 16px 4px;">
                                        <p style="margin:0 0 5px;font-size:12px;line-height:1.4;letter-spacing:1.3px;font-weight:700;color:#31527f;">APPOINTMENT TYPE</p>
                                        <p style="margin:0;font-size:20px;line-height:1.4;font-weight:700;color:#17366f;"><?php echo esc_html( $appointment_type ); ?></p>
                                    </td>
                                </tr>
                            </table>

                            <!-- Secure join instructions -->
                            <table role="presentation" width="100%" cellpadding="0" cellspacing="0" border="0" style="margin:22px 0 0;background-color:#eff7ff;border:1px solid #dceafb;border-radius:7px;">
                                <tr>
                                    <td width="86" align="center" valign="middle" style="padding:16px 8px 16px 14px;">
                                        <table role="presentation" width="58" height="58" cellpadding="0" cellspacing="0" border="0" style="width:58px;height:58px;background-color:#eaf4fd;border-radius:50%;"><tr><td align="center" valign="middle" style="font-size:27px;color:#0870c8;">&#128272;</td></tr></table>
                                    </td>
                                    <td valign="middle" style="padding:15px 17px 15px 4px;">
                                        <p style="margin:0 0 6px;font-size:12px;line-height:1.4;letter-spacing:1.3px;font-weight:700;color:#176dbb;">SECURE VIDEO CONSULTATION</p>
                                        <?php if ( $can_send_direct_join_link ) : ?>
                                            <p style="margin:0;font-size:16px;line-height:1.6;color:#17366f;">Your secure video consultation link is ready. Use the Join Consultation button below. Please do not share the link with anyone else.</p>
                                        <?php else : ?>
                                            <p style="margin:0;font-size:16px;line-height:1.6;color:#17366f;">Sign in to your patient dashboard to access your consultation. The portal will verify your access before allowing you to join.</p>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                            </table>

                            <p style="margin:32px 0 12px;font-size:24px;line-height:1.4;color:#17366f;">Before you join</p>
                            <p style="margin:0 0 35px;font-size:21px;line-height:1.5;color:#17366f;">
                                Please make sure you have a stable internet connection and that your camera and microphone are ready for your consultation.
                            </p>

                            <!-- CTA -->
                            <table role="presentation" align="center" cellpadding="0" cellspacing="0" border="0" style="margin:0 auto;">
                                <tr>
                                    <td align="center" bgcolor="#176dbb" style="background-color:#176dbb;border-radius:11px;">
                                        <a href="<?php echo esc_url( $join_destination ); ?>" target="_blank" rel="noopener noreferrer" style="display:inline-block;padding:23px 48px;font-family:Arial,Helvetica,sans-serif;font-size:24px;line-height:1.35;font-weight:700;color:#ffffff;text-decoration:none;border-radius:11px;">
                                            Join Consultation &nbsp;&#8594;
                                        </a>
                                    </td>
                                </tr>
                            </table>
                        </td>
                    </tr>

                    <!-- Same approved footer -->
                    <tr>
                        <td align="center" style="padding:29px 20px 31px;border-top:1px solid #d5e1ed;background-color:#ffffff;">
                            <p style="margin:0;font-size:18px;line-height:1.6;color:#6684b3;">
                                &copy; <?php echo esc_html( $year ); ?> <?php echo esc_html( $site_name ); ?>. All rights reserved.
                                &nbsp;&bull;&nbsp;
                                <a href="<?php echo esc_url( $site_url ); ?>" style="color:#075fc1;text-decoration:underline;">Visit Website</a>
                            </p>
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>
</body>
</html>
