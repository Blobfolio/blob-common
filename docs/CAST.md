# Typecasting

blob-common contains improved (smarter, safer) type-handling methods.

Many of these are available in both by-value and by-reference versions. The functionality is identical either way, except the former returns a cast copy of the original variable, while the latter modifies the original variable in place.

**Namespace:**
`blobfolio\common\cast`
`blobfolio\common\ref\cast`

**Use:**
```php
//by value
$foo = blobfolio\common\cast::to_int($foo);

//by reference
blobfolio\common\ref\cast::to_int($foo);
```



##### Table of Contents

 * [array_type()](#array_type)
 * [to_array()](#to_array)
 * [to_bool()](#to_bool)
 * [to_float()](#to_float)
 * [to_int()](#to_int)
 * [to_number()](#to_number)
 * [to_string()](#to_string)
 * [to_type()](#to_type)



## array_type()

This function attempts to classify the indexing method of an array.

#### Arguments

 * (*array*) (*reference*) Array

#### Returns

 * `"sequential"` if the keys are numeric and in sequence beginning with `0`
 * `"indexed"` if the keys are otherwise numeric
 * `"associative"` if the keys are mixed or non-numeric
 * `FALSE` if the array is empty

#### Example

```php
$arr1 = array(
    'apples',
    'bananas',
    'carrots'
);

echo blobfolio\common\cast::array_type($arr1); //sequential
```



## to_array()

Typecast to array. This is like `(array)` hinting, but will set non-arrayable objects as empty arrays.

#### Versions

 * By Value
 * By Reference

#### Aliases

The following are available under PHP7+:

 * ::array()

#### Arguments

 * (*mixed*) Array

#### Returns

Returns an array if passed by value, otherwise `TRUE` if by reference.

#### Example

```php
//by value
$foo = blobfolio\common\cast::to_array('apples'); //array(0 => 'apples')
$foo = blobfolio\common\cast::to_array(null); //array()
$foo = blobfolio\common\cast::to_array(array('apples')); //array(0 => 'apples')

//by reference
blobfolio\common\ref\cast::to_array($foo);
```



## to_bool()

Typecast to boolean. This is like `(bool)` hinting, but will also properly interpret things like `"true"`, `"on"`, etc.

#### Versions

 * By Value
 * By Reference

#### Aliases

The following are available under PHP7+:

 * ::bool()
 * ::boolean()

#### Arguments 

 * (*mixed*) Bool. If an array is passed, each value will be recursively cast.
 * (*bool*) (*optional*) Flatten. `TRUE` overrides the auto-recursive behavior, making sure that only a single value is returned. Default: `FALSE`

#### Returns

Returns `TRUE` or `FALSE`.

#### Example

```php
//by value
$foo = blobfolio\common\cast::to_bool(true); //TRUE
$foo = blobfolio\common\cast::to_bool(1); //TRUE
$foo = blobfolio\common\cast::to_bool("true"); //TRUE

//by reference
blobfolio\common\ref\cast::to_bool($foo);
```



## to_float()

Typecast to float.  This is like `(float)` hinting, but will strip out non-numeric data and attempt to convert values like percents and US cents.

#### Versions

 * By Value
 * By Reference

#### Aliases

The following are available under PHP7+:

 * ::double()
 * ::float()

#### Arguments

 * (*mixed*) Float. If an array is passed, each value will be recursively cast.
 * (*bool*) (*optional*) Flatten. `TRUE` overrides the auto-recursive behavior, making sure that only a single value is returned. Default: `FALSE`

#### Returns

Returns a float if passed by value, otherwise `TRUE`.

#### Example

```php
//by value
$foo = blobfolio\common\cast::to_float('$1.00'); //1
$foo = blobfolio\common\cast::to_float('10%'); //.1
$foo = blobfolio\common\cast::to_float(5.5); //5.5

//by reference
blobfolio\common\ref\cast::to_float($foo);
```



## to_int()

Typecast to integer.  This is like `(int)` hinting, but again, will strip out non-numeric data and attempt to convert values like percents, etc. This function will also properly interpret things like `"true"`, `"on"`, etc.

#### Versions

 * By Value
 * By Reference

#### Aliases

The following are available under PHP7+:

 * ::int()
 * ::integer()

#### Arguments

 * (*mixed*) Int. If an array is passed, each value will be recursively cast.
 * (*bool*) (*optional*) Flatten. `TRUE` overrides the auto-recursive behavior, making sure that only a single value is returned. Default: `FALSE`

#### Returns

Returns an integer if passed by value, otherwise `TRUE`.

#### Example

```php
//by value
$foo = blobfolio\common\cast::to_int(5.5); //5
$foo = blobfolio\common\cast::to_int('3'); //3
$foo = blobfolio\common\cast::to_int(8); //8

//by reference
blobfolio\common\ref\cast::to_int($foo);
```



## to_number()

This strips out non-numerical data and returns a float.

#### Versions

 * By Value
 * By Reference

#### Aliases

The following are available under PHP7+:

 * ::number()

#### Arguments

 * (*mixed*) Number. If an array is passed, each value will be recursively cast.
 * (*bool*) (*optional*) Flatten. `TRUE` overrides the auto-recursive behavior, making sure that only a single value is returned. Default: `FALSE`

#### Returns

Returns a float if passed by value, otherwise `TRUE`.

#### Example

```php
//by value
$foo = blobfolio\common\cast::to_number('5.5'); //5.5
$foo = blobfolio\common\cast::to_number(3); //3
$foo = blobfolio\common\cast::to_number('8%'); //0.08

//by reference
blobfolio\common\ref\cast::to_number($foo);
```



## to_string()

This casts to a UTF-8 string.

#### Versions

 * By Value
 * By Reference

#### Aliases

The following are available under PHP7+:

 * ::string()

#### Arguments

 * (*mixed*) String. If an array is passed, each value will be recursively cast.
 * (*bool*) (*optional*) Flatten. `TRUE` overrides the auto-recursive behavior, making sure that only a single value is returned. Default: `FALSE`

#### Returns

Returns a string if passed by value, otherwise `TRUE`.

#### Example

```php
//by value
$foo = blobfolio\common\cast::to_string('1'); //1

//by reference
blobfolio\common\ref\cast::to_string($foo);
```



## to_type()

This typecasts the variable to the specified type, mapping to one of the above functions.

#### Versions

 * By Value
 * By Reference

#### Arguments

 * (*mixed*) Variable. If an array is passed an the type is not `"array"`, each value will be recursively cast.
 * (*string*) Type: `"boolean"`, `"bool"`, `"integer"`, `"int"`, `"double"`, `"float"`, `"string"`, or `"array"`
 * (*bool*) (*optional*) Flatten. `TRUE` overrides the auto-recursive behavior, making sure that only a single value is returned. Default: `FALSE`

#### Returns

Returns the cast variable if passed by value, otherwise `TRUE`. 
