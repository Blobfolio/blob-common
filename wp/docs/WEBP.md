# Reference: WebP

The WebP image format offers superior compression over the staple formats JPEG, PNG, and GIF. This guide covers functionality that allows WordPress to automatically generate WebP copies of all media you upload and serve them in a way that maintains compatibility with most modern web browsers.

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

Note: plugins that regenerate thumbnails or replace media will probably not correctly account for the WebP sister files. The quickest way to remove **all** WebP images from your uploads folder is to run a quick command through SSH:

```bash
# find and delete all xxx.webp files residing in the wp-content/uploads folder
find /path/to/wp-content/uploads -name "*.webp" -type f -delete
```



##### Table of Contents

 * [common_webp_cleanup()](#common_webp_cleanup)
 * [common_get_webp_sister()](#common_get_webp_sister)
 * [common_get_webp_picture()](#common_get_webp_picture)
 * [common_get_webp_src()](#common_get_webp_src)
 * [common_get_webp_srcset()](#common_get_webp_srcset)



## common_webp_cleanup()

This function deletes any WebP thumbnails generated for a given attachment. It is called automatically whenever an image is deleted from the media library, but can also be called manually.

#### Arguments

 * (*int*) Attachment ID

#### Return

Returns `TRUE` or `FALSE`.



## common_get_webp_sister()

This function returns the WebP counterpart for the image source provided (e.g. image1.jpeg -> image1.webp). If the WebP source does not exist, it will try to generate it. The source must be in JPEG, PNG, or GIF format.

#### Arguments

 * (*string*) Path or URL

#### Return

This function returns the corresponding WebP's path or URL (depending on what you passed), or `FALSE` on failure.



## common_get_webp_picture()

The [common_get_webp_src()](#common_get_webp_src) and [common_get_webp_srcset()](#common_get_webp_srcset) function below are based on their WP counterparts, largely automating the image selections. But the `<picture>` element can serve entirely different sources at arbitrary breakpoints (not just different resolutions). That's what this function is for.

WebP support is not universal. This function will return a `<picture>` element with both WebP and original sources. An old fashioned `<img>` tag is included as a fallback for browsers that do not support `<picture>`.

#### Arguments

 * (*array*) Arguments

```php
//argument defaults
array(
	'attachment_id'=>0, //the fallback/default attachment id
	'sources'=>array(
		array(
			'attachment_id'=>0, //if this source pulls from a different attachment than the default provided above...
			'size'=>array('full'), //one more more sizes to use for the srcset. if one size is passed, WP will calculate the srcset, otherwise only the specific sizes will be used.
			'sizes'=>array(), //a string or array containing data for the `sizes` attribute, optional
			'media'=>'' //e.g. (min-width: 75em)
		),
		array()...
	),
	'alt'=>get_bloginfo('name'), //alt tag for the <img>
	'classes'=>array() //classes to add to the <picture>
	'default_size'=>'full' //the fallback size
)
```

#### Return

This function returns a `<picture>` element containing matching sources or false on failure. If WebP's cannot be located or generated, the element will only contain standard sources. If any individual source has multiple aspect ratios, all matches will be used (e.g. `srcset`).



## common_get_webp_src()

WebP support is not universal. This function will return a `<picture>` element with both WebP and original sources. An old fashioned `<img>` tag is included as a fallback for browsers that do not support `<picture>`.

There is a corresponding shortcode that can be used inside a blog post: `common-webp-src`. The arguments are the same, except `classes` should be a comma-separated string.

When using the shortcode, the `<picture>` element will be placed inside a `<figure>` element with the default WYSIWYG class `"wp-caption"`. Any specified classes will be bound to the `<figure>`, not the `<picture>`. If a caption is specified, a `<figcaption>` element will be added to the `<figure>` with the WYSIWYG class `"wp-caption-text"`.

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
```html
<!-- example shortcode -->
[common-webp-src attachment_id="0" size="full" classes="class-1,class-2,class-3"]An optional caption.[/common-webp-src]
```

#### Return

This function returns a `<picture>` element containing matching sources or false on failure. If WebP's cannot be located or generated, the element will only contain standard sources.



## common_get_webp_srcset()

This works just like `common_get_webp_src()` except it supports `srcset` and `sizes` attributes for responsive image serving. The `srcset` sources are pulled using WP's `wp_get_attachment_image_srcset()` function.

There is a corresponding shortcode that can be used inside a blog post: `common-webp-srcset`. The arguments are the same, except array types should be comma-separated strings.

When using the shortcode, the `<picture>` element will be placed inside a `<figure>` element with the default WYSIWYG class `"wp-caption"`. Any specified classes will be bound to the `<figure>`, not the `<picture>`. If a caption is specified, a `<figcaption>` element will be added to the `<figure>` with the WYSIWYG class `"wp-caption-text"`.

#### Arguments

 * (*array*) Arguments

```php
//argument defaults
array(
	'attachment_id'=>0,
	'size'=>array('full'),//one or more sizes for the srcset. if one size is passed, WP will calculate the srcset the usual way, otherwise only the specific sizes will be used
	'sizes'=>array(), //a string or array containing data for the `sizes` attribute, optional
	'alt'=>get_bloginfo('name'), //alt tag for the <img>
	'classes'=>array(), //classes to add to the <picture>
	'default_size'=>null //the src size to use for the <img> fallback; defaults to the size passed via 'size'
)
```
```html
<!-- example shortcode -->
[common-webp-srcset attachment_id="0" size="full" sizes="100vw" classes="class-1,class-2,class-3"]An optional caption.[/common-webp-srcset]
```

#### Return

This function returns a `<picture>` element containing matching sources or false on failure. If WebP's cannot be located or generated, the element will only contain standard sources.