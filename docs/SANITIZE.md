# Sanitization Helpers

blob-common contains a tons of sanitizing functions.

**All** of these functions are available in both by-value and by-reference versions. The functionality is identical either way, except the former returns a copy of the original variable, while the latter modifies the original variable in place.

**Namespace:**
`blobfolio\common\sanitize`
`blobfolio\common\ref\sanitize`

**Use:**
```php
//by value
$foo = blobfolio\common\sanitize::email('foo@Bar.com');

//by reference
blobfolio\common\ref\sanitize::email($foo);
```



##### Table of Contents

 * [accents()](#accents)
 * [attribute_value()](#attribute_value)
 * [cc()](#cc)
 * [control_characters()](#control_characters)
 * [country()](#country)
 * [csv()](#csv)
 * [date()](#date)
 * [datetime()](#datetime)
 * [domain()](#domain)
 * [email()](#email)
 * [file_extension()](#file_extension)
 * [html()](#html)
 * [hostname()](#hostname)
 * [ip()](#ip)
 * [iri_value()](#iri_value)
 * [js()](#js)
 * [mime()](#mime)
 * [name()](#name)
 * [password()](#password)
 * [printable()](#printable)
 * [province()](#province)
 * [quotes()](#quotes)
 * [state()](#state)
 * [svg()](#svg)
 * [timezone()](#timezone)
 * [to_range()](#to_range)
 * [url()](#url)
 * [utf8()](#utf8)
 * [whitespace()](#whitespace)
 * [whitespace_multiline()](#whitespace_multiline)
 * [zip5()](#zip5)



## accents()

Remove accents from characters in a string.

#### Arguments

 * (*mixed*) String. If an array is passed, each value will be recursively stripped.

#### Returns

Returns the unaccented string if passing by value, otherwise `TRUE`.

#### Example

```php
//by value
$foo = blobfolio\common\sanitize::accents('Björk Guðmundsdóttir is a swan.'); //Bjork Gudmundsdottir is a swan.

//by reference
blobfolio\common\ref\sanitize::accents($foo);
```



## attribute_value()

Decode entities, remove control characters, and trim edge whitespace.

Please note: this function is *not* for safely inserting a string value into HTML. For that, use [::html()](#html).

#### Arguments

 * (*mixed*) String. If an array is passed, each value will be recursively sanitized.

#### Returns

Returns the sanitized string if passing by value, otherwise `TRUE`.

#### Example

```php
//by value
$foo = blobfolio\common\sanitize::attribute_value('&nbsp;Björk"&quot; '); //Bjork""

//by reference
blobfolio\common\ref\sanitize::attribute_value($foo);
```



## cc()

Validate a credit card number, short of going so far as to check with the bank.

#### Arguments

 * (*string*) Card number

#### Returns

By value, returns the card number if valid, otherwise `FALSE`.  By reference returns `TRUE` or `FALSE`.

#### Example

```php
if (false === blobfolio\common\sanitize::cc($ccnum)) {
    throw new Exception('A valid credit card number is required.');
}
```



## control_characters()

Removes control characters from a string, including `"\0"`.

#### Arguments

 * (*mixed*) String. If an array is passed, each value will be recursively sanitized.

#### Returns

Returns the sanitized string if passing by value, otherwise `TRUE`.

#### Example

```php
//by value
$foo = blobfolio\common\sanitize::control_characters('\0hi'); //hi

//by reference
blobfolio\common\ref\sanitize::control_characters($foo);
```



## country()

Validate an ISO country code. If a full country name is passed, it will be converted back to an ISO code.

#### Arguments

 * (*mixed*) Country. If an array is passed, each value will be recursively sanitized.

#### Returns

Returns a valid ISO code or `""` by value, otherwise `TRUE`.

```php
//by value
$foo = blobfolio\common\sanitize::country('US'); //US
$foo = blobfolio\common\sanitize::country('canada'); //CA

//by reference
blobfolio\common\ref\sanitize::country($foo);
```



## csv()

Sanitize a cell for insertion into a CSV. This means removing new lines and doubling quotation marks.

#### Arguments

 * (*mixed*) String. If an array is passed, each value will be recursively sanitized.

#### Returns

Returns the sanitized string by value, otherwise `TRUE`.

```php
//by value
$Row = array(
    'John',
    '"The Man"',
    'Doe'
);
$Row = blobfolio\common\sanitize::csv($Row);
/*
array(
    John
    ""The Man""
    Doe
)
*/

//by reference
blobfolio\common\ref\sanitize::csv($Row);
```



## date()

Format a date string or timestamp in `YYYY-MM-DD` format.

#### Arguments

 * (*mixed*) Date. If an array is passed, each value will be recursively sanitized.

#### Returns

Returns the date or `"0000-00-00"` by value, otherwise `TRUE`.

#### Example

```php
//by value
$foo = blobfolio\common\sanitize::date('2015-01-01 11:23:48'); //2015-01-01
$foo = blobfolio\common\sanitize::date(1485211108); //2017-01-23

//by reference
blobfolio\common\ref\sanitize::date($foo);
```



## datetime()

Format a datetime string or timestamp in `YYYY-MM-DD HH:MM:SS` format.

#### Arguments

 * (*mixed*) Date. If an array is passed, each value will be recursively sanitized.

#### Returns

Returns the date or `"0000-00-00 00:00:00"` by value, otherwise `TRUE`.

#### Example

```php
//by value
$foo = blobfolio\common\sanitize::datetime('2015-01-01 11:23:48'); //2015-01-01 11:23:48
$foo = blobfolio\common\sanitize::datetime(1485211108); //2017-01-23 14:38:28

//by reference
blobfolio\common\ref\sanitize::datetime($foo);
```



## domain()

Sanitize a domain name. This function will tease out the FQDN portion of an arbitrary string, strip leading `www.` subdomains, convert Unicode to ASCII*, and validate the suffix.

Note: with this function, IP addresses and non-FQDN results will be returned as an empty string.

#### Arguments

 * (*mixed*) Domain/URL/etc. If an array is passed, each value will be recursively sanitized.
 * (*bool*) (*optional*) Return Unicode. If `TRUE` and the domain is Unicode, it will be returned thusly rather than being converted to ASCII. Default: `FALSE`

#### Returns

Returns the domain name or `""` by value, otherwise `TRUE`.

#### Example

```php
//by value
$foo = blobfolio\common\sanitize::domain('http://apple.com'); //apple.com

//by ref
blobfolio\common\ref\sanitize::domain($foo);
```



## email()

Sanitize an email address. This will remove invalid characters, quotes and apostrophes, convert to lowercase, and ensure that the host is a FQDN.

This is a little more restrictive than the pure spec and PHP's native `FILTER_SANITIZE_EMAIL`. Local parts (the user) can contain `A-Z`, `0-9`, and the following special characters: `. ! # $ % & * + - = ? _ ~`. Anything else, including comments like `user(blabla)@foo.com`, will be stripped, but won't otherwise cause a validation failure.

Unicode/IDN hosts are A-OK if the PHP extension `INTL` is installed, but will be converted to Punycode/ASCII format.

IP addresses and hosts with invalid suffix structures will be considered invalid.

#### Arguments

 * (*mixed*) Email. If an array is passed, each value will be recursively sanitized.

#### Returns

Returns a valid email address or `""` by value, otherwise `TRUE`.

#### Example

```php
//by value
$foo = blobfolio\common\sanitize::email('jane@localhost'); //empty string
$foo = blobfolio\common\sanitize::email('Jane@Doe.com'); //jane@doe.com

//by reference
blobfolio\common\ref\sanitize::email($foo);
```



## file_extension()

Sanitize a file extension. This converts the extension to lowercase and removes leading `"*"` and `"."`. Note: this will not attempt to parse paths; this is meant only to be run against an already-extracted extension.

#### Arguments

 * (*mixed*) Extension. If an array is passed, each value will be recursively sanitized.

#### Returns

Returns the file extension.

#### Example

```php
//by value
$foo = blobfolio\common\sanitize::file_extension('.JPG'); //jpg

//by reference
blobfolio\common\ref\sanitize::file_extension($foo);
```



## html()

Escape UTF-8 HTML. Note: this should only be run once on a given block of text or else entities might be double-encoded.

#### Arguments

 * (*mixed*) HTML. If an array is passed, each value will be recursively sanitized.

#### Returns

Returns the HTML by value, otherwise `TRUE`.

#### Example

```php
//by value
$foo = blobfolio\common\sanitize::html('<b>Hello</b>'); //&lt;b&gt;Hello&lt;/b&gt;

//by reference
blobfolio\common\ref\sanitize::html($foo);
```



## hostname()

Try to tease a hostname from a URL-like string. This will standardize IPs, validate suffixes, handle Unicode, etc.

Unlike `::domain()`, the result does not need to be a FQDN.

#### Arguments

 * (*string*) Domain
 * (*bool*) (*optional*) Keep `"www."`. If `FALSE`, leading `"www."` will be stripped. Default: `FALSE`
 * (*bool*) (*optional*) Return Unicode. If `TRUE` and the domain is Unicode, it will be returned thusly rather than being converted to ASCII. Default: `FALSE`

#### Returns

Returns the hostname or `FALSE` by value, otherwise `TRUE`/`FALSE`.

#### Example

```php
//by value
$foo = blobfolio\common\sanitize::hostname('http://www.apple.com', true); //apple.com

//by ref
blobfolio\common\ref\sanitize::hostname($foo, true);
```



## ip()

Compact and range-check an IPv4 or IPv6 address.

#### Arguments

 * (*mixed*) IP. If an array is passed, each value will be recursively sanitized.
 * (*bool*) (*optional*) Allow reserved. If `TRUE`, reserved/restricted IPs will be allowed. Default: `FALSE`

#### Returns

Returns the sanitized IP address or `""` by value, otherwise `TRUE`.

#### Example

```php
//by value
$foo = blobfolio\common\sanitize::ip('2600:3C00::F03C:91FF:FEAE:0FF2'); //2600:3c00::f03c:91ff:feae:ff2

//by reference
blobfolio\common\ref\sanitize::ip($foo);
```



## iri_value()

Sanitizes an IRI value to ensure it is well-formed and matches the allowed protocols and domains. By default it is designed for SVG values, but the whitelists can be augmented.

#### Arguments

 * (*mixed*) String. If an array is passed, each value will be recursively sanitized.
 * (*array*) (*optional*) Allowed protocols. Will be combined with the default, `"http"` and `"https"`.
 * (*array*) (*optional*) Allowed domains. Will be combined with the default, `"creativecommons.org"`, `"inkscape.org"`, `"sodipodi.sourceforge.net"`, and `"w3.org"`.

#### Returns

Returns the sanitized string if passing by value, otherwise `TRUE`. Invalid values will be returned/set as an empty string.

#### Example

```php
//by value
$foo = blobfolio\common\sanitize::iri_value('javascript: alert(Hi);'); //""

//by reference
blobfolio\common\ref\sanitize::iri_value($foo);
```



## js()

Escape a variable for insertion into a Javascript string. This removes newlines, straightens quotes, and escapes the enclosing quote.

#### Arguments

 * (*mixed*) String. If an array is passed, each value will be recursively sanitized.
 * (*string*) (*optional*) Quote, either `"` or `'`. Default: `'`

#### Returns

Returns the sanitized string by value, otherwise `TRUE`.

#### Example

```php
//by value
$foo = blobfolio\common\sanitize::js("How's it going?", "'"); //How\'s it going?

//by reference
blobfolio\common\ref\sanitize::js($foo);
```



## mime()

Sanitize a MIME type.

#### Arguments

 * (*mixed*) MIME type. If an array is passed, each value is recursively sanitized.

#### Returns

Returns the MIME type by value, otherwise `TRUE`.

#### Example

```php
//by value
$foo = blobfolio\common\sanitize::mime('Application/Octet-Stream'); //application/octet-stream

//by reference
blobfolio\common\ref\sanitize::mime($foo);
```



## name()

Try to sanitize a name, like a person's. This is a fool's errand, but tries to strip out completely unreasonable data.

#### Arguments

 * (*mixed*) Name

#### Returns

Returns the sanitized name by value, otherwise `TRUE`.

#### Example

```php
//by value
$foo = blobfolio\common\sanitize::name("Henry!!\nThe great"); //Henry The Great

//by reference
blobfolio\common\ref\sanitize::name($foo);
```



## password()

This is also a bit of a fool's errand, but exists mainly to prevent simple user errors (like extra whitespace) or system conflicts due to crazy input. It essentially combines the [whitespace()](#whitespace) and [printable()](#printable) filters.

This sort of filter needs to be implemented at the beginning of a project, otherwise you run the risk of preventing users from being able to type their original passwords.

#### Arguments

 * (*mixed*) Password. If an array is passed, each value is recursively sanitized.

#### Returns

Returns the password by value, otherwise `TRUE`.



## printable()

This strips out everything other than tabs, spaces, and characters that "use ink". Note: the behaviors may vary by environment, so be sure to test before implementing.

#### Arguments

 * (*mixed*) String. If an array is passed, each value is recursively sanitized.

#### Returns

Returns the printable string by value, otherwise `TRUE`.



## province()

Sanitize a Canadian province's 2-digit abbreviation. If the full name of a province or territory is passed, the abbreviation is returned.

#### Arguments

 * (*mixed*) Province. If an array is passed, each value is recursively sanitized.

#### Returns

Returns a valid 2-digit abbreviation or `""` by value, otherwise `TRUE`.

#### Example

```php
//by value
$foo = blobfolio\common\sanitize::province('alberta'); //AB
$foo = blobfolio\common\sanitize::province('ON'); //ON

//by reference
blobfolio\common\ref\sanitize::province($foo);
```



## quotes()

Straighten those awful curly quotes and apostrophes!

#### Arguments

 * (*mixed*) String. If an array is passed, each value is recursively sanitized.

#### Returns

Returns the string with normal quotes by value, otherwise `TRUE`.

#### Example

```php
//by value
$foo = blobfolio\common\sanitize::quotes('“T’was the night before Christmas...”'); //"T'was the night before Christmas..."

//by reference
blobfolio\common\ref\sanitize::quotes($foo);
```



## state()

Sanitize a US state's 2-digit abbreviation. If the full name of a state or territory is passed, the abbreviation is returned.

#### Arguments

 * (*mixed*) State. If an array is passed, each value is recursively sanitized.

#### Returns

Returns a valid 2-digit abbreviation or `""` by value, otherwise `TRUE`.

#### Example

```php
//by value
$foo = blobfolio\common\sanitize::state('puerto rico'); //PR
$foo = blobfolio\common\sanitize::state('TX'); //TX

//by reference
blobfolio\common\ref\sanitize::state($foo);
```



## svg()

Sanitize a block of SVG code for e.g. insertion into a web page. This function is called by [image::clean_svg()](https://github.com/Blobfolio/blob-common/blob/master/docs/IMAGE.md#clean_svg) when the `"sanitize"` argument is `TRUE`. If the more advanced handling of `image::clean_svg()` isn't needed, this can be called on its own.

The following sanitizing methods are employed:
 * Removal of comments (`<!-- -->` and `/* */` varieties);
 * Removal of XML, PHP, ASP;
 * Removal of Javascript tags, attributes, and values;
 * Sanitizing of CSS `url(...)` rules;
 * Namespace and IRI sanitizing via protocol and host (both extensible);
 * Extensible tag whitelist;
 * Extensible attribute whitelist;
 * Miscellaneous formatting repairs;
 * Whitespace collapsing;

#### Arguments

 * (*string*) SVG code.
 * (*array*) (*optional*) Additional whitelisted tags. Default: `NULL`
 * (*array*) (*optional*) Additional whitelisted attributes. Default: `NULL`
 * (*array*) (*optional*) Additional whitelisted protocols. Default: `NULL`
 * (*array*) (*optional*) Additional whitelisted domains. Default: `NULL`

#### Returns

Returns sanitized SVG code or an empty string on failure by value, otherwise `TRUE`.

#### Example

```php
//by value
$foo = blobfolio\common\sanitize::svg('<svg ... />');

//by reference
blobfolio\common\ref\sanitize::svg($foo);
```



## timezone()

Sanitize a timezone string. If not found among PHP's master list, it defaults to `"UTC"`.

#### Arguments

 * (*mixed*) Timezone. If an array is passed, each value is recursively sanitized.

#### Returns

Returns a valid timezone string or `"UTC"` by value, otherwise `TRUE`.

#### Example

```php
//by value
$foo = blobfolio\common\sanitize::timezone('foobar'); //UTC
$foo = blobfolio\common\sanitize::timezone('america/chicago'); //America/Chicago

//by reference
blobfolio\common\ref\sanitize::timezone($foo);
```



## to_range()

Ensure a value falls between a minimum and/or maximum boundary.

#### Arguments

 * (*mixed*) Value
 * (*mixed*) (*optional*) Min. Default: `NULL`
 * (*mixed*) (*optional*) Max. Default: `NULL`

#### Returns

If a minimum bound is specified and the value is below it, the minimum is returned. Equal and opposite for the maximum. Otherwise the original value is returned. When passing by reference, `TRUE` is always returned.

#### Example

```php
//by value
$foo = blobfolio\common\sanitize::to_range(5, 1, 10); //5
$foo = blobfolio\common\sanitize::to_range('2015-01-01', '2015-02-01'); //2015-02-01

//by reference
blobfolio\common\ref\sanitize::to_range($foo, $min, $max);
```



## url()

Remove inappropriate chraacters from a URL and make sure a valid scheme is present.

#### Arguments

 * (*mixed*) URL. If an array is passed, each value is recursively sanitized.

#### Returns

Returns the sanitized URL or `""` by value, otherwise `TRUE`.

#### Example

```php
//by value
$foo = blobfolio\common\sanitize::url('//fonts.google.com'); //https://fonts.google.com

//by reference
blobfolio\common\ref\sanitize::url($foo);
```



## utf8()

Ensure the string is encoded as UTF-8, convert if necessary, and strip invalid UTF garbage. Numeric and boolean values are ignored.

#### Arguments

 * (*mixed*) String. If an array is passed, each value is recursively sanitized.

#### Returns

Returns a valid UTF-8 string or `""` by value, otherwise `TRUE`.



## whitespace()

Trim, collapse horizontal whitespace to a single `" "`, convert vertical whitespace to `"\n"`, and collapse vertical whitespace to the specified number allowed.

#### Arguments

 * (*mixed*) String. If an array is passed, each value is recursively sanitized.
 * (*int*) (*optional*) Newlines. If `0`, vertical whitespace will be converted to a horizontal space and collapsed, otherwise contiguous vertical whitespace in excess of this value will be removed. Default: `0`

#### Returns

Returns the sanitized string by value, otherwise `TRUE`.

#### Example

```php
//by value
$foo = blobfolio\common\sanitize::whitespace('Happy  Birthday'); //Happy Birthday

//by reference
blobfolio\common\ref\sanitize::whitespace($foo);
```



## whitespace_multiline()

An alias of `whitespace()` with a default Newlines value of `1`.



## zip5()

Ensure a 5-digit ZIP string by chopping off +4 or zero-padding short entries.

#### Arguments

 * (*mixed*) ZIP. If an array is passed, each value is recursively sanitized.

#### Returns

Returns a 5-digit ZIP Code or `""` by value, otherwise `TRUE`.

#### Example

```php
//by value
$foo = blobfolio\common\sanitize::zip5('12345+6789'); //12345

//by reference
blobfolio\common\ref\sanitize::zip5($foo);
```
