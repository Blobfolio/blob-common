<?php
//---------------------------------------------------------------------
// Spacetime Tests
//---------------------------------------------------------------------

@require_once(dirname(dirname(dirname(dirname(dirname(__FILE__))))) . '/wp-load.php');
@require_once(dirname(__FILE__) . '/test.php');



\blobfolio\test\cli::record('common_get_us_states', array(), common_get_us_states());

\blobfolio\test\cli::record('common_get_ca_provinces', array(), common_get_ca_provinces());

\blobfolio\test\cli::record('common_get_countries', array(), common_get_countries());

$img = ABSPATH . '/wp-includes/images/rss.png';
\blobfolio\test\cli::record('common_get_data_uri', array($img), common_get_data_uri($img));

\blobfolio\test\cli::record('common_get_mime_type', array($img), common_get_mime_type($img));

$ip = '2600:3c00::f03c:91ff:feae:0ff2';
\blobfolio\test\cli::record('common_ip_to_number', array($ip), common_ip_to_number($ip));

\blobfolio\test\cli::record('common_cidr_to_range', array("$ip/64"), common_cidr_to_range("$ip/64"));

\blobfolio\test\cli::record('common_get_url_by_path', array($img), common_get_url_by_path($img));
$url = common_get_url_by_path($img);

\blobfolio\test\cli::record('common_get_path_by_url', array($url), common_get_path_by_url($url));

\blobfolio\test\cli::record('common_is_empty_dir', array(ABSPATH), common_is_empty_dir(ABSPATH));

\blobfolio\test\cli::record('common_get_site_hostname', array(), common_get_site_hostname());

\blobfolio\test\cli::record('common_upload_path', array('pics'), common_upload_path('pics'));

\blobfolio\test\cli::record('common_theme_path', array('pics'), common_theme_path('pics'));

\blobfolio\test\cli::record('common_datediff', array('2015-01-01','2015-02-01'), common_datediff('2015-01-01', '2015-02-01'));

\blobfolio\test\cli::record('common_get_blog_timezone', array(), common_get_blog_timezone());

$date = '2015-01-01 10:00:00';
\blobfolio\test\cli::record('common_to_blogtime', array($date), common_to_blogtime($date));

\blobfolio\test\cli::record('common_from_blogtime', array($date), common_from_blogtime($date));

\blobfolio\test\cli::print('SPACETIME FUNCTIONS');
?>