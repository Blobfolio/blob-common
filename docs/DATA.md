# General/Data

blob-common contains a lot of miscellaneous data-handling methods that don't necessarily fit into a more specific category.

**Namespace:**
`blobfolio\common\data`

**Use:**
```php
$arr = array('apples','bananas','carrots');
$first = blobfolio\common\data::array_pop_top($arr);
echo $first; //apples
```



##### Table of Contents

 * [array_compare()](#array_compare)
 * [array_map_recursive()](#array_map_recursive)
 * [array_pop()](#array_pop)
 * [array_pop_top()](#array_pop_top)
 * [cc_exp_months()](#cc_exp_months)
 * [cc_exp_years()](#cc_exp_years)
 * [datediff()](#datediff)
 * [in_range()](#in_range)
 * [is_json()](#is_json)
 * [is_utf8()](#is_utf8)
 * [json_decode_array()](#json_decode_array)
 * [length_in_range()](#length_in_range)
 * [parse_args()](#parse_args)
 * [random_int()](#random_int)
 * [random_string()](#random_string)
 * [switcheroo()](#switcheroo)
 * [unsetcookie()](#unsetcookie)



## array_compare()

Determine whether the contents (keys and values) of two arrays are equal. Sorting is ignored.

#### Arguments

 * (*array*) (*reference*) Array One
 * (*array*) (*reference*) Array Two

#### Returns

Returns `TRUE` if both arrays are equal, `FALSE` if they differ or if either are not an array.

#### Example

```php
$arr1 = array('apples','bananas','carrots');
$arr2 = array('bananas','carrots','apples');
var_dump(blobfolio\common\data::array_compare($arr1, $arr2)); //TRUE
```



## array_map_recursive()

Recursively apply a callback function to the contents of an array.

#### Arguments

 * (*function*) Callback
 * (*array*) Array

#### Returns

Returns the filtered array.



## array_pop()

Return the last element of an array, but without altering the array as the true `array_pop()` does.

#### Arguments

 * (*array*) (*reference*) Array

#### Returns

Returns the last value in the array, or `FALSE` if the array is empty.

#### Example

```php
$arr = array('apples','bananas','carrots');
$last = blobfolio\common\data::array_top($arr);
echo $last; //carrots
```



## array_pop_top()

Return the first element of an array, again without altering the original variable.

#### Arguments

 * (*array*) (*reference*) Array

#### Returns

Returns the first value in the array, or `FALSE` if the array is empty.

#### Example

```php
$arr = array('apples','bananas','carrots');
$first = blobfolio\common\data::array_top_pop($arr);
echo $first; //apples
```



## cc_exp_months()

Generate an array of months for e.g. use in a checkout form.

#### Arguments

 * (*string*) (*optional*) Value Format. Default: `"m - M"`

#### Returns

Returns an array of expiration months, keyed 1-12, values corresponding to the passed date format.

```php
print_r(blobfolio\common\data::cc_exp_months());
/*
array(
    1 => 01 - Jan,
    2 => 02 - Feb,
    ...
)
*/
```



## cc_exp_years()

Generate an array of years for e.g. use in a checkout form.

#### Arguments

 * (*int*) (*optional*) Length. Default: `10`

#### Returns

Returns an array of expiration years beginning with the current year.

```php
print_r(blobfolio\common\data::cc_exp_years());
/*
array(
    2010 => 2010,
    2011 => 2011,
    ...
)
*/
```



## datediff()

Calculate the number of days between two dates. This will use `DateTime` if available, but will fall back to counting up the seconds between the timestamps.

#### Arguments

 * (*mixed*) Date or timestamp
 * (*mixed*) Date or timestamp

#### Returns

Returns the number of days between the two values. If the dates are equal or either are invalid, `0` is returned.

#### Example

```php
echo blobfolio\common\data::datediff('2015-01-01', '2015-01-02'); //1
```



## in_range()

Determine whether a value falls within a given range.

#### Arguments

 * (*mixed*) Value
 * (*mixed*) (*optional*) Min. Default: `NULL`
 * (*mixed*) (*optional*) Max. Default: `NULL`

#### Returns

Returns `TRUE` or `FALSE`. Only passed boundaries are tested.

#### Example

```php
var_dump(blobfolio\common\data::in_range(3, 0, 300)); //TRUE
```



## is_json()

Determine whether a string represents JSON data.

#### Arguments

 * (*string*) String
 * (*bool*) (*optional*) Consider empty strings valid JSON. Default: `FALSE`

#### Returns

Returns `TRUE` or `FALSE`.



## is_utf8()

Determine whether a string is UTF-8.

#### Arguments

 * (*string*) String

#### Returns

Returns `TRUE` or `FALSE`.



## json_decode_array()

Decode a JSON string, make sure the result is an array, and optionally format its values against a template. See also: [parse_args()][#parse_args].

#### Arguments

 * (*string*) JSON
 * (*array*) (*optional*) Defaults
 * (*bool*) (*optional*) Strict. If `TRUE`, provided JSON values will be typecast according to the corresponding default. Default: `TRUE`
 * (*bool*) (*optional*) Recursive. If `TRUE`, provided JSON values will be parsed recusively when the corresponding default is an associative array. Default: `TRUE`

#### Returns

Returns an array, optionally parsed according to the provided defaults.

#### Example

```php
$json = '{"fruit":"apple","pet":dog}';
$defaults = array(
    'fruit'=>'banana',
    'vegetable'=>'carrot'
);
print_r(blobfolio\common\data::json_decode_array($json, $defaults));
/*
array(
    fruit => apple,
    vegetable => carrot
)
*/
```



## length_in_range()

Determine whether a string's length falls within a given range of characters. This function is multi-byte safe provided `mbstring` is present.

#### Arguments

 * (*string*) String
 * (*int*) (*optional*) Min. Default: `NULL`
 * (*int*) (*optional*) Max. Default: `NULL`

#### Returns

Returns `TRUE` or `FALSE`.

#### Example

```php
var_dump(blobfolio\common\data::length_in_range('The dog is cute.', 1, 10)); //FALSE
```



## parse_args()

Generate an array combining user arguments and expected defaults. The structure of the default takes priority; foreign keys from the user arguments are not included in the output.

#### Arguments

 * (*array*) Arguments
 * (*array*) Defaults
 * (*bool*) (*optional*) Strict. If `TRUE`, provided arguments are typecast according to the corresponding default. Default: `TRUE`
 * (*bool*) (*optional*) Recursive. If `TRUE`, provided arguments will be parsed recusively when the corresponding default is an associative array. Default: `TRUE`

#### Returns

Returns an array matching the default, with overrides courtesy of the passed arguments.

#### Example

```php
$args = array(
    'fruit'=>'apple',
    'pet'=>'dog'
);
$defaults = array(
    'fruit'=>'banana',
    'vegetable'=>'carrot'
);
print_r(blobfolio\common\data::parse_args($args, $defaults));
/*
array(
    fruit => apple,
    vegetable => carrot
)
*/
```



## random_int()

Generate a random integer using `random_int()` if available, otherwise `mt_rand()`.

#### Arguments

 * (*int*) (*optional*) Min. Default: `0`
 * (*int*) (*optional*) Max. Default: `1`

#### Returns

Returns a random integer between the bounds.



## random_string()

Generate a random string.

#### Arguments

 * (*int*) (*optional*) Length. Default: `10`
 * (*array*) (*optional*) Alphabet. If not provided, the random string will be built using unambiguous upper case letters and numbers (e.g. not `"I"` or `1`).

#### Returns

Returns a random string of the specified length.



## switcheroo()

Switch the values of two variables.

#### Arguments

 * (*mixed*) (*reference*) Variable One
 * (*mixed*) (*reference*) Variable Two

#### Returns

Returns `TRUE`.

#### Example

```php
$a = 'Hello';
$b = 'World';
blobfolio\common\data::switcheroo($a, $b);

echo "$a $b"; //World Hello
```



## unsetcookie()

Delete a cookie and unset the corresponding `$_COOKIE` superglobal. Note: this must be run before headers have been sent. For best results, the arguments should match those used during the original `setcookie()` call.

#### Arguments

 * (*string*) Name
 * (*string*) (*optional*) Path. Default: `""`
 * (*string*) (*optional*) Domain. Default: `""`
 * (*bool*) (*optional*) Secure. Default: `FALSE`
 * (*bool*) (*optional*) HTTP-only. Default: `FALSE`

#### Returns

Returns `TRUE` or `FALSE`. `TRUE` does not necessarily mean the cookie was successfully deleted, but rather that no show-stopping errors were thrown while trying.

#### Example

```php
//set a cookie
setcookie('foobar', 'Hello World');

//unset it
blobfolio\common\data::unsetcookie('foobar');
```
