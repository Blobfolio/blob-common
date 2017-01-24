<?php
//---------------------------------------------------------------------
// Image Tests
//---------------------------------------------------------------------

@require_once(dirname(dirname(dirname(dirname(dirname(__FILE__))))) . '/wp-load.php');
@require_once(dirname(__FILE__) . '/test.php');



$img = ABSPATH . '/wp-includes/fonts/dashicons.svg';
$img2 = ABSPATH . '/wp-includes/js/mediaelement/bigplay.svg';

\blobfolio\test\cli::record('common_get_clean_svg', array($img), common_get_clean_svg($img));

\blobfolio\test\cli::record('common_get_svg_dimensions', array($img), common_get_svg_dimensions($img));
\blobfolio\test\cli::record('common_get_svg_dimensions', array($img2), common_get_svg_dimensions($img2));

\blobfolio\test\cli::record('common_get_blank_image', array(), common_get_blank_image());


\blobfolio\test\cli::print('IMAGE FUNCTIONS');
?>