# Reference: Image Functions

This guide documents image-related functions. The code can be located in `functions-image.php`.

See also: [JIT Thumbnails](https://github.com/Blobfolio/blob-common/blob/1.5/blob-common/docs/JIT.md) and [WebP](https://github.com/Blobfolio/blob-common/blob/1.5/blob-common/docs/WEBP.md).



##### Table of Contents

 * SVG
   * [common_get_clean_svg()](#common_get_clean_svg)
   * [common_get_svg_dimensions()](#common_get_svg_dimensions)
 * [common_get_blank_image()](#common_get_blank_image)
 * [common_get_featured_image_path()](#common_get_featured_image_path)
 * [common_get_featured_image_src()](#common_get_featured_image_src)



## common_get_clean_svg()

This function cleans up SVG code for safer inline insertion into your document. It fixes some common Illustrator bugs (like broken reference links and the generic `id="Layer_1"` definition), strips `DOCTYPE` headers, and reduces whitespace.

Note: this requires `DOMDocument` support.

#### Arguments

 * (*string*) Image path
 * (*bool*) (*optional*) Randomize ID. Default `FALSE`

#### Return

This function returns the SVG contents as a string or `FALSE` if the file is missing, bad, etc.

#### Example

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