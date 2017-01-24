# blob-common

A PHP library with handy, reusable functions for sanitizing, formatting, and manipulating data.

For information about the plugin Tutan Common, which repackages and extends these functions for WordPress environments, click [here](https://github.com/Blobfolio/blob-common/tree/master/wp/).



##### Table of Contents

1. [Requirements](#requirements)
2. [Installation](#installation)
3. Reference
 * [Constants](https://github.com/Blobfolio/blob-common/blob/master/docs/CONSTANTS.md)
 * [Files and Paths](https://github.com/Blobfolio/blob-common/blob/master/docs/FILE.md)
 * [Formatting](https://github.com/Blobfolio/blob-common/blob/master/docs/FORMAT.md)
 * [General/Data](https://github.com/Blobfolio/blob-common/blob/master/docs/DATA.md)
 * [Images](https://github.com/Blobfolio/blob-common/blob/master/docs/IMAGE.md)
 * [MIME Types & File Extensions](https://github.com/Blobfolio/blob-common/blob/master/docs/MIME.md)
 * [Multi-byte Wrappers](https://github.com/Blobfolio/blob-common/blob/master/docs/MB.md)
 * [Sanitizing](https://github.com/Blobfolio/blob-common/blob/master/docs/SANITIZE.md)
 * [Typecasting](https://github.com/Blobfolio/blob-common/blob/master/docs/CASt.md)
4. [License](#license)



## Requirements

blob-common requires PHP 7+ with the following modules:

 * BCMath
 * DOM
 * Fileinfo
 * Filter
 * JSON
 * MBString
 * SimpleXML

UTF-8 is used for all string encoding. This could create conflicts on environments using something else.

The [WebP](https://github.com/Blobfolio/blob-common/blob/master/docs/IMAGE.md) functionality additionally requires access to server-side `cwebp` and `gif2webp` binaries. See the linked reference for more details.



## Installation

Use Composer:

```bash
composer require "blobfolio/blob-common:dev-master"
```



## License

Copyright © 2017 [Blobfolio, LLC](https://blobfolio.com) &lt;hello@blobfolio.com&gt;

This work is free. You can redistribute it and/or modify it under the terms of the Do What The Fuck You Want To Public License, Version 2.

    DO WHAT THE FUCK YOU WANT TO PUBLIC LICENSE
    Version 2, December 2004
    
    Copyright (C) 2016 Sam Hocevar <sam@hocevar.net>
    
    Everyone is permitted to copy and distribute verbatim or modified
    copies of this license document, and changing it is allowed as long
    as the name is changed.
    
    DO WHAT THE FUCK YOU WANT TO PUBLIC LICENSE
    TERMS AND CONDITIONS FOR COPYING, DISTRIBUTION AND MODIFICATION
    
    0. You just DO WHAT THE FUCK YOU WANT TO.
