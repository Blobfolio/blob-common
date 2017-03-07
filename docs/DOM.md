# DOM Helpers

blob-common contains a handful of helpers built around DOMDocument, etc.

**Namespace:**
`blobfolio\common\dom`

**Use:**
```php
$foo = blobfolio\common\dom::get_nodes_by_class($dom, 'apples');
```



##### Table of Contents

 * [get_nodes_by_class()](#get_nodes_by_class)
 * [load_svg()](#load_svg)
 * [parse_css()](#parse_css)
 * [remove_namespace()](#remove_namespace)
 * [remove_node()](#remove_node)
 * [remove_nodes()](#remove_nodes)
 * [save_svg()](#save_svg)



## get_nodes_by_class()

Get an array of nodes containing one or more specified classes.

#### Arguments

 * (*mixed*) Parent. A `DOMDocument` or `DOMElement` object.
 * (*mixed*) Class(es). One or more classes to search.
 * (*bool*) (*optional*) Match All. If `TRUE`, only nodes with every passed class will be returned. Otherwise nodes with any of the passed classes will be returned. Default: `FALSE`

#### Returns

Returns an array containing the matching nodes, if any.

#### Example

```php
$foo = blobfolio\common\dom::get_nodes_by_class($dom, 'apples');
```



## load_svg()

Generate a DOMDocument object from a string containing SVG code. This performs some initial setup optimized for SVG content.

#### Arguments

 * (*string*) SVG code.

#### Returns

Returns a DOMDocument object (empty on failure).

#### Example

```php
$dom = blobfolio\common\dom::load_svg($svg);
```



## parse_css()

This is a handy little parser that will take the bits between `<style>` tags and hand it back to you in parseable array format.

#### Arguments

 * (*string*) Style code.

#### Returns

Returns an array containing the parsed styles.

#### Example

```php
//from a .css file
$css = @file_get_contents('css/styles.css');
//or something like...
$css = $dom->getElementsByTagName('style')->item(0)->nodeValue;

//then parse
$foo = blobfolio\common\dom::parse_css($css);

/*
Array(
    //example of a regular old rule
    0 => Array(
        [@] => FALSE
        [nested] => FALSE
        [selectors] => Array(
            0 => .blobfolio-about-logo svg
        )
        [rules] => Array(
            [transition] => color .3s ease;
            [display] => block;
            [width] => 100%;
            [height] => auto;
        )
        [raw] => .blobfolio-about-logo svg{transition:color .3s ease;display:block;width:100%;height:auto;}
    ),
    //example @ rule with regular definitions
    1 => Array(
        [@] => font-face
        [nested] => FALSE
        [selectors] => Array(
            0 => @font-face
        )
        [rules] => Array(
            [font-family] => "TotallyRealFont";
            [src] => url ("../font/3197DB_0_0.woff2") format ("woff2"), url ("../font/3197DB_0_0.woff") format ("woff"), url ("../font/3197DB_0_0.ttf") format ("truetype");
            [font-weight] => 700;
            [font-style] => normal;
        )
        [raw] => @font-face{font-family:"TotallyRealFont";src:url ("../font/3197DB_0_0.woff2") format ("woff2"), url ("../font/3197DB_0_0.woff") format ("woff"), url ("../font/3197DB_0_0.ttf") format ("truetype");font-weight:700;font-style:normal;}
    ),
    //example @ rule without any {...} block
    2 => Array(
        [@] => import
        [nested] => FALSE
        [selectors] => Array()
        [rules] => Array(
            0 => @import url (https://fonts.googleapis.com/css?family=Open+Sans);
        )
        [raw] => @import url (https://fonts.googleapis.com/css?family=Open+Sans);
    ),
    //example nested @ rule
    3 => Array(
        [@] => media
        [nested] => TRUE
        [selector] => @media screen only and (min-width: 10em)
        [nest] => Array (
            0 => Array(
                [@] => FALSE
                [nested] => FALSE
                [selectors] => Array(
                    0 => #debug-log
                )
                [rules] => Array(
                    [background] => red;
                )
                [raw] => #debug-log{background:red;}
            )
        )
        [raw] => @media screen only and (min-width: 10em){#debug-log{background:red;}}
    )
)
*/
```



## remove_namespace()

Remove a namespace and any corresponding tags from a DOMDocument object.

#### Arguments

 * (*DOMDocument*) DOMDocument object.
 * (*string*) Namespace.

#### Returns

Returns `TRUE` or `FALSE`

#### Example

```php
\blobfolio\common\dom::remove_namespace($dom, 'foobar');
```



## remove_node()

Remove a DOMElement or DOMNode.

#### Arguments

 * (*DOMElement*) Node.

#### Returns

Returns `TRUE` or `FALSE`

#### Example

```php
$styles = $dom->getElementsByTagName('style');
while($styles->length > 1){
    \blobfolio\common\dom::remove_node($styles->item(0));
}
```



## remove_nodes()

Remove one or more nodes.

#### Arguments

 * (*DOMNodeList*) Nodes.

#### Returns

Returns `TRUE` or `FALSE`

#### Example

```php
$styles = $dom->getElementsByTagName('style');
\blobfolio\common\dom::remove_nodes($styles);
```



## save_svg()

Extract the SVG code from a DOMDocument object (presumably one created with `dom::load_svg()`).

#### Arguments

 * (*DOMDocument*) DOM object.

#### Returns

Returns a string, either with the SVG code or empty on failure.

#### Example

```php
$svg = \blobfolio\common\dom::save_svg($dom);
```
