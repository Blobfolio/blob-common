<?php
//---------------------------------------------------------------------
// File Tests
//---------------------------------------------------------------------

@require_once(dirname(dirname(__FILE__)) . '/lib/vendor/autoload.php');
@require_once(dirname(__FILE__) . '/test.php');



//-------------------------------------------------
// Slashing and paths

$data = array(
	'../README.md',
	'../docs',
	'path/to/dir/',
	'/path/to/dir'
);

foreach ($data as $d) {
	\blobfolio\test\cli::record('file::leadingslash', array($d), \blobfolio\common\file::leadingslash($d));
}
\blobfolio\test\cli::record('file::leadingslash', array($data), \blobfolio\common\file::leadingslash($data));

foreach ($data as $d) {
	\blobfolio\test\cli::record('file::unleadingslash', array($d), \blobfolio\common\file::unleadingslash($d));
}
\blobfolio\test\cli::record('file::unleadingslash', array($data), \blobfolio\common\file::unleadingslash($data));

foreach ($data as $d) {
	\blobfolio\test\cli::record('file::trailingslash', array($d), \blobfolio\common\file::trailingslash($d));
}
\blobfolio\test\cli::record('file::trailingslash', array($data), \blobfolio\common\file::trailingslash($data));

foreach ($data as $d) {
	\blobfolio\test\cli::record('file::untrailingslash', array($d), \blobfolio\common\file::untrailingslash($d));
}
\blobfolio\test\cli::record('file::untrailingslash', array($data), \blobfolio\common\file::untrailingslash($data));

foreach ($data as $d) {
	\blobfolio\test\cli::record('file::unixslash', array($d), \blobfolio\common\file::unixslash($d));
}
\blobfolio\test\cli::record('file::unixslash', array($data), \blobfolio\common\file::unixslash($data));

foreach ($data as $d) {
	\blobfolio\test\cli::record('file::path', array($d), \blobfolio\common\file::path($d));
}
\blobfolio\test\cli::record('file::path', array($data), \blobfolio\common\file::path($data));
\blobfolio\test\cli::record('file::path', array($data, false), \blobfolio\common\file::path($data, false));



//-------------------------------------------------
// Empty Dir

\blobfolio\test\cli::record('file::empty_dir', array('../docs'), \blobfolio\common\file::empty_dir('../docs'));



//-------------------------------------------------
// Data URI

\blobfolio\test\cli::record('file::data_uri', array('../wp/img/blobfolio.svg'), \blobfolio\common\file::data_uri('../wp/img/blobfolio.svg'));



//-------------------------------------------------
// Unparse URL

$data = parse_url('https://google.com:123/foobar?s=5');
\blobfolio\test\cli::record('file::unparse_url', array($data), \blobfolio\common\file::unparse_url($data));



\blobfolio\test\cli::print('FILE/PATH FUNCTIONS');
?>