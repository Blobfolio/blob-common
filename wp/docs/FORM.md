# Reference: Web Form Functions

This guide documents functions which can be incorporated into web forms. The code can be located in `functions-form.php`.



##### Table of Contents

 * [common_get_form_timestamp()](#common_get_form_timestamp)
 * [common_check_form_timestamp()](#common_check_form_timestamp)



## common_get_form_timestamp()

This function returns a hashed timestamp you can add to your form to help prevent robots from submitting data too quickly.

#### Arguments

 * N/A

#### Return

This function returns a string you can include in your form. You do not need to wrap the value in `esc_attr()`; it will only ever contain letters, numbers, and a comma.

#### Example

```html
<input type="hidden" name="timestamp" value="<?=common_get_form_timestamp()?>" />
```

#### Aliases

 * *common_generate_form_timestamp()*



## common_check_form_timestamp()

This function validates the timestamp hash passed with your form data.

#### Arguments

 * (*string*) Timestamp hash
 * (*int*) Minimum time elapsed in seconds. Default: `5`

#### Return

This function returns `FALSE` if the hash is invalid or the form was submitted faster than the time specified. Otherwise it returns `TRUE`.

#### Example

```php
//verify the submitted timestamp
$timestamp = isset($_POST['timestamp']) ? $_POST['timestamp'] : '';
if(!common_check_form_timestamp($timestamp)){
    $errors[] = new WP_Error(...);
}
```

#### Aliases

 * *common_verify_form_timestamp()*
