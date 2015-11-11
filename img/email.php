<?php


function mail_message($data_array, $template_file, $deadline_str) {


// Grabs the user's data and find-and-replaces it into the template
$email_message = file_get_contents($template_file);
$email_message = str_replace("#DEADLINE#", $deadline_str, $email_message);


$email_message = str_replace("#WHOAMI#", $data_array['whoami'], $email_message);
$email_message = str_replace("#DATE#", date("F d, Y h:i a"), $email_message);
$email_message = str_replace("#NAME#", $data_array['name'], $email_message);
$email_message = str_replace("#EMAIL#", $data_array['email'], $email_message);
$email_message = str_replace("#IP#", $_SERVER['HTTP_X_FORWARDED_FOR'], $email_message);
$email_message = str_replace("#AGENT#", $_SERVER['HTTP_USER_AGENT'], $email_message);
$email_message = str_replace("#SUBJECT#", $data_array['subject'], $email_message);
$email_message = str_replace("#MESSAGE#", $data_array['message'], $email_message);
$email_message = str_replace("#FOUND#", $data_array['found'], $email_message);

#include whether or not to contact the customer with offers in the future
$contact = "";
if (isset($data_array['update1'])) {
$contact = $contact." Please email updates about your products.<br/>";
}
if (isset($data_array['update2'])) {
$contact = $contact." Please email updates about products from third-party partners.<br/>";
}
$email_message = str_replace("#CONTACT#", $contact, $email_message);


#construct the email headers
$to = $_SESSION['email'];  //for testing purposes, this should be YOUR email address.
$from = $data_array['email'];
$email_subject = "CONTACT #".time().": ".$data_array['subject'];

$headers  = "From: " . $from . "\r\n";
$headers .= 'MIME-Version: 1.0' . "\n";  //these headers will allow our HTML tags to be displayed in the email
$headers .= 'Content-type: text/html; charset=iso-8859-1' . "\r\n";

#now mail
mail($to, $email_subject, $email_message, $headers);

}