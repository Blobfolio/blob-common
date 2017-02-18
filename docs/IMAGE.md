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
    //clean up <style> tag(s):
    //  merge tags, group identical rules, clean up formatting
    'clean_styles'=>false,

    //build viewBox from width/height or vice versa
    //for every tag which supports viewBox
    'fix_dimensions'=>true,

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

    //remove all <script> tags and on* attributes. note:
    //for performance reasons this does not sanitize
    //sneaky stuff like embedding script in an unexpected
    //attribute like a src or href.
    'strip_js'=>true,

    //remove all <style> tags and style/class attributes
    'strip_style'=>false,

    //remove all <title> tags
    'strip_title'=>false
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