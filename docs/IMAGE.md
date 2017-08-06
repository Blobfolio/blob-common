# Images

blob-common provides many image helpers.

**Namespace:**
`blobfolio\common\image`

**Use:**
```php
$svg = blobfolio\common\image::clean_svg('path/to/img.svg');
```



##### Table of Contents

 * [clean_svg()](#clean_svg)
 * [has_webp()](#has_webp)
 * [svg_dimensions()](#svg_dimensions)
 * [to_webp()](#to_webp)



## clean_svg()

This function cleans up SVG code for safer inline insertion into your document. It fixes some common Illustrator bugs (like broken reference links and the generic `id="Layer_1"` definition), strips `DOCTYPE` headers, and reduces whitespace. Many additional options are available (see below).

Note: this requires `DOMDocument` support.

#### Arguments

 * (*string*) Path
 * (*array*) (*optional*) Arguments. See below for more details. Default: `NULL`
 * (*string*) (*optional*) Output, either `"HTML"` or `"DATA_URI"`. Default: `"HTML"`

#### Returns

Returns the SVG source code or `FALSE` on error.

#### Example

```php
//possible arguments
$args = array(
    // Clean up <style> tag(s): merge tags, group identical rules, clean
    // up formatting.
    'clean_styles'=>false,

    // Build viewBox from width/height or vice versa for every tag which
    // supports viewBox.
    'fix_dimensions'=>false,

    // Set up an xmlns:svg namespace as a workaround for frameworks like
    // Vue.JS which remove stray <style> tags.
    'namespace'=>false,

    // Randomize any ID attributes to ensure that e.g. a million things
    // aren't all named "layer_1".
    'random_id'=>false,

    // Rename and merge all defined classes. For example, an SVG sprite
    // might have a hundred identical classes; this will generate a new
    // class name for each unique rule and remove all others.
    'rewrite_styles'=>false,

    // Remove invalid tags and attributes, strip script-y things, fix
    // formatting, etc. This is the only option enabled by default, and
    // is highly recommended for use on production environments.
    'sanitize'=>true,

    // Cleaning SVGs in PHP can be slow. This option will save the
    // output so on subsequent calls the file can be delivered as-is.
    // The original file is renamed *.dirty.123123123 in case you need
    // to revert.
    'save'=>false,

    // Remove any data-* attributes.
    'strip_data'=>false,

    // Remove all ID attributes.
    'strip_id'=>false,

    // Remove all <style> tags and style/class attributes.
    'strip_style'=>false,

    // Remove all <title> tags.
    'strip_title'=>false,

    // Additional whitelist tags, beyond spec.
    'whitelist_tags'=>array(),

    // Additional whitelist attributes, beyond spec.
    'whitelist_attributes'=>array(),

    // Additional whitelist protocols, beyond http and https.
    'whitelist_protocols'=>array(),

    // Additional whitelist domains, beyond creativecommons.org,
    // inkscape.org, sodipodi.sourceforge.net, w3.org
    'whitelist_domains'=>array()
)
```

```html
<div class="logo-wrapper">
    <?=blobfolio\common\image::clean_svg('/path/to/logo.svg')?>    
</div>
```



## has_webp()

Determine whether the system is able to generate WebP images using the `cwebp` binaries. This is not definitive, but a good early test.

#### Arguments

 * (*string*) (*optional*) `cwebp` path. Default: `"/usr/bin/cwebp"`
 * (*string*) (*optional*) `gif2webp` path. Default: `"/usr/bin/gif2webp"`

#### Returns

Returns `FALSE` if either binary is missing or unreadable, otherwise `TRUE`.



## svg_dimensions()

Find the native width and height for an SVG.

#### Arguments

 * (*string*) SVG (path or content)

#### Returns

Returns an array with `"width"` and `"height"` keys or `FALSE` on failure.

#### Example

```php
print_r(blobfolio\common\image::svg_dimensions('/path/to/logo.svg'));
/*
array(
    [width] => 555.3,
    [height] => 30
)
*/
```



## to_webp()

Generate a WebP from a JPEG, PNG, or GIF source. This requires `cwebp` and `gif2webp` binaries installed server-side and accessible to PHP. `proc_open()` and its family of functions must be enabled.

#### Arguments

 * (*string*) Source path
 * (*string*) (*optional*) Output path. If `NULL`, the source path (with a swapped extension) will be used. Default: `NULL`
 * (*string*) (*optional*) `cwebp` path. Default: `"/usr/bin/cwebp"`
 * (*string*) (*optional*) `gif2webp` path. Default: `"/usr/bin/gif2webp"`
 * (*bool*) (*optional*) Refresh. If `FALSE`, the image will not be generated if it already exists. Default: `FALSE`

#### Returns

Returns `TRUE` if the output file exists at the end of the process, otherwise `FALSE`.