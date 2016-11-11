# Reference: Date, Time, Space, File Handling, etc. Functions

This guide documents functions having to do (broadly) with space and time. The code can be located in `functions-spacetime.php`.



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
   * [common_get_path_by_url()](#common_get_path_by_url)
   * [common_get_site_hostname()](#common_get_site_hostname)
   * [common_get_url_by_path()](#common_get_url_by_path)
   * [common_is_current_page()](#common_is_current_page)
   * [common_is_empty_dir()](#common_is_empty_dir)
   * [common_is_site_url()](#common_is_site_url)
   * [common_redirect()](#common_redirect)
   * [common_theme_path()](#common_theme_path)
   * [common_upload_path()](#common_upload_path)
 * Time
   * [common_datediff()](#common_datediff)



## common_get_ca_provinces()

Return an array of Canadian provinces for e.g. a checkout form. This function originally returned all values in uppercase, but that can now be disabled by passing `FALSE`.

#### Arguments

 * (*bool*) (*optional*) Uppercase. Default `TRUE`

#### Return

Returns a key=>value array. The keys are the two-digit postal abbreviations, values are the full names.



## common_get_countries()

Return an array of (most) official countries. Note: Unlike the state/province functions, names are returned in title case by default.

#### Arguments

 * (*bool*) (*optional*) Uppercase. Default `FALSE`

#### Return

Returns a key=>value array. The keys are the two-digit ISO codes, values are the full names.



## common_get_us_states()

Returns an array of US states, and optionally not-quite-states that the post office delivers to anyway. This function originally returned all values in uppercase, but that can now be disabled by passing `FALSE`.

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

Returns a data-uri string or `FALSE` if the file doesn't exist or can't be opened.



## common_get_mime_type()

Why is this so damn hard? PHP's `fileinfo` extension is not reliably present, and even when it is it kinda sucks. WP's built-in function is missing a ton. Oh well, if you need more complete and reliable MIME types by file extension, use this function.

#### Arguments

 * (*string*) Path

#### Return

Returns the file's appropriate MIME type or `"application/octet-stream"` if it can't figure it out.



## common_readfile_chunked()

If you are buffering files through PHP, doing it in chunks can greatly reduce the overhead. This will read and output the file in 1MB chunks.

#### Arguments

 * (*string*) Path
 * (*bool*) (*optional*) Return Bytes (like `readfile()` does). Default `TRUE`

#### Return

Echoes the (binary-safe) file contents. Returns `TRUE` or `FALSE`.



## common_cidr_to_range()

Obtain the Min and Max IP from a Netblock.

#### Arguments

 * (*string*) CIDR

#### Return

Returns an array containing the min and max IP (keyed thusly), or `FALSE` if invalid.



## common_ip_to_number()

Convert an IPv4 or IPv6 address to its numerical equivalent. You will need a 64-bit operating system to handle IPv6 numbers, most likely.

#### Arguments

 * (*string*) IP

#### Return

Returns the numerical equivalent or `FALSE` if invalid.



## common_get_path_by_url()

This is a simple function that will attempt to convert a blog URL to the corresponding path. Obviously this won't work if you are using rewrite trickery, but it will also fail if you are sloppy with your base URLs (e.g. using "domain.com" and "www.domain.com" interchangeably).

#### Arguments

 * (*string*) URL

#### Return

Returns the (nonconfirmed) path or `FALSE`.



## common_get_site_hostname()

This returns the lowercase hostname portion of your blog URL, minus any leading "www.".

#### Arguments

 * N/A

#### Return

Returns the site hostname.



## common_get_url_by_path()

This is a simple function that will attempt to convert a path (within your WP base) to its corresponding URL.

#### Arguments

 * (*string*) Path

#### Return

Returns the (nonconfirmed) URL or `FALSE`.



## common_is_current_page()

Checks whether the page being viewed matches the URL you are passing.

#### Arguments

 * (*string*) URL
 * (*bool*) Match subpages. Default `FALSE`

#### Return

Returns `TRUE` or `FALSE`.



## common_is_empty_dir()

Checks whether a directory is empty or not.

#### Arguments

 * (*string*) Dir

#### Return

Returns `TRUE` if `$dir` is a directory and empty, otherwise `FALSE`.



## common_is_site_url()

Checks whether a URL's domain matches the blog's domain. Note: this will fail if you use "domain.com" and "www.domain.com" interchangeably.

#### Arguments

 * (*string*) URL

#### Return

Returns `TRUE` if the URL's domain matches the site's domain. If the passed URL contains invalid characters or has some other domain, it will return `FALSE`.



## common_redirect()

This is a more robust version of `wp_redirect()`. It will redirect users via Javascript if headers have already been sent. It unsets `$_POST`, `$_GET`, and `$_REQUEST` superglobals (to help prevent form resubmissions on reload). It also self-exits, so you can redirect in one step instead of two.

#### Arguments

 * (*string*) (*optional*) URL. Default `site_url()`
 * (*bool*) (*optional*) Allow off-site redirect. Default `FALSE`

#### Return

This function redirects the user to the specified URL. If off-site URLs are not allowed and the passed value is off-site, the user will be redirected to the home page instead.



## common_theme_path()

This is more or less like `site_url()` for your theme folder.

#### Arguments

 * (*string*) (*optional*) Sub-path. Default `NULL`
 * (*bool*) (*optional*) Return URL. If `FALSE` a file path is returned. Default `FALSE`

#### Return

Returns the (nonconfirmed) path or URL in your theme root.



## common_upload_path()

This is more or less like `site_url()` for your upload folder.

#### Arguments

 * (*string*) (*optional*) Sub-path. Default `NULL`
 * (*bool*) (*optional*) Return URL. If `FALSE` a file path is returned. Default `FALSE`

#### Return

Returns the (nonconfirmed) path or URL in your upload root.



## common_datediff()

Calculate the number of days between two dates.

#### Arguments

 * (*string*) Date One
 * (*string*) Date Two

#### Return

Returns an integer representing the number of days between two dates. `DateTime` is preferred, but if unavailable the value will be approximated by counting up the seconds. The latter can be inaccurate if a range contains a Daylight Saving adjustment.