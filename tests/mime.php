<?php
//---------------------------------------------------------------------
// MIME Tests
//---------------------------------------------------------------------

@require_once(dirname(dirname(__FILE__)) . '/lib/vendor/autoload.php');
@require_once(dirname(__FILE__) . '/test.php');



//-------------------------------------------------
// MIME type


\blobfolio\test\cli::record('mime::get_extension', array('jpeg'), \blobfolio\common\mime::get_extension('jpeg'));
\blobfolio\test\cli::record('mime::get_mime', array('image/jpeg'), \blobfolio\common\mime::get_mime('image/jpeg'));
\blobfolio\test\cli::record('mime::check_ext_and_mime', array('jpeg','image/jpeg'), \blobfolio\common\mime::check_ext_and_mime('jpeg', 'image/jpeg'));
\blobfolio\test\cli::record('mime::check_ext_and_mime', array('jpeg','image/gif'), \blobfolio\common\mime::check_ext_and_mime('jpeg', 'image/gif'));
\blobfolio\test\cli::record('mime::finfo', array('../wp/img/blobfolio.svg'), \blobfolio\common\mime::finfo('../wp/img/blobfolio.svg'));


\blobfolio\test\cli::record('mime::get_extensions', array(), \blobfolio\common\mime::get_extensions());

\blobfolio\test\cli::record('mime::get_mimes', array(), \blobfolio\common\mime::get_mimes());


\blobfolio\test\cli::print('MIME FUNCTIONS');
?>