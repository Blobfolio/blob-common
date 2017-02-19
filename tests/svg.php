<?php
//---------------------------------------------------------------------
// SVG Test
//---------------------------------------------------------------------

@require_once(dirname(dirname(__FILE__)) . '/lib/vendor/autoload.php');

$path = dirname(__FILE__) . '/img/monogram.svg';
$content = @file_get_contents($path);
$args = array(
	'clean_styles'=>true,		//consistent formatting, group like rules
	'fix_dimensions'=>true,		//supply missing width, height, viewBox
	'namespace'=>false,			//add an svg: namespace
	'random_id'=>false,			//randomize IDs
	'rewrite_styles'=>true,		//redo classes for overlaps
	'strip_data'=>true,			//remove data-X attributes
	'strip_id'=>true,			//remove all IDs
	'sanitize'=>true,			//remove all Javascript
	'strip_style'=>false,		//remove all styles
	'strip_title'=>true			//remove all titles
);

function section_title(string $title='') {
	echo "\n" . str_repeat('-', 25) . "\n$title\n" . str_repeat('-', 25) . "\n";
}

section_title('Original');
echo "\n$content\n";

section_title('sanitize::svg()');
echo "\n" . \blobfolio\common\sanitize::svg($content) . "\n";

section_title('image::clean_svg()');
echo "\n" . \blobfolio\common\image::clean_svg($path, $args) . "\n";

?>