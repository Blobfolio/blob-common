# Reference: Misc Wrappers (DEPRECATED)

For historical reasons, Tutan Common contains various functions for data handling that now just point to `blob-common` functions. These functions will remain in the plugin base for the foreseeable future so as not to break sites that use them, however new projects should just call `blob-common` functions directly.



##### Table of Contents

 * Geography
   * [common_get_ca_provinces()](#common_get_ca_provinces)
   * [common_get_countries()](#common_get_countries)
   * [common_get_us_states()](#common_get_us_states)
 * File Handling
   * [common_get_data_uri()](#common_get_data_uri)
   * [common_get_mime_type()](#common_get_mime_type)
   * [common_readfile_chunked()](#common_readfile_chunked)
 * IPs
   * [common_cidr_to_range()](#common_cidr_to_range)
   * [common_ip_to_number()](#common_ip_to_number)
 * Paths and URLs
   * [common_is_empty_dir()](#common_is_empty_dir)
 * Misc Data
   * [common_array_compare()](#common_array_compare)
   * [common_array_map_recursive()](#common_array_map_recursive)
   * [common_array_pop()](#common_array_pop)
   * [common_array_pop_top()](#common_array_pop_top)
   * [common_array_type()](#common_array_type)
   * [common_generate_random_string()](#common_generate_random_string)
   * [common_get_cc_exp_months()](#common_get_cc_exp_months)
   * [common_get_cc_exp_years()](#common_get_cc_exp_years)
   * [common_parse_args()](#common_parse_args)
   * [common_parse_json_args()](#common_parse_json_args)
   * [common_random_int()](#common_random_int)
   * [common_strlen()](#common_strlen)
   * [common_strpos()](#common_strpos)
   * [common_substr()](#common_substr)
   * [common_substr_count()](#common_substr_count)
   * [common_switcheroo()](#common_switcheroo)
 * (re)Formatting
   * [common_array_to_indexed()](#common_array_to_indexed)
   * [common_format_money()](#common_format_money)
   * [common_format_phone()](#common_format_phone)
   * [common_get_excerpt()](#common_get_excerpt)
   * [common_inflect()](#common_inflect)
   * Case
     * [common_strtolower()](#common_strtolower)
     * [common_strtoupper()](#common_strtoupper)
     * [common_ucfirst()](#common_ucfirst)
     * [common_ucwords()](#common_ucwords)
   * Paths
     * [common_leadingslashit()](#common_leadingslashit)
     * [common_unixslashit()](#common_unixslashit)
     * [common_unleadingslashit()](#common_unleadingslashit)
   * Spreadsheets
     * [common_to_csv()](#common_to_csv)
     * [common_to_xls()](#common_to_xls)
 * Sanitization
   * [common_sanitize_array()](#common_sanitize_array)
   * [common_sanitize_bool()](#common_sanitize_bool)
   * [common_sanitize_by_type()](#common_sanitize_by_type)
   * [common_sanitize_csv()](#common_sanitize_csv)
   * [common_sanitize_date()](#common_sanitize_date)
   * [common_sanitize_datetime()](#common_sanitize_datetime)
   * [common_sanitize_domain_name()](#common_sanitize_domain_name)
   * [common_sanitize_email()](#common_sanitize_email)
   * [common_sanitize_float()](#common_sanitize_float)
   * [common_sanitize_int()](#common_sanitize_int)
   * [common_sanitize_ip()](#common_sanitize_ip)
   * [common_sanitize_js_variable()](#common_sanitize_js_variable)
   * [common_sanitize_name()](#common_sanitize_name)
   * [common_sanitize_newlines()](#common_sanitize_newlines)
   * [common_sanitize_number()](#common_sanitize_number)
   * [common_sanitize_phone()](#common_sanitize_phone)
   * [common_sanitize_printable()](#common_sanitize_printable)
   * [common_sanitize_quotes()](#common_sanitize_quotes)
   * [common_sanitize_spaces()](#common_sanitize_spaces)
   * [common_sanitize_string()](#common_sanitize_string)
   * [common_sanitize_whitespace()](#common_sanitize_whitespace)
   * [common_sanitize_url()](#common_sanitize_url)
   * [common_sanitize_zip5()](#common_sanitize_zip5)
   * [common_to_range()](#common_to_range)
   * [common_utf8()](#common_utf8)
 * Validation
   * [common_in_range()](#common_in_range)
   * [common_is_utf8()](#common_is_utf8)
   * [common_length_in_range()](#common_length_in_range)
   * [common_validate_cc()](#common_validate_cc)
   * [common_validate_domain_name()](#common_validate_domain_name)
   * [common_validate_email()](#common_validate_email)
   * [common_validate_phone()](#common_validate_phone)



## common_array_to_indexed()

Convert an associative array to an indexed array for e.g. easier handling in Javascript.

#### Arguments

 * (*array*) Array

#### Return

Returns an array with each value containing an array containing the original key and value (keyed thusly).

```php
array(
    0 => array(
        'key'=>'original key',
        'value'=>'original value'
    ),
    1 => array(
        'key'=>'another original key',
        'value'=>'another original value'
    ) ...
)
```



## common_format_money()

Format a number in US dollars.

#### Arguments

 * (*mixed*) Number
 * (*bool*) (*optional*) Use ¢ sign if the amount is under $1. Default `FALSE`

#### Return

Returns the amount formatted in USD, e.g. `"$1.00"` or `"59¢"`.

#### Filters

 * `common_format_money` - Accepts the same arguments. The eponymous function runs with a priority of 5. You can enqueue additional callbacks before or after to alter its behavior and use `apply_filters()` instead of calling the function directly. You can also dequeue the main function if, for example, you wanted to introduce other localities.



## common_format_phone()

A very simple function for trying to format 10-digit North American phone numbers. For more in depth phone number formatting and validation, check out `libphonenumber` ports for PHP.

#### Arguments

 * (*string*) Phone

#### Return

Returns a phone number formatted as follows: (123) 456-7890 x123456.



## common_get_ca_provinces()

Return an array of Canadian provinces for e.g. a checkout form. This function originally returned all values in uppercase, but that can now be disabled by passing `FALSE`.

#### Arguments

 * (*bool*) (*optional*) Uppercase. Default `TRUE`

#### Return

Returns a key=>value array. The keys are the two-digit postal abbreviations, values are the full names.



## common_get_excerpt()

Generate an excerpt from a string based on letter or word length.

#### Arguments

 * (*string*) String
 * (*int*) (*optional*) Length. Default `200`
 * (*string*) (*optional*) Suffix if truncated. Default `"..."`
 * (*string*) (*optional*) Count method, either `"chars"` or `"words"`. Default `"chars"`

#### Return

Returns the original string or a shortened version if it was too long. Note: this function strips tags and reduces whitespace to try and prevent waste or broken output. It is still recommended you encode entities before printing.



## common_get_countries()

Return an array of (most) official countries. Note: Unlike the state/province functions, names are returned in title case by default, but passing `TRUE` will return them in uppercase.

#### Arguments

 * (*bool*) (*optional*) Uppercase. Default `FALSE`

#### Return

Returns a key=>value array. The keys are the two-digit ISO codes, values are the full names.



## common_get_us_states()

Returns an array of US states and optionally not-quite-states that the post office delivers to anyway. This function originally returned all values in uppercase, but that can now be disabled by passing `FALSE`.

#### Arguments

 * (*bool*) (*optional*) Include US territories, military addresses, etc. Default `TRUE`
 * (*bool*) (*optional*) Uppercase. Default `FALSE`

#### Return

Returns a key=>value array. The keys are the two-digit postal abbreviations, values are the full names.



## common_get_data_uri()

Convert a file to a base64-encoded data-uri string.

#### Arguments

 * (*string*) Path

#### Return

Returns a data-uri string or `FALSE` if the file doesn't exist or can't be read.



## common_readfile_chunked()

If you are buffering files through PHP, doing it in chunks can greatly reduce the overhead. This will read and output a file in 1MB chunks. Note: you will still need to send the appropriate headers ahead of calling this function.

#### Arguments

 * (*string*) Path
 * (*bool*) (*optional*) Return Bytes (like `readfile()` does). Default `TRUE`

#### Return

Echoes the (binary-safe) file contents. Returns `TRUE` or `FALSE`.



## common_get_mime_type()

PHP's `fileinfo` extension is not reliably present and sucks anyway. WP's `wp_check_filetype()` is only really meant for uploaded files and so is missing a ton of data.

TL;DR if you need more complete and reliable extension-to-MIME conversion, use this function instead.

#### Arguments

 * (*string*) Path

#### Return

Returns the file's MIME type or `"application/octet-stream"` if it can't figure it out.



## common_cidr_to_range()

Obtain the Min and Max IP from a Netblock.

#### Arguments

 * (*string*) CIDR

#### Return

Returns an array containing the min and max IP (keyed thusly), or `FALSE` if invalid.



## common_ip_to_number()

Convert an IPv4 or IPv6 address to its numerical equivalent. You will need a 64-bit operating system to handle the massive IPv6 numbers, most likely.

#### Arguments

 * (*string*) IP

#### Return

Returns the numerical equivalent or `FALSE` if invalid.



## common_is_empty_dir()

Checks whether a directory is empty or not.

#### Arguments

 * (*string*) Dir

#### Return

Returns `TRUE` if `$dir` is a directory and empty, otherwise `FALSE`.



## common_inflect()

Return the singular or plural version of a string given the count. `sprintf()` formatting is allowed.

#### Arguments

 * (*int*) Count
 * (*string*) Singular
 * (*string*) Plural

#### Return

Returns the singular or plural version of a string given the count.

#### Example

```php
echo 'I have read ' . common_inflect(5, '%d book', '%d books') . ' this year.';
```



## common_leadingslashit()

Like WP's `trailingslashit()` function but for the front.

#### Arguments

 * (*string*) Path

#### Return

Return path with a leading slash attached (if not already there).



## common_strtolower()

Convert a string to lower case, prefering multi-byte safe operations when `mbstring` support exists. It will also catch additional unicode characters with case distinctions like `Ⅸ` to `ⅸ`.

#### Arguments

 * (*string*) String

#### Return

Return the string in lower case. If `mbstring` is not present, accented characters, etc., will probably be ignored.



## common_strtoupper()

Convert a string to upper case, prefering multi-byte safe operations when `mbstring` support exists. It will also catch additional unicode characters with case distinctions like `ⅸ` to `Ⅸ`.

#### Arguments

 * (*string*) String

#### Return

Return the string in upper case. If `mbstring` is not present, accented characters, etc., will probably be ignored.



## common_to_csv()

Convert an array of data to a CSV.

#### Arguments

 * (*array*) Data. this should be an `array` of `array`s, rows => cells
 * (*array*) (*optional*) Headers. If provided, a row will be inserted at the top of the document containing these values. If absent, a header row can be built using the array keys of the first data row (if that inner array is associative). If neither apply, no header row will be inserted.
 * (*string*) (*optional*) Delimiter. Default: `","`
 * (*string*) (*optional*) Line separator. Default: `\n`

#### Return

A string containing the data in CSV format.

#### Example

```php
//header labels
$headers = array(
    'PRODUCT ID',
    'PRODUCT NAME',
    'PRICE'
);

//data
$data = array(
    array(
        12345,
        'Applesauce',
        4.99
    ),
    array(
        67890,
        'Banana',
        1.25
    )
);

$csv = common_to_csv($data, $headers);
```



## common_to_xls()

Convert an array of data to a Microsoft Excel document. Note: this returns data in [XML format](https://en.wikipedia.org/wiki/Microsoft_Office_XML_formats), which may be incompatible with *really* ancient versions of Microsoft Office.

#### Arguments

 * (*array*) Data. this should be an `array` of `array`s, rows => cells
 * (*array*) (*optional*) Headers. If provided, a row will be inserted at the top of the document containing these values. If absent, a header row can be built using the array keys of the first data row (if that inner array is associative). If neither apply, no header row will be inserted.

#### Return

A string containing the data in XML format. The following cell formats are automatically detected:

 * `Currency` (US): `"$5.00"`  or `"99&cent;"`
 * `General Date`: `"2015-01-03 12:30:33"`
 * `Long Time`: `"12:30:33"`
 * `Percent`: `"12%"`
 * `Short Date`: `"2015-01-03"`
 * `True/False`: `TRUE`
 * `Number`: any other kind of numeric value
 * `String`: everything else

#### Example

```php
//header labels
$headers = array(
    'PRODUCT ID',
    'PRODUCT NAME',
    'PRICE'
);

//data
$data = array(
    array(
        12345,
        'Applesauce',
        4.99
    ),
    array(
        67890,
        'Banana',
        1.25
    )
);

$xls = common_to_xls($data, $headers);
```



## common_ucfirst()

Convert a string to sentence case, prefering multi-byte safe operations when `mbstring` support exists. It will also catch additional unicode characters with case distinctions like `ⅸ` to `Ⅸ`.

As with the built-in `ucfirst()` function, this does not affect quoted content.

#### Arguments

 * (*string*) String

#### Return

Return the string in sentence case. If `mbstring` is not present, accented characters, etc., will probably be ignored.



## common_ucwords()

Convert a string to title case (e.g. the first letter of each word is upper case), prefering multi-byte safe operations when `mbstring` support exists. It will also catch additional unicode characters with case distinctions like `ⅸ` to `Ⅸ`.

As with the built-in `ucfirst()` function, this does not affect quoted content.

#### Arguments

 * (*string*) String

#### Return

Return the string in title case. If `mbstring` is not present, accented characters, etc., will probably be ignored.



## common_unixslashit()

Replace evil Windows-style backslashes with forward slashes and remove pointless bits like "//" or "/./".

#### Arguments

 * (*string*) Path

#### Return

Return path with proper Unix slashes.



## common_unleadingslashit()

Like WP's `untrailingslashit()` but for the front.

#### Arguments

 * (*string*) Path

#### Return

Return path without any leading slashes.



## common_sanitize_array()

Typecast value as array.

#### Arguments

 * (*mixed*) Value

#### Return

Returns an array.



## common_sanitize_bool()

Typecast as boolean, but additionally catch values like `"1"` and `"true"`.

#### Arguments

 * (*mixed*) Value
 * (*bool*) (*optional*) Flatten. `TRUE` overrides the auto-recursive behavior, making sure that only a single value is returned. Default: `FALSE`

#### Return

Returns a boolean.

#### Aliases

 * *common_sanitize_boolean()*



## common_sanitize_by_type()

Pass the value to the appropriate `common_sanitize_X()` function based on the specified type. Allowed types are:

 * array
 * bool
 * boolean
 * double
 * float
 * int
 * integer
 * string

#### Arguments

 * (*mixed*) Value
 * (*string*) Type
 * (*bool*) (*optional*) Flatten. `TRUE` overrides the auto-recursive behavior, making sure that only a single value is returned. Default: `FALSE`

#### Return

Returns the sanitized value according to the specified type. If the type is invalid, the value is returned unaltered.



## common_sanitize_csv()

Sanitize a string so it is safe to be quoted in a typical CSV. Quotes are standardized (goodbye crooked quotes). Double quotes are escaped with a backslash, all white space is reduced to a single horizontal space.

#### Arguments

 * (*string*) Value

#### Return

Returns the sanitized string.



## common_sanitize_date()

Return a Unix-style date string, e.g. YYYY-MM-DD. Invalid dates are returned as `"0000-00-00"` rather than the dawn of 1970.

#### Arguments

 * (*string|int*) Date

#### Return

Returns a date.



## common_sanitize_date()

Return a Unix-style datetime string, e.g. YYYY-MM-DD HH:MM:SS. Invalid dates are returned as `"0000-00-00 00:00:00"` rather than the dawn of 1970.

#### Arguments

 * (*string|int*) Datetime

#### Return

Returns a datetime.



## common_sanitize_domain_name()

This attempts to pull the hostname from any URL-like string. Along the way it will strip out most invalid characters. Note: this will only work with standard ASCII domains.

#### Arguments

 * (*string*) URL/Domain/Etc.

#### Return

Return the hostname in lowercase.



## common_sanitize_email()

In addition to WP's `sanitize_email()` filters, this also strips out quotes and apostrophes, which let's be honest, don't really belong even though they are technically allowed.

#### Arguments

 * (*string*) Email

#### Return

Returns the email in lowercase.



## common_sanitize_float()

Typecast as a float, stripping out non-numbery things.

#### Arguments

 * (*mixed*) Number
 * (*bool*) (*optional*) Flatten. `TRUE` overrides the auto-recursive behavior, making sure that only a single value is returned. Default: `FALSE`

#### Return

Return the sanitized number.

#### Aliases

 * *common_doubleval()*
 * *common_floatval()*



## common_sanitize_int()

Typecast as an integer, stripping out non-numbery things.

#### Arguments

 * (*mixed*) Number
 * (*bool*) (*optional*) Flatten. `TRUE` overrides the auto-recursive behavior, making sure that only a single value is returned. Default: `FALSE`

#### Return

Returns the sanitized number.

#### Aliases

 * *common_intval()*



## common_sanitize_ip()

Sanitize/format an IP address. IPv6 in particular can be written any number of ways; this ensures all values are compacted and lowercased.

#### Arguments

 * (*string*) IP

#### Return

Returns the IP.



## common_sanitize_js_variable()

This is similar to WP's `esc_js()` but also standardizes quotes (no more slanty nonsense) and removes line breaks and excess horizontal whitespace.

#### Arguments

 * (*mixed*) Value

#### Return

Returns the escaped value.



## common_sanitize_name()

Sanitize e.g. a person's name. This function isn't perfect, but helps add a bit of sanity to a value that can otherwise run wild. This strips out everything but whitespace, letters, dashes, numbers, quotes, apostrophes, commas, and periods. Whitespace is reduced to a single horizontal space, and Title Case is imposed.

#### Arguments

 * (*string*) Name

#### Return

Returns the sanitized name.



## common_sanitize_newlines()

Convert all vertical whitespace to Unix line breaks (`\n`), trim lines of leading/trailing whitespace, and collapse gratuitous verticality.

#### Arguments

 * (*string*) String
 * (*int*) (*optional*) Max consecutive linebreaks. Default: `2`

#### Return

Return the sanitized string.



## common_sanitize_number()

Strip non-numbery bits and return a proper float. This will also convert a value like `50¢` to `.5`.

#### Arguments

 * (*mixed*) Number
 * (*bool*) (*optional*) Flatten. `TRUE` overrides the auto-recursive behavior, making sure that only a single value is returned. Default: `FALSE`

#### Return

Returns a (float) number.



## common_sanitize_phone()

A simple attempt to sanitize a 10-digit North American phone number. Non-digits are removed. If the end result is 11-digits and begins with a 1, the leading 1 is removed.

For more in depth phone number formatting and validation, check out `libphonenumber` ports for PHP.

#### Arguments

 * (*string*) Phone

#### Return

Return the possibly-a-phone-number.



## common_sanitize_printable()

Remove non-printable characters from a string. Note: this might behave differently from one server environment to another. Test carefully!

#### Arguments

 * (*string*) String

#### Return

Return the printable characters.



## common_sanitize_quotes()

Try to convert all the different slanty quotes and apostrophes with their straight versions. (Slanty quotes mess up everything!)

#### Arguments

 * (*string*) String

#### Return

Return the string with regular quotes.



## common_sanitize_spaces()

Replace all horizontal whitespace with a single regular space.

#### Arguments

 * (*string*) String

#### Return

Return the sanitized string.



## common_sanitize_string()

Typecast as a UTF-8 string (and convert encoding if necessary).

#### Arguments

 * (*mixed*) Array/String
 * (*bool*) (*optional*) Flatten. `TRUE` overrides the auto-recursive behavior, making sure that only a single value is returned. Default: `FALSE`

#### Return

Return the sanitized string.

#### Aliases

 * *common_strval()*



## common_sanitize_whitespace()

Sanitize both horizontal and vertical whitespace.

#### Arguments

 * (*string*) String
 * (*mixed*) (*optional*) Max consecutive newlines. Default `FALSE` (i.e. no linebreaks allowed)

#### Return

Return the sanitized string.



## common_sanitize_url()

Strip invalid characters and ensure a scheme exists (e.g. `http://`). URLs beginning `//` will be prefixed with `https:`.

#### Arguments

 * (*string*) URL

#### Return

Return the sanitized string. Invalid URLs are returned as an empty string.



## common_sanitize_zip5()

Sanitize and 0-pad a 5-digit US ZIP Code. If a ZIP+4 is passed, the +4 is stripped off.

#### Arguments

 * (*string*) ZIP

#### Return

Returns a 5-digit ZIP Code or an empty string on failure.



## common_to_range()

Ensure a value falls within a given range. This can be a string or a number.

#### Arguments

 * (*mixed*) Value
 * (*mixed*) (*optional*) Min. Default `NULL`
 * (*mixed*) (*optional*) Max. Default `NULL`

#### Return

If Min is passed, the value returned will be greater or equal to it. If Max is passed, the value returned will be less than or equal to that.



## common_utf8()

Use Sebastián Grignoli's excellent Force-UTF8 library to convert/fix string encoding.

#### Arguments

 * (*string*) String

#### Return

Bools, numbers, and empty strings are passed through unchanged. Otherwise the function returns a valid UTF-8 string or `FALSE` on failure.

#### Aliases

 * *common_sanitize_utf8()*



## common_in_range()

Check if a value is within a defined range.

#### Arguments

 * (*mixed*) Value
 * (*mixed*) (*optional*) Min
 * (*mixed*) (*optional*) Max

#### Return

Returns `TRUE` or `FALSE`.



## common_is_utf8()

Checks whether the string is valid UTF-8.

#### Arguments

 * (*string*) String

#### Return

Returns `TRUE` if the passed value is an empty string or valid UTF-8; `FALSE` if not.



## common_length_in_range()

Check if a string's length is within a defined range. This is multi-byte safe when PHP is compiled with the `mbstring` module.

#### Arguments

 * (*string*) String
 * (*int*) (*optional*) Minimum length
 * (*int*) (*optional*) Maximum length

#### Return

Returns `TRUE` or `FALSE`.



## common_validate_cc()

Validate a credit card number's formatting.

#### Arguments

 * (*string*) Card Number

#### Return

Returns `TRUE` if the card number is formatted correctly or `FALSE` if not.



## common_validate_domain_name()

Checks whether a string is a valid ASCII domain.

#### Arguments

 * (*string*) Domain
 * (*bool*) (*optional*) Registered? If `TRUE`, it will attempt to pull DNS information. Default `TRUE`

#### Return

Returns `TRUE` or `FALSE`. If checking DNS and no A records are returned, the function will return `FALSE`. Note: the function does not attempt to resolve any IP returned.



## common_validate_email()

Checks to see if an email contains valid characters (e.g. *FILTER_VALIDATE_EMAIL*) and a FDQN.

#### Arguments

 * (*string*) Email

#### Return

Returns `TRUE` or `FALSE`.



## common_validate_phone()

Checks whether a phone number matches generic North American formatting rules. For more in depth phone number formatting and validation, check out `libphonenumber` ports for PHP.

#### Arguments

 * (*string*) Phone

#### Return

Returns `TRUE` or `FALSE`.



## common_array_compare()

This function attempts to check whether two arrays are equal. It does this by comparing each key one at a time, recursively. Note: certain types of non-iterable objects might prevent this from working.

#### Arguments

 * (*array*) (*reference) Array One
 * (*array*) (*reference) Array Two

#### Return

Returns `TRUE` or `FALSE`.



## common_array_map_recursive()

Recursively apply a callback function (without breaking array keys). Non-iterable objects are returned as-is. Other handling is up to the callback function.

#### Arguments

 * (*callable*) Function (by value)
 * (*mixed*) Variable

#### Return

Returns the filtered variable.



## common_array_pop()

Return the last value of an array like `array_pop()`, but without altering the original variable.

#### Arguments

 * (*array*) (*reference*) Array

#### Return

Returns the last value of the array or `FALSE` if not possible.



## common_array_pop_top()

Return the first value of an array without altering the original variable.

#### Arguments

 * (*array*) (*reference*) Array

#### Return

Returns the first value of the array or `FALSE` if not possible.



## common_array_type()

Return the "type" of indexing used by an array.

#### Arguments

 * (*array*) (*reference*) Array

#### Return

 * `"sequential"`: the indexes are squential, e.g. `0`, `1`, `2`, ...
 * `"indexed"`: the indexes are numeric, e.g. `0`, `5`, `6`, ...
 * `"associative"`: one or more indexes are strings
 * `FALSE`: the variable is not an array or has no length



## common_generate_random_string()

Generate a random string.

#### Arguments

 * (*int*) (*optional*) Length. Default `10`
 * (*array*) (*optional*) Characters to use. Defaults to unambiguous uppercase letters and numbers

#### Return

Returns a random string of characters from the "soup" of the chosen length.



## common_get_cc_exp_months()

Return months of the year for e.g. a credit card expiration field.

#### Arguments

 * (*string*) (*optional*) Value format (using `date()` syntax). Default `"m - M"`

#### Return

Returns a key=>value array of months. The key is the month as an integer (e.g. `1`), the value is the corresponding date string.



## common_get_cc_exp_years()

Returns years for e.g. a credit card expiration field.

#### Arguments

 * (*int*) (*optional*) Number of years to return, beginning with the current. Default `10`

#### Return

Returns a key=>value array of years. The keys and values are both integer values of the 4-digit year, e.g. `2000`.



## common_parse_args()

A `wp_parse_args()` wrapper on steroids. The primary difference is it returns an array containing all (and only) keys from the default. It can also optionally typecast values or parse args recursively.

#### Arguments

 * (*array*) User Args
 * (*array*) Defaults
 * (*bool*) (*optional*) Typecast user args to match tye default's type. Be careful with numbers. If the default has a value of `NULL`, no typecasting is performed. Default `FALSE`
 * (*bool*) (*optional*) Recursive. If `TRUE` and the default's value at a given index is a populated array, it will run the corresponding user args back through the function. Default `FALSE`

#### Return

Returns the default array with overrides provided by the user args.



## common_parse_args_json()

The same as `common_parse_args()` except the user args can be passed as a JSON string.

#### Arguments

 * (*JSON|array*) User Args
 * (*array*) Defaults
 * (*bool*) (*optional*) Typecast user args to match tye default's type. Be careful with numbers. If the default has a value of `NULL`, no typecasting is performed. Default `FALSE`
 * (*bool*) (*optional*) Recursive. If `TRUE` and the default's value at a given index is a populated array, it will run the corresponding user args back through the function. Default `FALSE`

#### Return

Returns the default array with overrides provided by the user args.



## common_random_int()

Generate a random number. The function prefers the modern `random_int()` function but will fall back to `mt_rand()` if necessary.

#### Arguments

 * (*int*) Min
 * (*int*) Max

#### Return

Returns a random integer between Min and Max.



## common_strlen()

Returns the (multi-byte safe) length of a string if PHP supports `mbstring`, otherwise it will fall back to `strlen()`.

#### Arguments

 * (*string*) String

#### Return

Return the number of characters if `mbstring` is supported, otherwise the number of bytes (which often amounts to the same thing).



## common_strpos()

Returns the (multi-byte safe) substring position if PHP supports `mbstring`, otherwise it will fall back to `strpos()`.

#### Arguments

 * (*string*) Haystack
 * (*string*) Needle
 * (*int*) (*optional*) Offset

#### Return

Return the substring position using `mb_strpos()` if supported, otherwise `strpos()`. `FALSE` is returned on failure.



## common_substr()

Returns the (multi-byte safe) substring if PHP supports `mbstring`, otherwise it will fall back to `substr()`.

#### Arguments

 * (*string*) String
 * (*int*) Start
 * (*int*) (*optional*) Length

#### Return

Return the substring using `mb_substr()` if supported, otherwise `substr()`.



## common_substr_count()

Returns the (multi-byte safe) substring count if PHP supports `mbstring`, otherwise it will fall back to `substr_count()`.

#### Arguments

 * (*string*) Haystack
 * (*string*) Needle

#### Return

Return the substring count using `mb_substr_count()` if supported, otherwise `substr_count()`.



## common_switcheroo()

Swap two variables.

#### Arguments

 * (*mixed*) (*reference*) Var One
 * (*mixed*) (*reference*) Var Two

#### Return

The variables are passed by reference. This function always returns `TRUE`.