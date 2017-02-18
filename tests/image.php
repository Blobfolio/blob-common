<?php
//---------------------------------------------------------------------
// Cast Tests
//---------------------------------------------------------------------

@require_once(dirname(dirname(__FILE__)) . '/lib/vendor/autoload.php');
@require_once(dirname(__FILE__) . '/test.php');



//-------------------------------------------------
// SVGs

$path = dirname(__FILE__) . '/img/mail.svg';
$args = array(
	'clean_styles'=>true,		//consistent formatting, group like rules
	'fix_dimensions'=>true,		//supply missing width, height, viewBox
	'namespace'=>true,			//add an svg: namespace
	'random_id'=>false,			//randomize IDs
	'rewrite_styles'=>true,		//redo classes for overlaps
	'strip_data'=>true,			//remove data-X attributes
	'strip_id'=>true,			//remove all IDs
	'strip_js'=>true,			//remove all Javascript
	'strip_style'=>false,		//remove all styles
	'strip_title'=>true			//remove all titles
);

\blobfolio\test\cli::record('image::clean_svg', array($path), \blobfolio\common\image::clean_svg($path));
\blobfolio\test\cli::record('image::clean_svg', array($path, $args), \blobfolio\common\image::clean_svg($path, $args));

\blobfolio\test\cli::record('image::svg_dimensions', array($path), \blobfolio\common\image::svg_dimensions($path));

\blobfolio\test\cli::record('image::has_webp', array(), \blobfolio\common\image::has_webp());

$in = dirname(__FILE__) . '/img/DSCF0229.jpg';
$out = dirname(__FILE__) . '/img/' . time() . '.webp';

\blobfolio\test\cli::record('image::to_webp', array($in), \blobfolio\common\image::to_webp($in));
\blobfolio\test\cli::record('image::to_webp', array($in, $out), \blobfolio\common\image::to_webp($in, $out));

\blobfolio\test\cli::print('IMAGE FUNCTIONS');
?>