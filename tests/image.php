<?php
//---------------------------------------------------------------------
// Cast Tests
//---------------------------------------------------------------------

@require_once(dirname(dirname(__FILE__)) . '/lib/vendor/autoload.php');
@require_once(dirname(__FILE__) . '/test.php');



//-------------------------------------------------
// SVGs

$path = '../wp/img/blobfolio.svg';

\blobfolio\test\cli::record('image::clean_svg', array($path), \blobfolio\common\image::clean_svg($path));

\blobfolio\test\cli::record('image::svg_dimensions', array($path), \blobfolio\common\image::svg_dimensions($path));

\blobfolio\test\cli::record('image::has_webp', array(), \blobfolio\common\image::has_webp());

\blobfolio\test\cli::record('image::to_webp', array('img/DSCF0229.jpg'), \blobfolio\common\image::to_webp('img/DSCF0229.jpg'));
\blobfolio\test\cli::record('image::to_webp', array('img/DSCF0229.jpg','img/' . time() . '.webp'), \blobfolio\common\image::to_webp('img/DSCF0229.jpg', 'img/' . time() . '.webp'));



\blobfolio\test\cli::print('IMAGE FUNCTIONS');
?>