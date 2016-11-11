# Reference: WebP

The WebP image format offers superior compression over the web staples JPEG, PNG, and GIF. This guide covers functionality that allows WordPress to automatically generate WebP copies of all media you upload and serve them in a way that maintains compatibility with most modern web browsers.

To enable WebP, add the following to `wp-config.php`:

```php
//enable WEBP image management
define('WP_WEBP_IMAGES', true);
```

Note: you must have `cwebp` and `webp2gif` binaries installed on the server, and these binaries must be accessible to PHP. If they aren't, these functions will not create or return any WebP sources, but will otherwise work.

By default, these binaries are assumed to live in `/usr/bin`. If you store them somewhere else, you can override the default paths by adding the following to `wp-config.php`:

```php
define('WP_WEBP_CWEBP', '/path/to/cwebp');
define('WP_WEBP_GIF2WEBP', '/path/to/gif2webp');
```



##### Table of Contents

 * [common_webp_cleanup()](#common_webp_cleanup)
 * [common_get_webp_sister()](#common_get_webp_sister)
 * [common_get_webp_src()](#common_get_webp_src)
 * [common_get_webp_srcset()](#common_get_webp_srcset)



## common_webp_cleanup()

This function deletes any WebP thumbnails generated for a given attachment. It is called automatically whenever an image is deleted from the media library, but can also be called manually.

#### Arguments

 * (*int*) Attachment ID

#### Return

Returns `TRUE` or `FALSE`.



## common_get_webp_sister()

This function returns the WebP counterpart for the image source provided (e.g. image1.jpeg -> image1.webp). If the WebP source does not exist, it will try to generate it.

#### Arguments

 * (*string*) Path or URL

#### Return

This function returns the corresponding WebP's path or URL (depending on what you passed), or `FALSE` on failure.



## common_get_webp_src()

WebP support is not universal. This function will return a `<picture>` element with both WebP and original sources. An old fashioned `<img>` tag is included as a fallback for browsers that do not support `<picture>`.

#### Arguments

 * (*array*) Arguments

```php
//argument defaults
array(
	'attachment_id'=>0,
	'size'=>'full',
	'alt'=>get_bloginfo('name'), //alt tag for the <img>
	'classes'=>array() //classes to add to the <picture>
)
```

#### Return

This function returns a `<picture>` element containing matching sources or false on failure. If WebP's cannot be located or generated, the element will only contain standard sources.



## common_get_webp_srcset()

This works just like `common_get_webp_src()` except it supports the `srcset` attribute for responsive image serving.

#### Arguments

 * (*array*) Arguments

```php
//argument defaults
array(
	'attachment_id'=>0,
	'size'=>'full',
	'sizes'=>array(), //a string or array containing data for the `sizes` attribute, optional
	'alt'=>get_bloginfo('name'), //alt tag for the <img>
	'classes'=>array(), //classes to add to the <picture>
	'default_size'=>null //the size to use for the <img> fallback; defaults to the size passed via 'size'
)
```

#### Return

This function returns a `<picture>` element containing matching sources or false on failure. If WebP's cannot be located or generated, the element will only contain standard sources.