<?php
//---------------------------------------------------------------------
// General Test
//---------------------------------------------------------------------


namespace blobfolio\test;

class cli {
	const INDENT = 2;
	const CAPY = 10;
	const CAPX = 75;
	const CUTOFF = '…';

	protected static $output = array(
		'functions'=>array('FUNCTION'),
		'arguments'=>array('ARGUMENT(S)'),
		'results'=>array('RESULT')
	);

	protected static function format($value, $key=null, int $level=0) {
		$type = substr(gettype($value), 0, 1);
		$out = array();

		if (is_array($value)) {
			$out[] = str_repeat(' ', $level * static::INDENT) . (!is_null($key) ? "[$key] => " : '') . '(array)';
			foreach ($value as $k=>$v) {
				$tmp = static::format($v, $k, $level + 1);
				foreach ($tmp as $t) {
					$out[] = $t;
				}
			}
		}
		else {
			if (is_bool($value)) {
				$value = $value ? 'TRUE' : 'FALSE';
			}
			elseif (is_null($value)) {
				$value = 'NULL';
			}
			else {
				$value = (string) $value;
				$value = preg_replace('/\v/u', '[\n]', $value);
			}

			$out[] = str_repeat(' ', $level * static::INDENT) . (!is_null($key) ? "[$key] => " : '') . "($type) $value";
		}

		return $out;
	}

	public static function record(string $function='', array $args=array(), $result='') {
		$line = array(
			'functions'=>array($function),
			'arguments'=>array(),
			'results'=>static::format($result)
		);

		if (count($args)) {
			foreach ($args as $v) {
				$tmp = static::format($v);
				foreach ($tmp as $t) {
				$line['arguments'][] = $t;
				}
			}
		}

		$max = 1;
		foreach ($line as $k=>$v) {
			if (count($line[$k]) > static::CAPY) {
				$line[$k] = array_splice($line[$k], 0, static::CAPY);
				$line[$k][] = static::CUTOFF;
			}

			foreach ($line[$k] as $k2=>$v2) {
				if (mb_strlen($v2) > static::CAPX) {
					$line[$k][$k2] = rtrim(mb_substr($v2, 0, static::CAPX)) . static::CUTOFF;
				}
			}

			if (count($line[$k]) > $max) {
				$max = count($line[$k]);
			}
		}

		foreach ($line as $k=>$v) {
			$line[$k] = array_pad($v, $max, '');
			foreach ($line[$k] as $v2) {
				static::$output[$k][] = $v2;
			}
		}
	}

	public static function print(string $title='') {
		//size columns
		$columns = array('functions'=>0, 'arguments'=>0, 'results'=>0);
		foreach ($columns as $k=>$v) {
			$columns[$k] = max(array_map('mb_strlen', static::$output[$k]));
			if ($k !== 'results') {
				$columns[$k] += static::INDENT * 2;
			}

			foreach (static::$output[$k] as $k2=>$v2) {
				static::$output[$k][$k2] .= str_repeat(' ', $columns[$k] - mb_strlen($v2));
			}
		}

		$divider = str_repeat('-', array_sum($columns) + static::INDENT * 2) . "\n";

		echo "{$divider}{$title}\n";

		foreach (static::$output['functions'] as $k=>$v) {
			if (strlen(trim($v))) {
				echo $divider;
			}
			echo "$v" . str_repeat(' ', static::INDENT) . static::$output['arguments'][$k]  . str_repeat(' ', static::INDENT) . static::$output['results'][$k] . "\n";
		}

		static::$output = array(
			'functions'=>array('FUNCTION'),
			'arguments'=>array('ARGUMENT(S)'),
			'results'=>array('RESULT')
		);
	}
}

?>