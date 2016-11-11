# Reference: Just-In-Time (JIT) Image Thumbnails

By default, WordPress will generate every possible thumbnail size for every image that is uploaded. This is resource-intensive and wasteful on many fronts.

The following JIT functionality alters this behavior so that thumbnails are only generated if and when a particular image/size is actually requested. They are still saved to the server the usual way (i.e. this isn't like `timthumb`), but only if they are needed.

JIT does not require any modifications to your code; you only need to enable it. There are, however, a couple [Gotchas](#gotchas) noted at the end of this document that you should be aware of.



##### Table of Contents

 * [Enable](#enable)
 * [Gotchas](#gotchas)



## Enable

Add the following to your `wp-config.php`:

```php
//enable JIT thumbnail management
define('WP_JIT_IMAGES', true);
```



## Gotchas

#### Exceptions

WordPress will still automatically generate the three default thumbnail sizes immediately after upload. These sizes are integrated too deeply into the core to safely postpone.

#### Code Compatibility

Tutan Common maintains full compatibility with all WP src and srcset functions (and by extension, any function that uses those functions). You will only need to make changes to your code if you are doing something weird, like manually crawling a directory.

#### Plugin Conflicts

Certain image plugins may run into conflicts when JIT is enabled and/or prevent JIT from working properly. Some common examples are noted below, but in general, you'll want to avoid any plugin which attempts to cycle through every size in existence:

 * Advanced Custom Fields - ACF itself is not a problem, however you should avoid defining any image fields that return an object or array, as such data requires ACF to run through every possible size, thus generating every possible size. Instead, you should have image fields return only the attachment ID.
 * Post Thumbnail Editor - Like ACF, PTE will run through every possible image size and thus generate every single thumbnail size.

Lastly, you should be careful with plugins that attempt to regenerate thumbnails or replace media. Most of these are sloppily written and can corrupt the attachment metadata, which in turn can result in 404 errors.

If you need to regenerate the thumbnails site-wide, we recommend the `wp-cli` command line tool.