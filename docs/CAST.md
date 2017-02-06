# Typecasting

blob-common contains improved (smarter, safer) type-handling methods.

Many of these are available in both by-value and by-reference versions. The functionality is identical either way, except the former returns a cast copy of the original variable, while the latter modifies the original variable in place.

**Namespace:**
`blobfolio\common\cast`
`blobfolio\common\ref\cast`

**Use:**
```php
//by value
$foo = blobfolio\common\cast::int($foo);

//by reference
blobfolio\common\ref\cast::int($foo);
```



##### Table of Contents

 * [array()](#array)
 * [array_type()](#array_type)
 * [bool()](#bool)
 * [float()](#float)
 * [int()](#int)
 * [number()](#number)
 * [to_type()](#to_type)



## array()

Typecast to array. This is like `(array)` hinting, but will set non-arrayable objects as empty arrays.

#### Versions

 * By Value
 * By Reference

#### Arguments

 * (*mixed*) Array

#### Returns

Returns an array if passed by value, otherwise `TRUE` if by reference.

#### Example

```php
//by value
$foo = blobfolio\common\cast::array('apples'); //array(0 => 'apples')
$foo = blobfolio\common\cast::array(null); //array()
$foo = blobfolio\common\cast::array(array('apples')); //array(0 => 'apples')

//by reference
blobfolio\common\ref\cast::array($foo);
```



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



## bool()

Typecast to boolean. This is like `(bool)` hinting, but will also properly interpret things like `"true"`, `"on"`, etc.

#### Versions

 * By Value
 * By Reference

#### Arguments 

 * (*mixed*) Bool. If an array is passed, each value will be recursively cast.
 * (*bool*) (*optional*) Flatten. `TRUE` overrides the auto-recursive behavior, making sure that only a single value is returned. Default: `FALSE`

#### Returns

Returns `TRUE` or `FALSE`.

#### Example

```php
//by value
$foo = blobfolio\common\cast::bool(true); //TRUE
$foo = blobfolio\common\cast::bool(1); //TRUE
$foo = blobfolio\common\cast::bool("true"); //TRUE

//by reference
blobfolio\common\ref\cast::bool($foo);
```



## float()

Typecast to float.  This is like `(float)` hinting, but will strip out non-numeric data and attempt to convert values like percents and US cents.

#### Versions

 * By Value
 * By Reference

#### Arguments

 * (*mixed*) Float. If an array is passed, each value will be recursively cast.
 * (*bool*) (*optional*) Flatten. `TRUE` overrides the auto-recursive behavior, making sure that only a single value is returned. Default: `FALSE`

#### Returns

Returns a float if passed by value, otherwise `TRUE`.

#### Example

```php
//by value
$foo = blobfolio\common\cast::float('$1.00'); //1
$foo = blobfolio\common\cast::float('10%'); //.1
$foo = blobfolio\common\cast::float(5.5); //5.5

//by reference
blobfolio\common\ref\cast::float($foo);
```



## int()

Typecast to integer.  This is like `(int)` hinting, but again, will strip out non-numeric data and attempt to convert values like percents, etc.

#### Versions

 * By Value
 * By Reference

#### Arguments

 * (*mixed*) Int. If an array is passed, each value will be recursively cast.
 * (*bool*) (*optional*) Flatten. `TRUE` overrides the auto-recursive behavior, making sure that only a single value is returned. Default: `FALSE`

#### Returns

Returns an integer if passed by value, otherwise `TRUE`.

#### Example

```php
//by value
$foo = blobfolio\common\cast::int(5.5); //5
$foo = blobfolio\common\cast::int('3'); //3
$foo = blobfolio\common\cast::int(8); //8

//by reference
blobfolio\common\ref\cast::int($foo);
```



## number()

This strips out non-numerical data and returns a float.

#### Versions

 * By Value
 * By Reference

#### Arguments

 * (*mixed*) Number. If an array is passed, each value will be recursively cast.
 * (*bool*) (*optional*) Flatten. `TRUE` overrides the auto-recursive behavior, making sure that only a single value is returned. Default: `FALSE`

#### Returns

Returns a float if passed by value, otherwise `TRUE`.

#### Example

```php
//by value
$foo = blobfolio\common\cast::number('5.5'); //5.5
$foo = blobfolio\common\cast::number(3); //3
$foo = blobfolio\common\cast::number('8%'); //0.08

//by reference
blobfolio\common\ref\cast::number($foo);
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
