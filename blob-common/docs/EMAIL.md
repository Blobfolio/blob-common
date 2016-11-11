# Reference: Email Functions

This guide documents functions which extend email capability. The code can be located in `functions-email.php`.



##### Table of Contents

 * [common_mail()](#common_mail)



## common_mail()

This is a wrapper function allowing you to send HTML email through `wp_mail()`. The arguments are the same as the main function.

#### Arguments

 * (*string|array*) Recipient email
 * (*string*) Subject
 * (*string*) Message
 * (*string|array*) (*optional*) Headers
 * (*string|array*) (*optional*) Attachments

#### Return

This function always returns `TRUE`.