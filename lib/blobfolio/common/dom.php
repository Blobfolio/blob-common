<?php
//---------------------------------------------------------------------
// DOM Manipulation
//---------------------------------------------------------------------
// helpers for DomDocument and the like



namespace blobfolio\common;

class dom {

	//-------------------------------------------------
	// Load an SVG
	//
	// this creates a DOMDocument object from an SVG
	// source
	//
	// @param svg
	// @return DOMDocument
	public static function load_svg(string $svg='') {
		try {
			//first thing first, lowercase all tags
			$svg = preg_replace('/<svg/i', '<svg', $svg);
			$svg = preg_replace('/<\/svg>/i', '</svg>', $svg);

			//find the start and end tags so we can cut out miscellaneous garbage
			if (
				false === ($start = mb::strpos($svg, '<svg')) ||
				false === ($end = mb::strrpos($svg, '</svg>'))
			) {
				return false;
			}
			$svg = mb::substr($svg, $start, ($end - $start + 6));

			//open it
			libxml_use_internal_errors(true);
			libxml_disable_entity_loader(true);
			$dom = new \DOMDocument('1.0', 'UTF-8');
			$dom->formatOutput = false;
			$dom->preserveWhiteSpace = false;
			$dom->loadXML(constants::SVG_HEADER . "\n{$svg}");

			return $dom;
		} catch (\Throwable $e) {
			return new \DOMDocument('1.0', 'UTF-8');
		}
	}

	//-------------------------------------------------
	// Save an SVG
	//
	// this creates a DOMDocument object from an SVG
	// source
	//
	// @param dom
	// @return svg or ''
	public static function save_svg(\DOMDocument $dom) {
		try {
			$svgs = $dom->getElementsByTagName('svg');
			if (!$svgs->length) {
				return '';
			}
			$svg = $svgs->item(0)->ownerDocument->saveXML($svgs->item(0), LIBXML_NOEMPTYTAG);

			return $svg;
		} catch (\Throwable $e) {
			return '';
		}
	}

	//-------------------------------------------------
	// Get Nodes by Class
	//
	// this will return all nodes containing a
	// particular class. Note: it does not rely on
	// xpath
	//
	// @param DOMDocument or DOMElement
	// @param class(es) to look for
	// @param match all? false for any
	// @return nodes
	public static function get_nodes_by_class($parent, $class=null, bool $all=false) {
		$nodes = array();

		try {
			if (!method_exists($parent, 'getElementsByTagName')) {
				return $nodes;
			}

			ref\cast::array($class);
			$class = array_filter($class, 'strlen');
			sort($class);
			$class = array_unique($class);
			if (!count($class)) {
				return $nodes;
			}

			$possible = $parent->getElementsByTagName('*');
			if ($possible->length) {
				foreach ($possible as $child) {
					if ($child->hasAttribute('class')) {
						$classes = $child->getAttribute('class');
						ref\sanitize::whitespace($classes);
						$classes = explode(' ', $classes);
						$overlap = array_intersect($classes, $class);

						if (count($overlap) && (!$all || count($overlap) === count($class))) {
							$nodes[] = $child;
						}
					}
				}
			}
		} catch (\Throwable $e) {
			return $nodes;
		}

		return $nodes;
	}

	//-------------------------------------------------
	// Remove Nodes
	//
	// @param DOMNodeList
	// @return true/false
	public static function remove_nodes(\DOMNodeList $nodes) {
		try {
			while ($nodes->length) {
				static::remove_node($nodes->item(0));
			}
			return true;
		} catch (\Throwable $e) {
			return false;
		}
	}

	//-------------------------------------------------
	// Remove Node
	//
	// @param DOMElement
	// @return true/false
	public static function remove_node(\DOMElement $node) {
		try {
			$node->parentNode->removeChild($node);
		} catch (\Throwable $e) {
			return false;
		}
	}
}

?>