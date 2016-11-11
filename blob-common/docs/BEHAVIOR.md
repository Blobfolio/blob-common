# Reference: Behavioral/System

This guide documents functions focusing on core WordPress behaviors. The code can be located in `functions-behavior.php`.

Note: some behaviors are modified automatically by activating this plugin, but only when they are *really* annoying or potentially dangerous. ;)



##### Table of Contents

 * Automatic
   * [common_cron_schedules()](#common_cron_schedules)
   * [common_disable_checked_to_top()](#common_disable_checked_to_top)
   * [common_svg_media_thumbnail()](#common_svg_media_thumbnail)
   * [common_upload_mimes()](#common_upload_mimes)
   * [Other](#other)
 * [WP_DISABLE_JQUERY_MIGRATE](#WP_DISABLE_JQUERY_MIGRATE)
 * [WP_DISABLE_EMOJI](#WP_DISABLE_EMOJI)



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
# Trigger WordPress pseudo-cron every minute
* * * * * wget https://domain.com/wp-cron.php?doing_wp_cron > /dev/null 2>&1
```



## common_disable_checked_to_top()

By default, WordPress bubbles taxonomy selections to the top of the list, making it very difficult to differentiate hierarchical relations. Tutan Common automatically disables this; terms are displayed in their default order with no bubbling.



## common_svg_media_thumbnail()

This enables SVG thumbnail support in the WordPress media library's grid view.

This function hooks into WordPress automatically; you don't need to do anything.



## common_upload_mimes()

This adds SVG and WebP to the list of MIME types whitelisted by WordPress for upload.

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