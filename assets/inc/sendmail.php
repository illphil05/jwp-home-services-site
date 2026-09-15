<?php

require_once('phpmailer/class.phpmailer.php');
require_once('phpmailer/class.smtp.php');

$mail = new PHPMailer();

// TODO before launch: Jason needs to create a dedicated inbox for estimate
// requests (he doesn't use email personally otherwise) and this whole SMTP
// block needs real credentials for that inbox. Everything below is a
// placeholder so the form fails gracefully instead of leaking template
// vendor credentials.
//$mail->SMTPDebug = 3; // Enable verbose debug output
$mail->isSMTP(); // Set mailer to use SMTP
$mail->Host = 'smtp.example.com'; // TODO: real SMTP host for Jason's estimate inbox
$mail->SMTPAuth = true; // Enable SMTP authentication
$mail->Username = 'estimates@jwphomeservices.com'; // TODO: real SMTP username
$mail->Password = 'CHANGE_ME'; // TODO: real SMTP password
$mail->SMTPSecure = true; // Enable TLS encryption, `ssl` also accepted
$mail->Port = 465; // TCP port to connect to

$message = "";
$status = "false";

if( $_SERVER['REQUEST_METHOD'] == 'POST' ) {
 if( $_POST['form_name'] != '' AND $_POST['form_phone'] != '' AND $_POST['form_message'] != '' ) {

 $name = $_POST['form_name'];
 $phone = $_POST['form_phone'];
 $email = isset($_POST['form_email']) ? $_POST['form_email'] : '';
 $town = isset($_POST['form_town']) ? $_POST['form_town'] : '';
 $service = isset($_POST['form_service']) ? $_POST['form_service'] : '';
 $timing = isset($_POST['form_timing']) ? $_POST['form_timing'] : '';
 $message = $_POST['form_message'];

 $subject = 'New Estimate Request | JWP Home Services';

 $botcheck = $_POST['form_botcheck'];

 $toemail = 'estimates@jwphomeservices.com'; // TODO: replace with Jason's real dedicated inbox once created
 $toname = 'JWP Home Services';

 if( $botcheck == '' ) {

 $replyto = ($email != '') ? $email : 'no-reply@jwphomeservices.com';
 $mail->SetFrom( $replyto , $name );
 $mail->AddReplyTo( $replyto , $name );
 $mail->AddAddress( $toemail , $toname );
 $mail->Subject = $subject;

 $name = isset($name) ? "Name: $name<br><br>" : '';
 $phone = isset($phone) ? "Phone: $phone<br><br>" : '';
 $email = ($email != '') ? "Email: $email<br><br>" : '';
 $town = ($town != '') ? "Town / ZIP: $town<br><br>" : '';
 $service = ($service != '') ? "Service: $service<br><br>" : '';
 $timing = ($timing != '') ? "Timing: $timing<br><br>" : '';
 $message = isset($message) ? "Description: $message<br><br>" : '';

 $referrer = $_SERVER['HTTP_REFERER'] ? '<br><br><br>This Form was submitted from: ' . $_SERVER['HTTP_REFERER'] : '';

 $body = "$name $phone $email $town $service $timing $message $referrer";

 $mail->MsgHTML( $body );
 $sendEmail = $mail->Send();

 if( $sendEmail == true ):
 $message = 'We have <strong>successfully</strong> received your Message and will get Back to you as soon as possible.';
 $status = "true";
 else:
 $message = 'Email <strong>could not</strong> be sent due to some Unexpected Error. Please Try Again later.<br /><br /><strong>Reason:</strong><br />' . $mail->ErrorInfo . '';
 $status = "false";
 endif;
 } else {
 $message = 'Bot <strong>Detected</strong>.! Clean yourself Botster.!';
 $status = "false";
 }
 } else {
 $message = 'Please <strong>Fill up</strong> all the Fields and Try Again.';
 $status = "false";
 }
} else {
 $message = 'An <strong>unexpected error</strong> occured. Please Try Again later.';
 $status = "false";
}

$status_array = array( 'message' => $message, 'status' => $status);
echo json_encode($status_array);
?>