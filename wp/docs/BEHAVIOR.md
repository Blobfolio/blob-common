# Reference: Behavioral/System

This guide documents functions focusing on core WordPress behaviors. The code can be located in `functions-behavior.php`.

Note: some behaviors are modified automatically by activating this plugin, but only when the defaults are *really* annoying or potentially dangerous. ;)



##### Table of Contents

 * Automatic
   * [common_cron_schedules()](#common_cron_schedules)
   * [common_disable_checked_to_top()](#common_disable_checked_to_top)
   * [common_disable_wp_embed()](#common_disable_wp_embed)
   * [common_svg_media_thumbnail()](#common_svg_media_thumbnail)
   * [common_upload_mimes()](#common_upload_mimes)
   * [common_upload_real_mimes()](#common_upload_real_mimes)
   * [Other](#other)
 * [WP_DISABLE_JQUERY_MIGRATE](#wp_disable_jquery_migrate)
 * [WP_DISABLE_EMOJI](#wp_disable_emoji)



## common_cron_schedules()

Extend pseudo-cron by adding the following intervals:

 * oneminute
 * twominutes
 * fiveminutes
 * tenminutes
 * halfhour

This function hooks into WordPress automatically; you don't need to do anything.

To ensure short intervals trigger reliably, you may need to disable WP's built-in triggering and use your system's CRON handler instead:

Add the following to `wp-config.php`:

```php
//disable automatic pseudo-cron triggering
define('DISABLE_WP_CRON', true);
```

And create a cronjob like:

```bash
# trigger WordPress pseudo-cron every minute
* * * * * wget https://domain.com/wp-cron.php?doing_wp_cron > /dev/null 2>&1
```



## common_disable_checked_to_top()

By default, WordPress bubbles a post's selected terms to the top of the list, making it very difficult to interpret hierarchical relations. Tutan Common automatically disables this; terms are displayed in their default order with no bubbling.



## common_disable_wp_embed()

WordPress adds a small script to every page to make it easier for other sites to embed your site. Nobody uses this, I swear! Tutan Common automatically disables this bloat.



## common_svg_media_thumbnail()

This enables SVG thumbnail support in the WordPress media library's grid view.

This function hooks into WordPress automatically; you don't need to do anything.



## common_upload_mimes()

This adds SVG and WebP to the list of MIME types whitelisted by WordPress for upload.

This function hooks into WordPress automatically; you don't need to do anything.



## common_upload_real_mimes()

WordPress `4.7.1` introduced [Magic MIME](https://en.wikipedia.org/wiki/File_format#Magic_number) detection as a security measure, but its implementation was flawed and as a result many valid files can no longer be uploaded.

blob-common automatically hooks into the `wp_check_filetype_and_ext` filter and if needed runs its own analysis, which is both more thorough and more conservative. If a file's true type doesn't match its extension, it will be renamed.

All uploads are still subject to the built-in whitelist of allowed types, so if your project requires something unusual (aside from SVG and WebP, which this plugin enables automatically), be sure to add it via the `upload_mimes` filter.

This function hooks into WordPress automatically; you don't need to do anything.



## Other

Tutan Common automatically disables the adjacent post meta tags WP inserts into the document header.

```php
//do not include back/next links in meta
add_filter('previous_post_rel_link', '__return_false');
add_filter('next_post_rel_link', '__return_false');
```



## WP_DISABLE_JQUERY_MIGRATE

To prevent WordPress from injecting jQuery Migrate into your pages, add the following to `wp-config.php`:

```php
//disable jQuery Migrate
define('WP_DISABLE_JQUERY_MIGRATE', true);
```



## WP_DISABLE_EMOJI

To prevent WordPress from injecting emoji scripts and styles into your pages, add the following to `wp-config.php`:

```php
//disable WP emoji
define('WP_DISABLE_EMOJI', true);
```
