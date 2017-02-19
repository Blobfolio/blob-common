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



## remove_node()

Remove a DOMElement node.

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
