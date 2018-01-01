# Tutan Common

A WordPress plugin containing various functions to aid complex theme development. This plugin bundles the [blob-common](https://github.com/Blobfolio/blob-common) PHP library, so all of those capabilities can be used as well.



##### Table of Contents

1. [Features](#features)
2. [Installation](#installation)
3. [Reference](#reference)
4. [License](#license)



## Features

Tutan Common is a collection of under-the-hood functions and behavioral extensions for WordPress developers. There is a lot here! Refer to the [reference section](#reference) below for detailed documentation on each and every function.



## Installation

Because Tutan Common is a developer resource rather than something end users can directly play with, it is not part of the main WordPress plugin repository. Therefore it must be manually installed.

The current release requires PHP 5.6+ and several additional modules. Refer to the [requirements](https://github.com/Blobfolio/blob-common#requirements) for `blob-common`. If you meet these standards, download the [current release](https://raw.githubusercontent.com/Blobfolio/blob-common/master/release/blob-common.zip).

Plugin updates will be delivered the usual way through WordPress Admin; no special effort required.



## Reference

All functions are prefixed with `common_` or `_common_` to help separate namespace. You can override or replace any function by declaring it before Tutan Common is loaded, either inside `wp-config.php` or a plugin with a higher priority.

Some functionality is toggled by constants defined in `wp-config`. They are explained in more detail in the corresponding function documentation, but quickly, they are:

 * (*array*) **WP_CLEAN_SVG** - Default arguments to use when not explicitly passing any arguments to `common_get_clean_svg()`.
 * (*bool*) **WP_DB_DEBUG_LOG** - Log database query errors to `wp-content/db-debug.log`
 * (*string*) **WP_DEBUG_EMAIL** - Send debug emails to an address other than the site email
 * (*string*) **WP_DEBUG_LOG_CAP** - Capability required to view `Tools > Debug Log`; Default: `"manage_options"`
 * (*bool*) **WP_DISABLE_EMOJI** - Disable WP's emoji scripts and styles
 * (*bool*) **WP_DISABLE_JQUERY_MIGRATE** - Disable WP's jQuery Migrate script
 * (*bool*) **WP_JIT_IMAGES** - Enable just-in-time thumbnail generation
 * (*string*) **WP_WEBP_CWEBP** - Override default bin path for `cwebp`
 * (*string*) **WP_WEBP_GIF2WEBP** - Override default bin path for `gif2webp`
 * (*bool*) **WP_WEBP_IMAGES** - Enable WebP features

To make digestion easier, the function documentation is broken up into the following broad categories:

 * [Behavior/System](https://github.com/Blobfolio/blob-common/blob/master/wp/docs/BEHAVIOR.md)
 * [Debugging](https://github.com/Blobfolio/blob-common/blob/master/wp/docs/DEBUG.md)
 * [Email](https://github.com/Blobfolio/blob-common/blob/master/wp/docs/EMAIL.md)
 * [Forms](https://github.com/Blobfolio/blob-common/blob/master/wp/docs/FORM.md)
 * [Images](https://github.com/Blobfolio/blob-common/blob/master/wp/docs/IMAGE.md)
   * [JIT Thumbnails](https://github.com/Blobfolio/blob-common/blob/master/wp/docs/JIT.md)
   * [WebP](https://github.com/Blobfolio/blob-common/blob/master/wp/docs/WEBP.md)
 * [Spacetime](https://github.com/Blobfolio/blob-common/blob/master/wp/docs/SPACETIME.md)
 * [Misc Tools](https://github.com/Blobfolio/blob-common/blob/master/wp/docs/TOOL.md)
 * [Misc Wrappers (Deprecated)](https://github.com/Blobfolio/blob-common/blob/master/wp/docs/DEPRECATED.md)



## License

Copyright © 2018 [Blobfolio, LLC](https://blobfolio.com) &lt;hello@blobfolio.com&gt;

This work is free. You can redistribute it and/or modify it under the terms of the Do What The Fuck You Want To Public License, Version 2.

    DO WHAT THE FUCK YOU WANT TO PUBLIC LICENSE
    Version 2, December 2004
    
    Copyright (C) 2004 Sam Hocevar <sam@hocevar.net>
    
    Everyone is permitted to copy and distribute verbatim or modified
    copies of this license document, and changing it is allowed as long
    as the name is changed.
    
    DO WHAT THE FUCK YOU WANT TO PUBLIC LICENSE
    TERMS AND CONDITIONS FOR COPYING, DISTRIBUTION AND MODIFICATION
    
    0. You just DO WHAT THE FUCK YOU WANT TO.