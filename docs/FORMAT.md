# Formatting

blob-common contains helpers for formatting data for display.

Many of these are available in both by-value and by-reference versions. The functionality is identical either way, except the former returns a copy of the original variable, while the latter modifies the original variable in place.

**Namespace:**
`blobfolio\common\format`
`blobfolio\common\ref\format`

**Use:**
```php
//by value
$foo = blobfolio\common\format::money('$1.00');

//by reference
blobfolio\common\ref\format::money($foo);
```



##### Table of Contents

 * [array_to_indexed()](#array_to_indexed)
 * [cidr_to_range()](#cidr_to_range)
 * [decode_entities()](#decode_entities)
 * [excerpt()](#excerpt)
 * [inflect()](#inflect)
 * [ip_to_number()](#ip_to_number)
 * [links()](#links)
 * [money()](#money)
 * [phone()](#phone)
 * [to_csv()](#to_csv)
 * [to_timezone()](#to_timezone)
 * [to_xls()](#to_xls)



## array_to_indexed()

Rebuild a key=>value array so that each value is an array containing the original key and value. This can be helpful if exporting associative array data to Javascript, for example.

#### Versions

 * By Value
 * By Reference

#### Arguments

 * (*array*) Array

#### Returns

If passing by value a new array is returned, otherwise `TRUE`.

#### Example

```php
$arr = array(
    'fruit'=>'apple',
    'vegetable'=>'carrot'
);
print_r(blobfolio\common\format::array_to_indexed($arr));
/*
array(
    0 => array(
        [key] => fruit,
        [value] => apple
    ),
    1 => array(
        [key] => vegetable,
        [value] => carrot
    )
)
*/
```



## cidr_to_range()

Convert a CIDR to a minimum/maximum range of IPs.

#### Arguments

 * (*string*) CIDR

#### Returns

Returns an array with `"min"` and `"max"` IPs or `FALSE` on failure.

#### Example

```php
print_r(blobfolio\common\format::cidr_to_range('2600:3c00::f03c:91ff:feae:0ff2/64'));
/*
array(
    [min] => 2600:3c00::f03c:91ff:feae:ff2,
    [max] => 2600:3c00::ffff:ffff:ffff:ffff
)
*/
```



## decode_entities()

Convert as many HTML entities to their character counterpoints as possible with as many passes as it takes to get them all.

#### Versions

 * By Value
 * By Reference

#### Arguments

 * (*string*) HTML

#### Returns

If passing by value a decoded string is returned, otherwise `TRUE`.

#### Example

```php
\blobfolio\common\format::decode_entities('&quot;Happy Days&quot;'); //"Happy Days"
```



## excerpt()

Shorten text to a set number of letters or words. This function is multi-byte safe provided `mbstring` is present.

#### Arguments

 * (*string*) String
 * (*array*) (*optional*) Options
   * (*int*) Length. Default: `200`
   * (*string*) Suffix. Default: `"…"`
   * (*string*) Unit. Either `"character"` or `"word"`. Default: `"character"`

#### Returns

Returns the original or truncated string.

#### Example

```php
$str = "Hey good lookin'";
$foo = blobfolio\common\format::excerpt($str, array('length'=>5, 'unit'=>'character')); //Hey g…
$foo = blobfolio\common\format::excerpt($str, array('length'=>2, 'unit'=>'word')); //Hey good…
```



## inflect()

Choose between a singular and plural string given a numerical value. `printf()` formatting is supported.

#### Arguments

 * (*mixed*) Count. If an array is passed, the count of the array is used. Otherwise the value should be numeric.
 * (*string*) Singular.
 * (*string*) Plural.

#### Returns

Returns the singular string if the count is `1`, otherwise the plural string.

#### Example

```php
echo "I have " . \blobfolio\common\format::inflect(1, '%d book', '%d books'); //I have 1 book
```



## ip_to_number()

Convert an IPv4 or IPv6 address to its numerical equivalent. IPv6 addresses require `bcmath` to convert.

#### Versions

 * By Value
 * By Reference

#### Arguments

 * (*string*) IP

#### Returns

If passed by value, returns the numerical IP address of `FALSE`, otherwise `TRUE`/`FALSE`.

#### Example

```php
//by value
$foo = blobfolio\common\format::ip_to_number('50.116.18.174'); //846467758

//by reference
blobfolio\common\ref\format::ip_to_number($foo);
```



## links()

Convert plain-text URLs, domains, email addresses, and telephone numbers into clickable (HTML) links.

Malformed data will be left alone, as will any non-FQDN domain or email address.

Unicode hosts are supported if the PHP extension `INTL` is installed, but the resulting `href` attribute will be converted to Punycode/ASCII.

People are very inconsistent at how they write telephone numbers. This looks specifically for international formatting (`+1 201-555-0123`), or common North American 10-digit representations like `(201) 555-0123`, `201-555-0123`, `201.555.0123`, etc. Other types of numbery bits, or phone-ish strings that don't validate, will be ignored.

#### Versions

 * By Value
 * By Reference

#### Arguments

 * (*string*) Text
 * (*array*) (*optional*) Attributes: an associative array with any of the following keys: `"class"`, `"rel"`, `"target"`. Class may be an `array` or a `string`; the others should contain `string` values. Any attributes provided will be added to the generated links. Default: `NULL`

#### Returns

If passed by value, returns the text with added links, otherwise `TRUE`.

#### Example

```php
//by value
$foo = blobfolio\common\format::links('Welcome to domain.com!'); //Welcome to <a href="http://domain.com">domain.com</a>!

$args = array(
  'target'=>'_blank'
);
$foo = blobfolio\common\format::links('me@domain.com', $args); //<a href="mailto:me@domain.com" target="_blank">me@domain.com</a>

//by reference
blobfolio\common\ref\format::links($foo);
```



## money()

Format a value as US currency.

#### Versions

 * By Value
 * By Reference

#### Arguments

 * (*float*) Amount
 * (*bool*) (*optional*) Cents. Whether sub-dollar values should be printed as `"50¢"` or `"$0.50"`. Default: `FALSE`
 * (*string*) (*optional*) Thousands separator. Default `""`
 * (*bool*) (*optional*) Remove trailing `.00`. Default `FALSE`

#### Returns

If passing by value, returns the formatted amount, otherwise `TRUE`.

```php
//by value
$foo = blobfolio\common\format::money(.75, true); //75¢
$foo = blobfolio\common\format::money(.75, false); //$0.75

//by reference
blobfolio\common\ref\format::money($foo);
```



## phone()

Format and verify a phone number using international formatting. This uses `libphonenumber` for extra strength goodness.

#### Versions

 * By Value
 * By Reference

#### Arguments

 * (*string*) Phone number
 * (*string*) (*optional*) Country (suspected). Providing a suspected country of origin helps with identification. Default: `"US"`
 * (*array*) (*optional*) Optionally require the number be of a certain type or types:
   * `"fixed"`, i.e. landline
   * `"mobile"`
   * `"pager"`
   * `"personal_number"`
   * `"premium_rate"`, e.g. your favorite party line
   * `"shared_cost"`
   * `"toll_free"`
   * `"voicemail"`
   * `"voip"`, e.g. Google Voice

#### Returns

Returns `""` if the number is invalid, otherwise the number in proper international format. If passing by reference `TRUE` or `FALSE` is returned.

#### Example

```php
//by value
$foo = blobfolio\common\format::phone('(555) 618-2086'); //+1 555-608-2086

//by reference
blobfolio\common\ref\format::phone($foo);
```



## to_csv()

Convert a dataset to CSV format.

#### Arguments

 * (*array*) Data. This should be an array of arrays, the outer being rows, the inner being columns.
 * (*array*) (*optional*) Headers. If provided, a header row will be inserted with these values. Otherwise if the inner arrays of the dataset are associative, the keys will be used. Otherwise no header will be printed. Default: `NULL`
 * (*string*) (*optional*) Delimiter. Default: `","`
 * (*string*) (*optional*) Row separator. Default: `"\n"`

#### Returns

Returns a string containing the CSV content.

#### Example

```php
$data = array(
    array('John','Doe','01/01/2000'),
    array('Jane','Doe','12/25/1998')
);
$headers = array('First', 'Last', 'Joined');

$csv = blobfolio\common\format::to_csv($data, $headers);
```



## to_timezone()

Convert a datestring from  one timezone to another.

#### Versions

 * By Value
 * By Reference

#### Arguments

 * (*mixed*) Date or timestamp
 * (*string*) (*optional*) Old TZ. Default: `"UTC"`
 * (*string*) (*optional*) New TZ. Default: `"UTC"`

#### Example

```php
//by value
$foo = blobfolio\common\format::to_timezone('2015:01:01 10:00:00', 'UTC', 'America/Los_Angeles'); //2015-01-01 02:00:00

//by value
blobfolio\common\ref\format::to_timezone($foo, 'UTC', 'America/Chicago');
```



## to_xls()

Convert a dataset to Microsoft Excel's XML format. Special cell formatting will be used for boolean, numeric, percent, currency, and date values.

#### Arguments

 * (*array*) Data. This should be an array of arrays, the outer being rows, the inner being columns.
 * (*array*) (*optional*) Headers. If provided, a header row will be inserted with these values. Otherwise if the inner arrays of the dataset are associative, the keys will be used. Otherwise no header will be printed. Default: `NULL`

#### Returns

Returns a string containing the XML/XLS content.

#### Example

```php
$data = array(
    array('John','Doe','01/01/2000'),
    array('Jane','Doe','12/25/1998')
);
$headers = array('First', 'Last', 'Joined');

$xls = blobfolio\common\format::to_xls($data, $headers);
```
