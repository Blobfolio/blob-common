<?php
//---------------------------------------------------------------------
// CSS Test
//---------------------------------------------------------------------

@require_once(dirname(dirname(__FILE__)) . '/lib/vendor/autoload.php');

$css = dirname(__FILE__) . '/img/style.css';
$css = @file_get_contents($css);

print_r(\blobfolio\common\dom::parse_css($css));

?>