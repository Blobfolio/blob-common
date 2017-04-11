# Reference: Main Tools & Functions

This guide documents the main tools and helper functions provided by Tutan Common. The code can be located in `functions-tool.php`.



##### Table of Contents


 * Arrays
   * [common_iarray_key_exists()](#common_iarray_key_exists)
   * [common_iin_array()](#common_iin_array)   
 * Strings    
   * [common_isubstr_count()](#common_isubstr_count)   



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
