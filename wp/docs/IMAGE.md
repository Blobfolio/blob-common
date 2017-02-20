# Reference: Image Functions

This guide documents image-related functions. The code can be located in `functions-image.php`.

See also: [JIT Thumbnails](https://github.com/Blobfolio/blob-common/blob/master/wp/docs/JIT.md) and [WebP](https://github.com/Blobfolio/blob-common/blob/master/wp/docs/WEBP.md).



##### Table of Contents

 * SVG
   * [common_get_clean_svg()](#common_get_clean_svg)
   * [common_get_svg_dimensions()](#common_get_svg_dimensions)
 * [common_get_blank_image()](#common_get_blank_image)
 * [common_get_featured_image_path()](#common_get_featured_image_path)
 * [common_get_featured_image_src()](#common_get_featured_image_src)
 * [common_get_image_srcset()](#common_get_image_srcset)



## common_get_clean_svg()

This function cleans up SVG code for safer inline insertion into your document. It fixes some common Illustrator bugs (like broken reference links and the generic `id="Layer_1"` definition), strips `DOCTYPE` headers, and reduces whitespace. Many additional options are available (see below).

Note: this requires `DOMDocument` support.

#### Arguments

 * (*string*) Path
 * (*array*) (*optional*) Arguments. See below for more details. Note: for historical reasons you can pass a single boolean to specify whether or not to randomize all IDs. If you call this function a lot, you can set a `WP_CLEAN_SVG` constant as the default arguments you wish to pass. (Passing any arguments explicitly will override your default.) Default: `NULL`

#### Return

This function returns the SVG contents as a string or `FALSE` if the file is missing, bad, etc.

#### Example

```php
//possible arguments
$args = array(
    //clean up <style> tag(s):
    //  merge tags, group identical rules, clean up formatting
    'clean_styles'=>false,

    //build viewBox from width/height or vice versa
    //for every tag which supports viewBox
    'fix_dimensions'=>false,

    //set up an xmlns:svg namespace as a workaround for
    //frameworks like Vue.JS which remove stray <style>
    //tags
    'namespace'=>false,

    //randomize any ID attributes to ensure that e.g.
    //a million things aren't all named "layer_1"
    'random_id'=>false,

    //rename and merge all defined classes. for example,
    //an SVG sprite might have a hundred identical
    //classes; this will generate a new class name for
    //each unique rule and remove all others.
    'rewrite_styles'=>false,

    //remove invalid tags and attributes, strip script-y
    //things. note: this does not remove CSS properties
    //like behavior or extension, not does it strip
    //embedded data:
    'sanitize'=>true,

    //cleaning SVGs in PHP can be slow. this option will
    //save the cleaned output so on subsequent calls
    //the file can be delivered as-is. the original file
    //is renamed *.dirty.123123123 in case you need to
    //revert.
    'save'=>false,

    //remove any data-* attributes
    'strip_data'=>false,

    //remove all ID attributes
    'strip_id'=>false,

    //remove all <style> tags and style/class attributes
    'strip_style'=>false,

    //remove all <title> tags
    'strip_title'=>false
)
```

```html
<!-- my SVG -->
<?=common_get_clean_svg('/path/to/logo.svg')?>
```



## common_get_svg_dimensions()

This function returns the width and height of an SVG.

Note: this requires `simpleXML` support.

#### Arguments

 * (*string*) Image path

#### Return

This function returns an associative array containing the width and height of the SVG, keyed thusly. If the SVG cannot be parsed, the attributes default to `0`.



## common_get_blank_image()

This function returns a data-uri of a 1x1 transparent GIF, which can be handy if you are lazy-loading sources or something.

#### Arguments

 * N/A

#### Return

This function returns a string you can use for an image source.

#### Example

```html
<img src="<?=common_get_blank_image()?>" alt="Invisible Image" />
```



## common_get_featured_image_path()

This function will return the file path to a post's featured image, optionally at a specific thumbnail size.

#### Arguments

 * (*int*) (*optional*) Post ID. Default `get_the_ID()`
 * (*string*) (*optional*) Size. Default `NULL`

#### Return

This function returns the file path as a string or `FALSE` on failure.



## common_get_featured_image_src()

This function returns the URL to a post's featured image in one step instead of the two or three normally required.

#### Arguments

 * (*int*) (*optional*) Post ID. Default `get_the_ID()`
 * (*string*) (*optional*) Size. Default `NULL`
 * (*bool*) (*optional*) Return attributes? If `TRUE` is passed, it will return an array formatted like the value returned by `wp_get_attachment_image_src()`, otherwise it returns just the image URL. Default `FALSE`
 * (*int*) (*optional*) Fallback image ID. If the post has no featured image, this image will be used instead. Default `0`

#### Return

If `$attributes` is `FALSE`, the URL for the image is returned; otherwise an array containing the URL, width, and height is returned. `FALSE` is returned on failure.



## common_get_image_srcset()

This function returns a string that can be used for an image's `srcset` attribute, like `wp_get_attachment_image_srcset()`. However it will not return `FALSE` in the event fewer than two sources exist, and you can optionally pass more than one size, in which case the `srcset` will only contain those specific sizes (rather than letting WP calculate matches).

#### Arguments

 * (*int*) Attachment ID
 * (*mixed*) (*optional*) Size(s). If one size is passed, WordPress will calculate an `srcset` from aspect ratios. If more than one size is passed, only those sources will be used. Default `"full"`

#### Return

Returns the `srcset` string or `FALSE` on failure.