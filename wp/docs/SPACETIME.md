# Reference: Date, Time, Space, File Handling, etc. Functions

This guide documents functions having to do (broadly) with space and time. The code can be located in `functions-spacetime.php`.



##### Table of Contents

 * Paths and URLs
   * [common_get_path_by_url()](#common_get_path_by_url)
   * [common_get_site_hostname()](#common_get_site_hostname)
   * [common_get_url_by_path()](#common_get_url_by_path)
   * [common_is_current_page()](#common_is_current_page)
   * [common_is_site_url()](#common_is_site_url)
   * [common_redirect()](#common_redirect)
   * [common_theme_path()](#common_theme_path)
   * [common_upload_path()](#common_upload_path)
 * Time
   * [common_datediff()](#common_datediff)
   * [common_get_blog_timezone()](#common_get_blog_timezone)
   * [common_from_blogtime()](#common_from_blogtime)
   * [common_to_blogtime()](#common_to_blogtime)



## common_get_path_by_url()

This is a simple function that will attempt to convert a blog URL to the corresponding path. Obviously this won't work if you are using rewrite trickery, but it will also fail if you are sloppy with your base URLs (e.g. using "domain.com" and "www.domain.com" interchangeably).

PS: www is evil.

#### Arguments

 * (*string*) URL

#### Return

Returns the (nonconfirmed) path or `FALSE`.



## common_get_site_hostname()

This returns the lowercase hostname portion of your blog URL, minus any leading "www.". This can be handy in generating an email address, for example.

#### Arguments

 * N/A

#### Return

Returns the site hostname.



## common_get_url_by_path()

This is a simple function that will attempt to convert a path (within your WP base) to its corresponding URL. If the path is outside `ABSPATH` it will fail.

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



## common_is_site_url()

Checks whether a URL's domain matches the blog's domain. Note: this will fail if you use "domain.com" and "www.domain.com" interchangeably.

PS: www is evil.

#### Arguments

 * (*string*) URL

#### Return

Returns `TRUE` if the URL's domain matches the site's domain. If the passed URL contains invalid characters or has some other domain, it will return `FALSE`.



## common_redirect()

This is a more robust version of `wp_redirect()`:
 * It will redirect users via Javascript if headers have already been sent;
 * It unsets `$_POST`, `$_GET`, and `$_REQUEST` superglobals (to help prevent form resubmissions on reload);
 * For security reasons, it will not redirect users to off-site locations (this can be toggled);
 * It also self-exits, so you can redirect in one step instead of two;

#### Arguments

 * (*int|string*) (*optional*) Post ID or URL. Default `site_url()`
 * (*bool*) (*optional*) Allow off-site redirect. Default `FALSE`

#### Return

This function redirects the user to the specified URL. If off-site URLs are not allowed and the passed value is off-site, the user will be redirected to the home page instead.



## common_theme_path()

This is more or less like `site_url()` for your theme folder.

#### Arguments

 * (*string*) (*optional*) Sub-path. Default `NULL`
 * (*bool*) (*optional*) Return as URL. If `FALSE` a file path is returned. Default `FALSE`

#### Return

Returns the (nonconfirmed) path or URL in your theme root.



## common_upload_path()

This is more or less like `site_url()` for your upload folder.

#### Arguments

 * (*string*) (*optional*) Sub-path. Default `NULL`
 * (*bool*) (*optional*) Return as URL. If `FALSE` a file path is returned. Default `FALSE`

#### Return

Returns the (nonconfirmed) path or URL in your upload root.



## common_datediff()

Calculate the number of days between two dates.

#### Arguments

 * (*string*) Date One
 * (*string*) Date Two

#### Return

Returns an integer representing the number of days between two dates. `DateTime` is preferred, but if unavailable the value will be approximated by counting up the seconds. The latter can be inaccurate if a range contains a Daylight Saving adjustment.



## common_get_blog_timezone()

Returns the blog's timezone as a string, e.g. `"America/Los_Angeles"`.

#### Arguments

N/A

#### Return

Returns the blog's timezone as a string, using (in order of priority) WP's timezone setting or a GMT offset. On failure it assumes UTC.



## common_from_blogtime()

Convert a datetime string in the blog's timezone to another timezone. Note: this requires `DateTime`.

#### Arguments

 * (*string*) Date
 * (*string*) (*optional*) New Timezone. Default: `"UTC"`

#### Return

This will return the datetime string in the specified timezone.



## common_to_blogtime()

Convert a datetime string in one timezone to the blog's local timezone. Note: this requires `DateTime`.

#### Arguments

 * (*string*) Date
 * (*string*) (*optional*) Original Timezone. Default: `"UTC"`

#### Return

This wil return the datetime string in the local timezone.