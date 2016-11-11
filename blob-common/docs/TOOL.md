# Reference: Main Tools & Functions

This guide documents the main tools and helper functions provided by Tutan Common. The code can be located in `functions-tool.php`.



##### Table of Contents

 * [common_array_compare()](#common_array_compare)
 * [common_array_map_recursive()](#common_array_map_recursive)
 * [common_array_pop()](#common_array_pop)
 * [common_array_pop_top()](#common_array_pop_top)
 * [common_generate_random_string()](#common_generate_random_string)
 * [common_get_cc_exp_months()](#common_get_cc_exp_months)
 * [common_get_cc_exp_years()](#common_get_cc_exp_years)
 * [common_iarray_key_exists()](#common_iarray_key_exists)
 * [common_iin_array()](#common_iin_array)
 * [common_isubstr_count()](#common_isubstr_count)
 * [common_parse_args()](#common_parse_args)
 * [common_parse_json_args()](#common_parse_json_args)
 * [common_random_int()](#common_random_int)
 * [common_strlen()](#common_strlen)
 * [common_switcheroo()](#common_switcheroo)



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



## common_iarray_key_exists()

Case-insensitive `array_key_exists()`.

#### Arguments

 * (*mixed*) Needle
 * (*array*) Haystack

#### Return

Returns `TRUE` or `FALSE`.



## common_iin_array()

Case-insensitive `in_array()`.

#### Arguments

 * (*mixed*) Needle
 * (*array*) Haystack

#### Return

Returns `TRUE` or `FALSE`.



## common_isubstr_count()

Case-insensitive `substr_count()`.

#### Arguments

 * (*array*) Haystack
 * (*mixed*) Needle

#### Return

Returns integer of count.



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



## common_switcheroo()

Swap two variables.

#### Arguments

 * (*mixed*) (*reference*) Var One
 * (*mixed*) (*reference*) Var Two

#### Return

The variables are passed by reference. This function always returns `TRUE`.