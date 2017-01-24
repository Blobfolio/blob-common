# Constants

The following static data exists as defined constants for your coding pleasure.

**Namespace:**
`blobfolio\common\constants`

**Use:**
```php
echo blobfolio\common\constants::SOME_CONSTANT;
```



##### Table of Contents

1. Geography
 * [COUNTRIES](#countries)
 * [PROVINCES](#provinces)
 * [REGIONS](#regions)
 * [STATES](#states)
2. Other
 * [BLANK_IMAGE](#blank_image)
 * [MIME_DEFAULT](#mime_default)



## BLANK_IMAGE

A Data-URI corresponding to a 1x1 pixel transparent GIF. This might be shoved into an SRC attribute for a lazy-loaded image, for example.



## COUNTRIES

An array containing the ISO code, name, region, and currency of most world countries.

```php
array(
    'US'=>array(
        'name'=>'USA',
        'region'=>'North America',
        'currency'=>'USD'
    ),
    'CA'=>array(
        'name'=>'Canada',
        'region'=>'North America',
        'currency'=>'CAD'
    ),
    ...
);
```



## MIME_DEFAULT

The default MIME type, i.e. `"application/octet-stream"`. A web server will often return this type when it can't figure out what a file is from its name or content.



## PROVINCES

An array of Canadian provinces and territories.

```php
array(
    'AB'=>'Alberta',
    'BC'=>'British Columbia',
    ...
);
```



## REGIONS

An array of populated continents (i.e. sans Antarctica).

```php
array(
    'Africa',
    'Asia',
    ...
);
```



## STATES

An array of US states and territories.

```php
array(
    'AL'=>'Alabama',
    'AK'=>'Alaska',
    ...
);
```
