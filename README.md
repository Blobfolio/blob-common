# blob-common

A PHP library with handy, reusable functions for sanitizing, formatting, and manipulating data.

For information about the plugin Tutan Common, which repackages and extends these functions for WordPress environments, click [here](https://github.com/Blobfolio/blob-common/tree/master/wp/).

[![Build Status](https://travis-ci.org/Blobfolio/blob-common.svg?branch=master)](https://travis-ci.org/Blobfolio/blob-common)

&nbsp;

## Table of Contents

1. [Requirements](#requirements)
2. [Installation](#installation)
3. [Reference](https://github.com/Blobfolio/blob-common/wiki)
4. [License](#license)

&nbsp;

## Requirements

blob-common requires PHP 5.6+ with the following modules:

 * BCMath or GMP
 * DOM
 * Fileinfo
 * Filter
 * JSON
 * MBString
 * SimpleXML

UTF-8 is used for all string encoding. This could create conflicts on environments using something else.

The [WebP](https://github.com/Blobfolio/blob-common/wiki/Images) functionality additionally requires access to server-side `cwebp` and `gif2webp` binaries. See the linked reference for more details.

&nbsp;

## Installation

Use Composer:

```bash
composer require "blobfolio/blob-common:dev-master"
```

Or grab the compiled Phar and include it in your project:

```php
require_once('bin/blob-common.phar');
```

Note: The Phar is gzipped so PHP needs to have gzip capabilities to be able to read it. :)

&nbsp;

## License

Copyright © 2017 [Blobfolio, LLC](https://blobfolio.com) &lt;hello@blobfolio.com&gt;

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

### Donations

<table>
  <tbody>
    <tr>
      <td width="200"><img src="https://blobfolio.com/wp-content/themes/b3/svg/btc-github.svg" width="200" height="200" alt="Bitcoin QR" /></td>
      <td width="450">If you have found this work useful and would like to contribute financially, Bitcoin tips are always welcome!<br /><br /><strong>1PQhurwP2mcM8rHynYMzzs4KSKpBbVz5is</strong></td>
    </tr>
  </tbody>
</table>
