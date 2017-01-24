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

Retrieve and tidy up the source of an SVG file for, e.g., inclusion in HTML.

#### Arguments

 * (*string*) Path
 * (*array*) (*optional*) Arguments. Default: `NULL`
   * (*bool*) (*optional*) Randomize ID. If `TRUE`, a random ID will be generated on the fly, replacing e.g. `"Layer_1"`. Default: `FALSE`
   * (*bool*) (*optional*) Strip Title. Default: `FALSE`
 * (*string*) (*optional*) Output, either `"HTML"` or `"DATA_URI"`. Default: `"HTML"`

#### Returns

Returns the SVG source code or `FALSE` on error.

#### Example

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