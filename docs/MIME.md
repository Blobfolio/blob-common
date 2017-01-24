# MIME Types & File Extensions

Building on the [blob-mimes](https://github.com/Blobfolio/blob-mimes) database, blob-common includes methods for determining and managing MIME types given a file, extension, type, etc.

**Namespace:**
`blobfolio\common\mime`



##### Table of Contents

 * [check_ext_and_mime()](#check_ext_and_mime)
 * [finfo()](#finfo)
 * [get_extension()](#get_extension)
 * [get_extensions()](#get_extensions)
 * [get_mime()](#get_mime)
 * [get_mimes()](#get_mimes)



## check_ext_and_mime()

Verify that a file extension and MIME type belong together. Because technology is always evolving and the MIME standard is always changing, this will consider `some/thing` and `some/x-thing` equivalent.

#### Arguments

 * (*string*) File extension
 * (*string*) MIME type
 * (*bool*) (*optional*) Soft pass. If `TRUE`, the check will return `TRUE` if it lacks information about either the extension or MIME type.

#### Returns

Returns `TRUE` or `FALSE`.

#### Example

```php
$foo = blobfolio\common\mime::check_ext_and_mime('jpeg', 'image/jpeg'); //TRUE
$foo = blobfolio\common\mime::check_ext_and_mime('jpeg', 'image/gif'); //FALSE
```



## finfo()

Pull path and type information for a file, using its name and/or content. If it is determined that the file is incorrectly named, alternative names with the correct file extension(s) are suggested.

#### Arguments

 * (*string*) Path
 * (*string*) (*optional*) Nice name. If provided, the nice name will be treated as the filename. This can be useful if passing a temporary upload, for example. Default: `NULL`

#### Returns

Returns all file information that can be derived according to the format below.

#### Example

```php
print_r(blobfolio\common\mime::finfo('../wp/img/blobfolio.svg'));
/*
array(
    [dirname] => /var/www/blob-common/wp/img
    [basename] => blobfolio.svg
    [extension] => svg
    [filename] => blobfolio
    [path] => /var/www/blob-common/wp/img/blobfolio.svg
    [mime] => image/svg+xml
    [suggested_filename] => array()
)
*/
```



## get_extension()

Retrieve information about a file extension.

#### Arguments

 * (*string*) File extension

#### Returns

Returns the information or `FALSE`.

```php
print_r(blobfolio\common\mime::get_extension('jpeg'));
/*
array(
    [ext] => jpeg
    [mime] => array(
        0 => image/jpeg
        1 => image/pjpeg
    )
    [source] => array(
        0 => Apache
        1 => Nginx
        2 => freedesktop.org
    )
    [alias] => array(
        ] => image/pjpeg
    )
    [primary] => image/jpeg
)
*/
```



## get_extensions()

Retrieve information about all known file extensions.

#### Arguments

N/A

#### Returns

Returns a MIME database oganized by extension.



## get_mime()

Retrieve information about a MIME type.

#### Arguments

 * (*string*) MIME type

#### Returns

Returns the information or `FALSE`.

#### Example

```php
print_r(blobfolio\common\mime::get_mime('image/jpeg'));
/*
array(
    [mime] => image/jpeg
    [ext] => array(
        0 => jpeg
        1 => jpg
        2 => jpe
    )
    [source] => array(
        0 => Apache
        1 => Nginx
        2 => freedesktop.org
    )
)
*/
```



## get_mimes()

Retrieve information about all known MIME types.

#### Arguments

N/A

#### Returns

Returns a MIME database oganized by type.