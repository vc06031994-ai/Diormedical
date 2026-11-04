<?php
/**
 * DIOR MEDICAL - Appointment Cancelled Email
 *
 * Expected optional variables:
 * $patient_name, $doctor_name, $doctor_specialty, $appointment_date,
 * $appointment_time, $appointment_timezone, $appointment_type,
 * $appointment_reference, $appointment_status, $cancellation_reason,
 * $book_appointment_url, $site_url, $site_name.
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

$patient_name          = isset( $patient_name ) ? (string) $patient_name : 'Patient';
$doctor_name           = isset( $doctor_name ) ? (string) $doctor_name : 'Attending Physician';
$doctor_specialty      = isset( $doctor_specialty ) ? (string) $doctor_specialty : '';
$appointment_date      = isset( $appointment_date ) ? (string) $appointment_date : '';
$appointment_time      = isset( $appointment_time ) ? (string) $appointment_time : '';
$appointment_timezone  = isset( $appointment_timezone ) ? (string) $appointment_timezone : '';
$appointment_type      = isset( $appointment_type ) ? (string) $appointment_type : 'Consultation';
$appointment_reference = isset( $appointment_reference ) ? (string) $appointment_reference : '';
$appointment_status    = isset( $appointment_status ) ? (string) $appointment_status : 'Cancelled';
$cancellation_reason   = isset( $cancellation_reason ) ? trim( (string) $cancellation_reason ) : '';
$book_appointment_url  = isset( $book_appointment_url ) ? (string) $book_appointment_url : home_url( '/patient-dashboard/#tab=appointments' );
$site_url              = isset( $site_url ) ? (string) $site_url : home_url( '/' );
$site_name             = isset( $site_name ) ? (string) $site_name : 'DIOR MEDICAL';
$year                  = wp_date( 'Y' );

$logo_id  = (int) get_theme_mod( 'custom_logo' );
$logo_url = $logo_id ? wp_get_attachment_image_url( $logo_id, 'full' ) : '';

$appointment_datetime = trim( $appointment_date . ( $appointment_time ? ' · ' . $appointment_time : '' ) );
if ( $appointment_datetime === '' ) {
    $appointment_datetime = 'Not available';
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="color-scheme" content="light">
    <title><?php echo esc_html__( 'Appointment Cancelled | DIOR MEDICAL', 'dior-medical-dashboard' ); ?></title>
</head>
<body style="margin:0;padding:0;background-color:#f4f8fc;font-family:Arial,Helvetica,sans-serif;color:#102b60;">
    <div style="display:none;font-size:1px;line-height:1px;max-height:0;max-width:0;opacity:0;overflow:hidden;">
        Your DIOR MEDICAL appointment has been cancelled. Review the details and book another appointment when you're ready.
    </div>

    <table role="presentation" width="100%" cellpadding="0" cellspacing="0" border="0" style="width:100%;background-color:#f4f8fc;">
        <tr>
            <td align="center" style="padding:17px 10px 24px;">
                <table role="presentation" width="904" cellpadding="0" cellspacing="0" border="0" style="width:100%;max-width:904px;background-color:#ffffff;border:1px solid #e2ebf4;border-collapse:collapse;">
                    <!-- Brand header: matches the approved Welcome Email -->
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

                    <!-- Main message -->
                    <tr>
                        <td style="padding:47px 57px 35px;">
                            <p style="margin:0 0 13px;font-size:13px;line-height:1.4;letter-spacing:2.5px;font-weight:700;color:#176dbb;">APPOINTMENT UPDATE</p>
                            <h1 style="margin:0 0 25px;font-family:Arial,Helvetica,sans-serif;font-size:42px;line-height:1.2;font-weight:700;letter-spacing:-1.2px;color:#102b60;">
                                Your Appointment Has Been Cancelled
                            </h1>
                            <p style="margin:0 0 20px;font-size:25px;line-height:1.5;color:#17366f;">
                                Dear <?php echo esc_html( $patient_name ); ?>,
                            </p>
                            <p style="margin:0 0 28px;font-size:24px;line-height:1.5;color:#17366f;">
                                This email confirms that your appointment with <strong style="color:#176dbb;"><?php echo esc_html( $site_name ); ?></strong> has been cancelled. Please review the appointment information below.
                            </p>

                            <!-- Cancellation status -->
                            <table role="presentation" width="100%" cellpadding="0" cellspacing="0" border="0" style="margin:0 0 25px;background-color:#eef6fd;border:1px solid #d8e9f8;border-radius:8px;">
                                <tr>
                                    <td width="86" align="center" valign="middle" style="padding:17px 10px;">
                                        <table role="presentation" width="58" height="58" cellpadding="0" cellspacing="0" border="0" style="width:58px;height:58px;background-color:#dceeff;border-radius:50%;">
                                            <tr><td align="center" valign="middle" style="font-family:Arial,Helvetica,sans-serif;font-size:31px;line-height:1;color:#176dbb;">&#10005;</td></tr>
                                        </table>
                                    </td>
                                    <td valign="middle" style="padding:15px 18px 15px 3px;">
                                        <p style="margin:0 0 5px;font-size:12px;line-height:1.4;letter-spacing:1.5px;font-weight:700;color:#176dbb;">APPOINTMENT STATUS</p>
                                        <p style="margin:0;font-size:22px;line-height:1.35;font-weight:700;color:#102b60;"><?php echo esc_html( $appointment_status ); ?></p>
                                        <p style="margin:3px 0 0;font-size:15px;line-height:1.5;color:#536f96;">The appointment is no longer scheduled.</p>
                                    </td>
                                </tr>
                            </table>

                            <p style="margin:0 0 17px;font-size:24px;line-height:1.45;color:#17366f;">Appointment details</p>

                            <!-- Appointment details card -->
                            <table role="presentation" width="100%" cellpadding="0" cellspacing="0" border="0" style="background-color:#f7fbff;border:1px solid #dfebf7;border-radius:7px;">
                                <tr>
                                    <td width="86" align="center" valign="middle" style="padding:16px 8px 13px 14px;">
                                        <table role="presentation" width="58" height="58" cellpadding="0" cellspacing="0" border="0" style="width:58px;height:58px;background-color:#eaf4fd;border-radius:50%;"><tr><td align="center" valign="middle" style="font-size:28px;color:#0870c8;">&#128100;</td></tr></table>
                                    </td>
                                    <td valign="middle" style="padding:15px 17px 13px 4px;border-bottom:1px solid #dce9f6;">
                                        <p style="margin:0 0 5px;font-size:12px;line-height:1.4;letter-spacing:1.3px;font-weight:700;color:#31527f;">PATIENT</p>
                                        <p style="margin:0;font-size:21px;line-height:1.4;font-weight:700;color:#17366f;"><?php echo esc_html( $patient_name ); ?></p>
                                    </td>
                                </tr>
                                <tr>
                                    <td width="86" align="center" valign="middle" style="padding:13px 8px 13px 14px;">
                                        <table role="presentation" width="58" height="58" cellpadding="0" cellspacing="0" border="0" style="width:58px;height:58px;background-color:#eaf4fd;border-radius:50%;"><tr><td align="center" valign="middle" style="font-size:28px;color:#0870c8;">&#9877;</td></tr></table>
                                    </td>
                                    <td valign="middle" style="padding:13px 17px 13px 4px;border-bottom:1px solid #dce9f6;">
                                        <p style="margin:0 0 5px;font-size:12px;line-height:1.4;letter-spacing:1.3px;font-weight:700;color:#31527f;">DOCTOR</p>
                                        <p style="margin:0;font-size:21px;line-height:1.4;font-weight:700;color:#17366f;"><?php echo esc_html( $doctor_name ); ?></p>
                                        <?php if ( $doctor_specialty !== '' ) : ?><p style="margin:3px 0 0;font-size:15px;line-height:1.4;color:#6680a2;"><?php echo esc_html( $doctor_specialty ); ?></p><?php endif; ?>
                                    </td>
                                </tr>
                                <tr>
                                    <td width="86" align="center" valign="middle" style="padding:13px 8px 13px 14px;">
                                        <table role="presentation" width="58" height="58" cellpadding="0" cellspacing="0" border="0" style="width:58px;height:58px;background-color:#eaf4fd;border-radius:50%;"><tr><td align="center" valign="middle" style="font-size:27px;color:#0870c8;">&#128467;</td></tr></table>
                                    </td>
                                    <td valign="middle" style="padding:13px 17px 13px 4px;border-bottom:1px solid #dce9f6;">
                                        <p style="margin:0 0 5px;font-size:12px;line-height:1.4;letter-spacing:1.3px;font-weight:700;color:#31527f;">APPOINTMENT DATE &amp; TIME</p>
                                        <p style="margin:0;font-size:21px;line-height:1.4;font-weight:700;color:#17366f;"><?php echo esc_html( $appointment_datetime ); ?></p>
                                        <?php if ( $appointment_timezone !== '' ) : ?><p style="margin:3px 0 0;font-size:14px;line-height:1.4;color:#6680a2;"><?php echo esc_html( $appointment_timezone ); ?></p><?php endif; ?>
                                    </td>
                                </tr>
                                <tr>
                                    <td width="86" align="center" valign="middle" style="padding:13px 8px 13px 14px;">
                                        <table role="presentation" width="58" height="58" cellpadding="0" cellspacing="0" border="0" style="width:58px;height:58px;background-color:#eaf4fd;border-radius:50%;"><tr><td align="center" valign="middle" style="font-size:27px;color:#0870c8;">&#128 video;#128249;</td></tr></table>
                                    </td>
                                    <td valign="middle" style="padding:13px 17px 13px 4px;<?php echo $appointment_reference !== '' ? 'border-bottom:1px solid #dce9f6;' : ''; ?>">
                                        <p style="margin:0 0 5px;font-size:12px;line-height:1.4;letter-spacing:1.3px;font-weight:700;color:#31527f;">APPOINTMENT TYPE</p>
                                        <p style="margin:0;font-size:20px;line-height:1.4;font-weight:700;color:#17366f;"><?php echo esc_html( $appointment_type ); ?></p>
                                    </td>
                                </tr>
                                <?php if ( $appointment_reference !== '' ) : ?>
                                <tr>
                                    <td width="86" align="center" valign="middle" style="padding:13px 8px 15px 14px;">
                                        <table role="presentation" width="58" height="58" cellpadding="0" cellspacing="0" border="0" style="width:58px;height:58px;background-color:#eaf4fd;border-radius:50%;"><tr><td align="center" valign="middle" style="font-size:27px;color:#0870c8;">&#35;</td></tr></table>
                                    </td>
                                    <td valign="middle" style="padding:13px 17px 15px 4px;">
                                        <p style="margin:0 0 5px;font-size:12px;line-height:1.4;letter-spacing:1.3px;font-weight:700;color:#31527f;">REFERENCE ID</p>
                                        <p style="margin:0;font-size:19px;line-height:1.4;font-weight:700;color:#17366f;"><?php echo esc_html( $appointment_reference ); ?></p>
                                    </td>
                                </tr>
                                <?php endif; ?>
                            </table>

                            <?php if ( $cancellation_reason !== '' ) : ?>
                            <!-- Optional cancellation reason: omitted when no reason was supplied -->
                            <table role="presentation" width="100%" cellpadding="0" cellspacing="0" border="0" style="margin:24px 0 0;background-color:#eef6fd;border:1px solid #d8e9f8;border-radius:8px;">
                                <tr>
                                    <td width="78" align="center" valign="top" style="padding:18px 8px 18px 12px;">
                                        <table role="presentation" width="54" height="54" cellpadding="0" cellspacing="0" border="0" style="width:54px;height:54px;background-color:#dceeff;border-radius:50%;"><tr><td align="center" valign="middle" style="font-size:26px;color:#0870c8;">&#9432;</td></tr></table>
                                    </td>
                                    <td valign="middle" style="padding:17px 17px 17px 3px;">
                                        <p style="margin:0 0 7px;font-size:12px;line-height:1.4;letter-spacing:1.3px;font-weight:700;color:#31527f;">CANCELLATION REASON</p>
                                        <p style="margin:0;font-size:19px;line-height:1.5;color:#17366f;"><?php echo esc_html( $cancellation_reason ); ?></p>
                                    </td>
                                </tr>
                            </table>
                            <?php endif; ?>

                            <!-- Rebooking instructions -->
                            <p style="margin:30px 0 12px;font-size:24px;line-height:1.4;color:#17366f;">Need another appointment?</p>
                            <p style="margin:0 0 34px;font-size:21px;line-height:1.5;color:#17366f;">
                                You can book a new appointment at a time that works for you. Sign in to your patient portal and choose an available appointment slot.
                            </p>

                            <!-- Primary CTA -->
                            <table role="presentation" align="center" cellpadding="0" cellspacing="0" border="0" style="margin:0 auto;">
                                <tr>
                                    <td align="center" bgcolor="#176dbb" style="background-color:#176dbb;border-radius:11px;">
                                        <a href="<?php echo esc_url( $book_appointment_url ); ?>" target="_blank" style="display:inline-block;padding:23px 48px;font-family:Arial,Helvetica,sans-serif;font-size:24px;line-height:1.35;font-weight:700;color:#ffffff;text-decoration:none;border-radius:11px;">
                                            Book Another Appointment &nbsp;&#8594;
                                        </a>
                                    </td>
                                </tr>
                            </table>
                        </td>
                    </tr>

                    <!-- Footer: matches the approved Welcome Email -->
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
