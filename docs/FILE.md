# Files and Paths

blob-common provides many file and path helpers to aid with output, translation, etc.

Many of these are available in both by-value and by-reference versions. The functionality is identical either way, except the former returns a copy of the original variable, while the latter modifies the original variable in place.

**Namespace:**
`blobfolio\common\file`
`blobfolio\common\ref\file`

**Use:**
```php
//by value
$path = blobfolio\common\file::leadingslash('path/to/img.jpg');

//by reference
blobfolio\common\ref\file::leadingslash($path);
```



##### Table of Contents

 * [data_uri()](#data_uri)
 * [empty_dir()](#empty_dir)
 * [leadingslash()](#leadingslash)
 * [path()](#path)
 * [readfile_chunked()](#readfile_chunked)
 * [redirect()](#redirect)
 * [trailingslash()](#trailingslash)
 * [unixslash()](#unixslash)
 * [unleadingslash()](#unleadingslash)
 * [unparse_url()](#unparse_url)
 * [untrailingslash()](#untrailingslash)



## data_uri()

Return a file as a Data-URI for, e.g., embedding in HTML.

#### Arguments

 * (*string*) Path

#### Returns

Returns a Data-URI string on success or `FALSE` if the file could not be opened.

#### Example

```html
<img src="<?=blobfolio\common\file::data_uri('/path/to/world.jpg')?>" />
```



## empty_dir()

Determine whether a directory is empty.

#### Arguments

 * (*string*) Path

#### Returns

Returns `TRUE` if the directory is empty, `FALSE` if it isn't or if the path was unreadable.



## leadingslash()

Ensure the path has a leading `"/"`.

#### Versions

 * By Value
 * By Reference

#### Arguments

 * (*mixed*) Path. If an array is passed, each value will be recursively slashed.

#### Returns

Returns the path with a leading `"/"` if passed by value, otherwise `TRUE`.



## path()

Convert backslashes to forwardslashes, end directories with a trailing slash, expand symlinks, and resolve to absolute paths (when possible).

#### Versions

 * By Value
 * By Reference

#### Arguments

 * (*mixed*) Path. If an array is passed, each value will be recursively sanitized.
 * (*bool*) (*optional*) Validate. If `TRUE`, the path must exist and be readable. Default: `TRUE`

#### Returns

Returns the sanitized path if passed by value. If validating and the path is bad, `FALSE` is returned. When passed by value, `TRUE` or `FALSE` is returned.

#### Example

```php
//by value
$foo = blobfolio\common\file::path('..\hello'); ///var/www/foobar/hello/
$foo = blobfolio\common\file::path('../hello', true); //FALSE

//by reference
blobfolio\common\ref\file::path($foo);
```



## readfile_chunked()

This buffers the contents of a file in chunks, greatly reducing the strain on a web server when transmitting large files through PHP.

#### Arguments

 * (*string*) Path
 * (*bool*) (*optional*) Return Bytes. When `TRUE`, the function returns the number of bytes like `readfile()`, otherwise the status is returned. Default: `TRUE`

#### Returns

The contents of the file are buffered and flushed. The function returns either the byte count or status of the read.



## redirect()

Unset `$_REQUEST` data if any and issue a redirect to another location. By default this will be accomplished by sending a `"Location"` header, but if headers have already been sent it will output Javascript instead.

#### Arguments

 * (*string*) URL

#### Returns

N/A



## trailingslash()

Ensure a path ends in a `"/"`.

#### Versions

 * By Value
 * By Reference

#### Arguments

 * (*mixed*) Path. If an array is passed, each value will be recursively slashed.

#### Returns

Returns the slashed path if passed by value, otherwise `TRUE`.



## unixslash()

Convert `"\"` to `"/"`.

#### Versions

 * By Value
 * By Reference

#### Arguments

 * (*mixed*) Path. If an array is passed, each value will be recursively slashed.

#### Returns

Returns the slashed path if passed by value, otherwise `TRUE`.



## unleadingslash()

Remove the leading slash on a path, if any.

#### Versions

 * By Value
 * By Reference

#### Arguments

 * (*mixed*) Path. If an array is passed, each value will be recursively unslashed.

#### Returns

Returns the unslashed path if passed by value, otherwise `TRUE`.



## unparse_url()

The opposite of `parse_url()`, this will rebuild a URL given an array of parts.

#### Arguments

 * (*array*) Parts, i.e. the output of `parse_url()`.

#### Returns

Returns a URL as a string or `FALSE` if invalid.

#### Example

```php
$parsed = parse_url('https://google.com?s=Foobar');
/*
Array
(
    [scheme] => https
    [host] => google.com
    [query] => s=Foobar
)
*/
echo blobfolio\common\file::unparse_url($parsed); //https://google.com?s=Foobar
```



## untrailingslash()

Remove the tailing slash from a path, if any.

#### Versions

 * By Value
 * By Reference

#### Arguments

 * (*mixed*) Path. If an array is passed, each value will be recursively unslashed.

#### Returns

Returns the unslashed path if passed by value, otherwise `TRUE`.
