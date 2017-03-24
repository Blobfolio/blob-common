# Multi-Byte Helpers

blob-common contains a handful of wrapper functions that will use `mbstring` when available but fall back to their non-multi-byte counterparts when needed.

Many of these are available in both by-value and by-reference versions. The functionality is identical either way, except the former returns a copy of the original variable, while the latter modifies the original variable in place.

**Namespace:**
`blobfolio\common\mb`
`blobfolio\common\ref\mb`

**Use:**
```php
//by value
$foo = blobfolio\common\mb::strtolower('HI THERE');

//by reference
blobfolio\common\ref\mb::strtolower($foo);
```



##### Table of Contents

 * [parse_str()](#parse_str)
 * [parse_url()](#parse_url)
 * [str_split()](#str_split)
 * [strlen()](#strlen)
 * [str_pad()](#str_pad)
 * [strpos()](#strpos)
 * [strrpos()](#strrpos)
 * [strtolower()](#strtolower)
 * [strtoupper()](#strtoupper)
 * [substr()](#substr)
 * [substr_count()](#substr_count)
 * [ucfirst()](#ucfirst)
 * [ucwords()](#ucwords)



## parse_str()

Parse a query string, e.g. `"foo=bar&apples=oranges"`.

#### Arguments

 * (*string*) String
 * (*mixed*) (*reference*) Result

#### Returns

N/A



## parse_url()

Break a URL down into its constituent parts. Aside from adding Unicode support, this wrapper will:
 * Fix scheme-agnostic URLs like `"//domain.com"`;
 * Treat a schemeless host as a host rather than a path;
 * Compact IPv6 hosts;
 * Punycode conversion of Unicode hosts (if `php-intl` is installed);

#### Arguments

 * (*string*) URL
 * (*int*) (*optional*) Component

#### Returns

Returns an array of the URL parts if `$component` is omitted, otherwise the component or `NULL` is returned.

#### Example

```php
$foo = blobfolio\common\mb::parse_url('http://☺.com', PHP_URL_HOST); //xn--74h.com

$foo = blobfolio\common\mb::parse_url('http://☺.com/party-time/');
/*
array(
    scheme => http
    host => xn--74h.com
    path => /party-time/
)
*/
```



## str_split()

Split the characters in a string by the desired length.

#### Arguments

 * (*string*) String
 * (*int*) (*optional*) Length. Default: `1`

#### Returns

Returns an array of characters.

#### Example

```php
$foo = 'quEen BjöRk Ⅷ loVes aPplEs.';
print_r(blobfolio\common\mb::str_split($foo, 5));
/*
array(
    0 => quEen
    1 =>  BjöR
    ...
)
*/
```



## strlen()

Count the number of characters or the byte size depending on whether `mbstring` is available.

#### Arguments

 * (*string*) String

#### Returns

Returns the size of the string.



## str_pad()

Pad a string so it meets a required length.

#### Arguments

 * (*string*) String
 * (*int*) Width
 * (*string*) (*optional*) Pad string. Default: `" "`
 * (*int*) (*optional*) Pad type, either `STR_PAD_LEFT`, `STR_PAD_BOTH`, or `STR_PAD_RIGHT`. Default: `STR_PAD_RIGHT`

#### Returns

Returns the padded string. If the desired width is less than the string or negative, the string will be returned as-was. If the pad string doesn't divide evenly into the pad width, it may be truncated.

#### Example

```php
$foo = 'quEen BjöRk Ⅷ loVes aPplEs.';
echo blobfolio\common\mb::str_pad($foo, 50, '<>', STR_PAD_LEFT));
echo blobfolio\common\mb::str_pad($foo, 50, '<>', STR_PAD_BOTH));
echo blobfolio\common\mb::str_pad($foo, 50, '<>', STR_PAD_RIGHT));
/*
><><><><><><><><><><><>quEen BjöRk Ⅷ loVes aPplEs.
<><><><><><>quEen BjöRk Ⅷ loVes aPplEs.<><><><><><
quEen BjöRk Ⅷ loVes aPplEs.<><><><><><><><><><><><
*/
```



## strpos()

Find the first occurrence of the needle in the haystack.

#### Arguments

 * (*string*) Haystack
 * (*string*) Needle
 * (*int*) (*optional*) Offset

#### Returns

Returns the starting position of the needle of `FALSE` if not found.



## strrpos()

Find the last occurrence of the needle in the haystack.

#### Arguments

 * (*string*) Haystack
 * (*string*) Needle
 * (*int*) (*optional*) Offset

#### Returns

Returns the last starting position of the needle of `FALSE` if not found.



## strtolower()

Lowercase a string. This function additionally catches various unicode characters with upper/lower variants that `mbstring` doesn't bother checking for.

#### Versions

 * By Value
 * By Reference

#### Arguments

 * (*mixed*) String. If an array is passed, each value will be recursively lowercased.

#### Returns

If passing by value, the lowercased string is returned, otherwise `TRUE`.

#### Example

```php
$foo = 'quEen BjöRk Ⅷ loVes aPplEs.';
echo blobfolio\common\mb::strtolower($foo); //queen björk ⅷ loves apples.
```



## strtoupper()

Uppercase a string. This function additionally catches various unicode characters with upper/lower variants that `mbstring` doesn't bother checking for.

#### Versions

 * By Value
 * By Reference

#### Arguments

 * (*mixed*) String. If an array is passed, each value will be recursively uppercased.

#### Returns

If passing by value, the uppercased string is returned, otherwise `TRUE`.

#### Example

```php
$foo = 'quEen BjöRk Ⅷ loVes aPplEs.';
echo blobfolio\common\mb::strtoupper($foo); //QUEEN BJÖRK Ⅷ LOVES APPLES.
```



## substr()

Retrieve a portion of the string.

#### Arguments

 * (*string*) String
 * (*int*) (*optional*) Start. Default: `0`
 * (*int*) (*length*) Length. Default: `NULL`

#### Returns

Returns the matching substring.



## substr_count()

Count the number of occurrences of the needle in the haystack.

#### Arguments

 * (*string*) Haystack
 * (*string*) Needle

#### Returns

Returns the number of matches. 



## ucfirst()

Sentence-case a string. This function additionally catches various unicode characters with upper/lower variants that `mbstring` doesn't bother checking for.

#### Versions

 * By Value
 * By Reference

#### Arguments

 * (*mixed*) String. If an array is passed, each value will be recursively sentence-cased.

#### Returns

If passing by value, the sentence-cased string is returned, otherwise `TRUE`.

#### Example

```php
$foo = 'quEen BjöRk Ⅷ loVes aPplEs.';
echo blobfolio\common\mb::ucfirst($foo); //QuEen BjöRk Ⅷ loVes aPplEs.
```



## ucwords()

Title-case a string. This function additionally catches various unicode characters with upper/lower variants that `mbstring` doesn't bother checking for, and will also adjust letters following dashes and forward slashes.

#### Versions

 * By Value
 * By Reference

#### Arguments

 * (*mixed*) String. If an array is passed, each value will be recursively title-cased.

#### Returns

If passing by value, the title-cased string is returned, otherwise `TRUE`.

#### Example

```php
$foo = 'quEen BjöRk Ⅷ loVes aPplEs.';
echo blobfolio\common\mb::ucwords($foo); //Queen Björk Ⅷ Loves Apples.
```
