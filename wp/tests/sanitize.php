<?php
//---------------------------------------------------------------------
// Sanitize Tests
//---------------------------------------------------------------------

@require_once(dirname(dirname(dirname(dirname(dirname(__FILE__))))) . '/wp-load.php');
@require_once(dirname(__FILE__) . '/test.php');



$data = array(
	'quEen BjöRk Ⅷ loVes aPplEs.',
	'THE lazY Rex ⅸ eAtS f00d.'
);

\blobfolio\test\cli::record('common_strtolower', array($data[0]), common_strtolower($data[0]));
\blobfolio\test\cli::record('common_strtoupper', array($data[0]), common_strtoupper($data[0]));
\blobfolio\test\cli::record('common_ucwords', array($data[0]), common_ucwords($data[0]));
\blobfolio\test\cli::record('common_ucfirst', array($data[0]), common_ucfirst($data[0]));

\blobfolio\test\cli::record('common_format_money', array(2.2), common_format_money(2.2));

\blobfolio\test\cli::record('common_inflect', array(1, '%d book', '%d books'), common_inflect(1, '%d book', '%d books'));
\blobfolio\test\cli::record('common_inflect', array(2, '%d book', '%d books'), common_inflect(2, '%d book', '%d books'));

$str = "Hey good lookin'!";

\blobfolio\test\cli::record('common_get_excerpt', array($str, 10), common_get_excerpt($str, 10));
\blobfolio\test\cli::record('common_get_excerpt', array($str, 2, null, 'words'), common_get_excerpt($str, 2, null, 'words'));

$path = 'apples\oranges//thing';
\blobfolio\test\cli::record('common_unixslashit', array($path), common_unixslashit($path));
\blobfolio\test\cli::record('common_leadingslashit', array($path), common_leadingslashit($path));

\blobfolio\test\cli::record('common_array_to_indexed', array($data), common_array_to_indexed($data));

\blobfolio\test\cli::record('common_to_range', array(5, 1, 7), common_to_range(5, 1, 7));
\blobfolio\test\cli::record('common_in_range', array(5, 1, 7), common_in_range(5, 1, 7));
\blobfolio\test\cli::record('common_length_in_range', array($str, 1, 30), common_length_in_range($str, 1, 30));

$str = 'Björk Guðmundsdóttir!';
\blobfolio\test\cli::record('common_sanitize_name', array($str), common_sanitize_name($str));

\blobfolio\test\cli::record('common_sanitize_printable', array($str), common_sanitize_printable($str));

\blobfolio\test\cli::record('common_sanitize_csv', array($str), common_sanitize_csv($str));

$str = "Hey!\n\n\nThere!";
\blobfolio\test\cli::record('common_sanitize_newlines', array($str), common_sanitize_newlines($str));
\blobfolio\test\cli::record('common_sanitize_whitespace', array($str), common_sanitize_whitespace($str));

$str = '“T’was the night before Christmas...”';
\blobfolio\test\cli::record('common_sanitize_quotes', array($str), common_sanitize_quotes($str));

\blobfolio\test\cli::record('common_sanitize_js_variable', array($str), common_sanitize_js_variable($str));

\blobfolio\test\cli::record('common_sanitize_email', array('jane@doE.com'), common_sanitize_email('jane@doE.com'));

\blobfolio\test\cli::record('common_sanitize_email', array('jane@localhost'), common_sanitize_email('jane@localhost'));

\blobfolio\test\cli::record('common_sanitize_zip5', array('123'), common_sanitize_zip5('123'));

\blobfolio\test\cli::record('common_sanitize_ip', array('2600:3c00::f03c:91ff:feae:0ff2'), common_sanitize_ip('2600:3c00::f03c:91ff:feae:0ff2'));

\blobfolio\test\cli::record('common_sanitize_number', array('123'), common_sanitize_number('123'));
\blobfolio\test\cli::record('common_sanitize_number', array('12%'), common_sanitize_number('12%'));

\blobfolio\test\cli::record('common_sanitize_bool', array('FALSE'), common_sanitize_bool('FALSE'));
\blobfolio\test\cli::record('common_sanitize_bool', array('1'), common_sanitize_bool('1'));

\blobfolio\test\cli::record('common_sanitize_float', array(1), common_sanitize_float(1));

\blobfolio\test\cli::record('common_sanitize_int', array(2.2), common_sanitize_int(2.2));

\blobfolio\test\cli::record('common_sanitize_string', array(5.5), common_sanitize_string(5.5));

\blobfolio\test\cli::record('common_sanitize_array', array(5.5), common_sanitize_array(5.5));

\blobfolio\test\cli::record('common_sanitize_datetime', array('01/01/2015'), common_sanitize_datetime('01/01/2015'));
\blobfolio\test\cli::record('common_sanitize_date', array('01/01/2015'), common_sanitize_date('01/01/2015'));

\blobfolio\test\cli::record('common_sanitize_domain_name', array('http://google.com'), common_sanitize_domain_name('http://google.com'));

\blobfolio\test\cli::record('common_validate_cc', array('4242424242424242'), common_validate_cc('4242424242424242'));
\blobfolio\test\cli::record('common_validate_cc', array('4242424242424241'), common_validate_cc('4242424242424241'));





\blobfolio\test\cli::print('SANITIZE FUNCTIONS');
?>