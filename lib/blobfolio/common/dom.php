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
			$class = array_map('trim', $class);
			foreach ($class as $k=>$v) {
				$class[$k] = ltrim($class[$k], '.');
			}
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
	// Parse Styles
	//
	// @param styles
	// @return styles
	public static function parse_css($styles='') {
		ref\cast::string($styles, true);

		//remove comments
		while (false !== $start = mb::strpos($styles, '/*')) {
			if (false !== $end = mb::strpos($styles, '*/')) {
				$styles = str_replace(mb::substr($styles, $start, ($end - $start + 2)), '', $styles);
			}
			else {
				$styles = mb::substr($styles, 0, $start);
			}
		}

		//a few more types of comment wrappers we might see
		$styles = str_replace(
			array('<!--','//-->','-->','//<![CDATA[','//]]>','<![CDATA[',']]>'),
			'',
			$styles
		);

		//standardize quoting
		ref\sanitize::quotes($styles);
		$styles = str_replace("'", '"', $styles);

		//whitespace
		ref\sanitize::whitespace($styles);

		//early bail
		if (!strlen($styles)) {
			return array();
		}

		//substitute brackets for unlikely characters to make parsing easier
		//hopefully nobody's using braille in their stylesheets...
		$styles = preg_replace('/\{(?![^"]*"(?:(?:[^"]*"){2})*[^"]*$)/u', '⠁', $styles);
		$styles = preg_replace('/\}(?![^"]*"(?:(?:[^"]*"){2})*[^"]*$)/u', '⠈', $styles);

		//put spaces behind and after parentheses
		$styles = preg_replace('/\s*(\()\s*(?![^"]*"(?:(?:[^"]*"){2})*[^"]*$)/u', ' (', $styles);
		$styles = preg_replace('/\s*(\))\s*(?![^"]*"(?:(?:[^"]*"){2})*[^"]*$)/u', ') ', $styles);

		//make sure {} have no whitespace on either end
		$styles = preg_replace('/\s*(⠁|⠈|@)\s*/u', '$1', $styles);

		//push @ rules to their own lines
		$styles = str_replace('@', "\n@", $styles);
		$styles = explode("\n", $styles);
		$styles = array_map('trim', $styles);
		//push all rulesets to their own lines (leaving @media-type ones on their own for now)
		foreach ($styles as $k=>$v) {
			//@rule
			if (mb::substr($styles[$k], 0, 1) === '@') {
				//nested, like @media
				if (mb::substr_count($styles[$k], '⠈⠈')) {
					$styles[$k] = preg_replace('/(⠈{2,})/u', "$1\n", $styles[$k]);
				}
				//not nested, but has properties, like @font-face
				elseif (false !== mb::strpos($styles[$k], '⠈')) {
					$styles[$k] = str_replace('⠈', "⠈\n", $styles[$k]);
				}
				//just a line, like @import
				elseif (preg_match('/;(?![^"]*"(?:(?:[^"]*"){2})*[^"]*$)/', $styles[$k])) {
					$styles[$k] = preg_replace('/;(?![^"]*"(?:(?:[^"]*"){2})*[^"]*$)/u', ";\n", $styles[$k], 1);
				}

				$tmp = explode("\n", $styles[$k]);
				for ($x = 1; $x < count($tmp); $x++) {
					$tmp[$x] = str_replace('⠈', "⠈\n", $tmp[$x]);
				}
				$styles[$k] = implode("\n", $tmp);
			}//end @
			else {
				$styles[$k] = str_replace('⠈', "⠈\n", $styles[$k]);
			}
		}
		$styles = implode("\n", $styles);

		//one more quick formatting thing, we can get rid of spaces between closing ) and punctuation
		$styles = preg_replace('/\)\s(,|;)(?![^"]*"(?:(?:[^"]*"){2})*[^"]*$)/u', ')$1', $styles);

		$styles = explode("\n", $styles);
		$styles = array_filter($styles, 'strlen');

		$out = array();

		//one more time around!
		foreach ($styles as $k=>$v) {
			$styles[$k] = trim($styles[$k]);

			//nested rule
			if (mb::substr($styles[$k], 0, 1) === '@' && mb::substr_count($styles[$k], '⠈⠈')) {
				$tmp = constants::CSS_NESTED;

				//what kind of @ is this?
				preg_match_all('/^@([a-z\-]+)/ui', $styles[$k], $matches);
				$tmp['@'] = mb::strtolower($matches[1][0]);

				//find the outermost bit
				if (false === $start = mb::strpos($styles[$k], '⠁')) {
					continue;
				}

				$tmp['selector'] = mb::strtolower(trim(mb::substr($styles[$k], 0, $start)));
				$chunk = mb::substr($styles[$k], $start + 1, -1);
				$chunk = str_replace(array('⠁','⠈'), array('{','}'), $chunk);
				$tmp['nest'] = static::parse_css($chunk);

				//and build the raw
				$tmp['raw'] = $tmp['selector'] . '{';
				foreach ($tmp['nest'] as $n) {
					$tmp['raw'] .= $n['raw'];
				}
				$tmp['raw'] .= '}';
			}//at rule
			else {
				$tmp = constants::CSS_FLAT;

				if (mb::substr($styles[$k], 0, 1) === '@') {
					//what kind of @ is this?
					preg_match_all('/^@([a-z\-]+)/ui', $styles[$k], $matches);
					$tmp['@'] = mb::strtolower($matches[1][0]);
				}

				//a normal {k:v, k:v}
				preg_match_all('/^([^⠁]+)⠁([^⠈]*)⠈/u', $styles[$k], $matches);
				if (count($matches[0])) {
					//sorting out selectors is easy
					$tmp['selectors'] = explode(',', $matches[1][0]);
					$tmp['selectors'] = array_map('trim', $tmp['selectors']);

					//rules a little trickier
					$rules = explode(';', $matches[2][0]);
					$rules = array_map('trim', $rules);
					$rules = array_filter($rules, 'strlen');
					if (!count($rules)) {
						continue;
					}

					foreach ($rules as $k2=>$v2) {
						$rules[$k2] = rtrim($rules[$k2], ';') . ';';
						if (preg_match('/:(?![^"]*"(?:(?:[^"]*"){2})*[^"]*$)/', $rules[$k2])) {
							$rules[$k2] = preg_replace('/:(?![^"]*"(?:(?:[^"]*"){2})*[^"]*$)/u', "\n", $rules[$k2], 1);
							list($key, $value) = explode("\n", $rules[$k2]);
							$key = mb::strtolower(trim($key));
							$value = trim($value);
							$tmp['rules'][$key] = $value;
						}
						else {
							$tmp['rules']['__NONE__'] = $value;
						}
					}

					//build the raw
					$tmp['raw'] = implode(',', $tmp['selectors']) . '{';
					foreach ($tmp['rules'] as $k=>$v) {
						if ($k === '__NONE__') {
							$tmp['raw'] .= $v;
						}
						else {
							$tmp['raw'] .= "$k:$v";
						}
					}
					$tmp['raw'] .= '}';
				}
				//who knows
				else {
					$styles[$k] = str_replace(array('⠁','⠈'), array('{','}'), $styles[$k]);
					$styles[$k] = trim(rtrim(trim($styles[$k]), ';'));
					if (mb::substr($styles[$k], -1) !== '}') {
						$styles[$k] .= ';';
					}
					$tmp['rules'][] = $styles[$k];
					$tmp['raw'] = $styles[$k];
				}
			}

			$out[] = $tmp;
		}

		return $out;
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