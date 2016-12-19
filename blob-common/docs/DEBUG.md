# Reference: Debugging Tools

This guide documents functions which help developers debug issues. The code can be located in `functions-debug.php`.



##### Table of Contents

 * [common_db_debug_log()](#common_db_debug_log)
 * [common_debug_mail()](#common_debug_mail)
 * [Debug Log Viewer](#debug-log-viewer)



## common_db_debug_log()

This function logs MySQL query errors to `wp-content/db_debug.log`. To enable this behavior, add the following to `wp-config.php`:

```php
//enable MySQL error log
define('WP_DB_DEBUG_LOG', true);
```

If using this on production environments, please remember to restrict access to the log file.

Note: this can be used independently of the main `WP_DEBUG*` constants.



## common_debug_mail()

`print_r()` and `var_dump()` are great functions for gaining quick insight into what is going on, however depending on where the code is being executed, printing it to the screen may not be an option. This function instead sends that data via email so as not to interrupt the execution of the code.

By default messages are sent to the site administrator's email, but this can be changed by adding the following to `wp-config.php`:

```php
//set debug recipient
define('WP_DEBUG_EMAIL', 'debugger@domain.com');
```

#### Arguments

 * (*mixed*) Variable - The variable you want to see in more detail
 * (*string*) (*optional*) Subject - The email subject. Default: `NULL`
 * (*bool*) (*optional*) Prefer `mail()` - Use `mail()` instead of `wp_mail()` for sending. Default: `TRUE`
 * (*bool*) (*optional*) Prefer `var_dump()` - Use `var_dump()` instead of `print_r()`. Default: `FALSE`

#### Return

This function always returns `TRUE`.

#### Example

```php
$transaction = \Authnet\Charge::($args);
common_debug_mail($transaction, 'Debug Transaction');
```



## Debug Log Viewer

Tutan Common automatically adds an administrative page accessible via `Tools > Debug Log` to allow developers to view the contents of the `debug.log` file. Log entries are color-coded and filterable to make life a bit easier.

This feature is particular useful when direct access to the `debug.log` file is prohibited by the server environment (which is recommended!).

By default this menu entry is only available to users with "manage_options" capabilities (i.e. admins), but can be set to require a different capability by adding the following to `wp-config.php`:

```php
//Tools > Debug Log capability
define('WP_DEBUG_LOG_CAP', 'some_other_capability');
```
