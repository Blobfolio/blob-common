<?php
//---------------------------------------------------------------------
// Locality
//---------------------------------------------------------------------
// functions for handling space and time



//---------------------------------------------------------------------
// Geography
//---------------------------------------------------------------------

//-------------------------------------------------
// Return array of us states
//
// @param include other?
// @return states
if(!function_exists('common_get_us_states')){
	function common_get_us_states($include_other=true){
		$states = array(
			'AL' => 'ALABAMA',
			'AK' => 'ALASKA',
			'AZ' => 'ARIZONA',
			'AR' => 'ARKANSAS',
			'CA' => 'CALIFORNIA',
			'CO' => 'COLORADO',
			'CT' => 'CONNECTICUT',
			'DE' => 'DELAWARE',
			'DC' => 'DISTRICT OF COLUMBIA',
			'FL' => 'FLORIDA',
			'GA' => 'GEORGIA',
			'HI' => 'HAWAII',
			'ID' => 'IDAHO',
			'IL' => 'ILLINOIS',
			'IN' => 'INDIANA',
			'IA' => 'IOWA',
			'KS' => 'KANSAS',
			'KY' => 'KENTUCKY',
			'LA' => 'LOUISIANA',
			'ME' => 'MAINE',
			'MD' => 'MARYLAND',
			'MA' => 'MASSACHUSETTS',
			'MI' => 'MICHIGAN',
			'MN' => 'MINNESOTA',
			'MS' => 'MISSISSIPPI',
			'MO' => 'MISSOURI',
			'MT' => 'MONTANA',
			'NE' => 'NEBRASKA',
			'NV' => 'NEVADA',
			'NH' => 'NEW HAMPSHIRE',
			'NJ' => 'NEW JERSEY',
			'NM' => 'NEW MEXICO',
			'NY' => 'NEW YORK',
			'NC' => 'NORTH CAROLINA',
			'ND' => 'NORTH DAKOTA',
			'OH' => 'OHIO',
			'OK' => 'OKLAHOMA',
			'OR' => 'OREGON',
			'PA' => 'PENNSYLVANIA',
			'RI' => 'RHODE ISLAND',
			'SC' => 'SOUTH CAROLINA',
			'SD' => 'SOUTH DAKOTA',
			'TN' => 'TENNESSEE',
			'TX' => 'TEXAS',
			'UT' => 'UTAH',
			'VT' => 'VERMONT',
			'VA' => 'VIRGINIA',
			'WA' => 'WASHINGTON',
			'WV' => 'WEST VIRGINIA',
			'WI' => 'WISCONSIN',
			'WY' => 'WYOMING'
		);

		$other = array(
			'AA' => 'ARMED FORCES AMERICAS',
			'AE' => 'ARMED FORCES EUROPE',
			'AP' => 'ARMED FORCES PACIFIC',
			'AS' => 'AMERICAN SAMOA',
			'FM' => 'FEDERATED STATES OF MICRONESIA',
			'GU' => 'GUAM GU',
			'MH' => 'MARSHALL ISLANDS',
			'MP' => 'NORTHERN MARIANA ISLANDS',
			'PW' => 'PALAU',
			'PR' => 'PUERTO RICO',
			'VI' => 'VIRGIN ISLANDS'
		);

		if($include_other)
			return array_merge($states, $other);
		else
			return $states;
	}
}

//-------------------------------------------------
// Return canadian provinces
//
// @param n/a
// @return provinces
if(!function_exists('common_get_ca_provinces')){
	function common_get_ca_provinces(){
		return array(
			'AB' => 'ALBERTA',
			'BC' => 'BRITISH COLUMBIA',
			'MB' => 'MANITOBA',
			'NB' => 'NEW BRUNSWICK',
			'NL' => 'NEWFOUNDLAND',
			'NT' => 'NORTHWEST TERRITORIES',
			'NS' => 'NOVA SCOTIA',
			'NU' => 'NUNAVUT',
			'ON' => 'ONTARIO',
			'PE' => 'PRINCE EDWARD ISLAND',
			'QC' => 'QUEBEC',
			'SK' => 'SASKATCHEWAN',
			'YT' => 'YUKON'
		);
	}
}

//-------------------------------------------------
// Return countries
//
// key is ISO code
//
// @param n/a
// @return countries
if(!function_exists('common_get_countries')){
	function common_get_countries(){
		return array(
			'US' => 'USA',
			'CA' => 'Canada',
			'GB' => 'United Kingdom',
			'AF' => 'Afghanistan',
			'AL' => 'Albania',
			'AR' => 'Argentina',
			'AU' => 'Australia',
			'AT' => 'Austria',
			'BD' => 'Bangladesh',
			'BE' => 'Belgium',
			'BO' => 'Bolivia',
			'BA' => 'Bosnia and Herzegovina',
			'BR' => 'Brazil',
			'BG' => 'Bulgaria',
			'KH' => 'Cambodia',
			'CL' => 'Chile',
			'CN' => 'China',
			'CO' => 'Colombia',
			'CR' => 'Costa Rica',
			'HR' => 'Croatia',
			'CZ' => 'Czech Republic',
			'DK' => 'Denmark',
			'DO' => 'Dominican Republic',
			'EC' => 'Ecuador',
			'EG' => 'Egypt',
			'SV' => 'El Salvador',
			'EE' => 'Estonia',
			'ET' => 'Ethiopia',
			'FJ' => 'Fiji',
			'FI' => 'Finland',
			'FR' => 'France',
			'DE' => 'Germany',
			'GI' => 'Gibraltar',
			'GR' => 'Greece',
			'GL' => 'Greenland',
			'GU' => 'Guam',
			'GT' => 'Guatemala',
			'GG' => 'Guernsey',
			'HT' => 'Haiti',
			'HN' => 'Honduras',
			'HK' => 'Hong Kong',
			'HU' => 'Hungary',
			'IS' => 'Iceland',
			'IN' => 'India',
			'ID' => 'Indonesia',
			'IQ' => 'Iraq',
			'IE' => 'Ireland',
			'IM' => 'Isle of Man',
			'IL' => 'Israel',
			'IT' => 'Italy',
			'JM' => 'Jamaica',
			'JP' => 'Japan',
			'JE' => 'Jersey',
			'JO' => 'Jordan',
			'KZ' => 'Kazakhstan',
			'KE' => 'Kenya',
			'KR' => 'Korea, South',
			'KW' => 'Kuwait',
			'KG' => 'Kyrgyzstan',
			'LA' => 'Laos',
			'LV' => 'Latvia',
			'LB' => 'Lebanon',
			'LI' => 'Liechtenstein',
			'LT' => 'Lithuania',
			'LU' => 'Luxembourg',
			'MO' => 'Macao',
			'MK' => 'Macedonia',
			'MG' => 'Madagascar',
			'MW' => 'Malawi',
			'MY' => 'Malaysia',
			'MT' => 'Malta',
			'MX' => 'Mexico',
			'MD' => 'Moldova',
			'MC' => 'Monaco',
			'MN' => 'Mongolia',
			'ME' => 'Montenegro',
			'MA' => 'Morocco',
			'MZ' => 'Mozambique',
			'MM' => 'Myanmar (Burma)',
			'NA' => 'Namibia',
			'NP' => 'Nepal',
			'NL' => 'Netherlands',
			'NZ' => 'New Zealand',
			'NI' => 'Nicaragua',
			'NO' => 'Norway',
			'PK' => 'Pakistan',
			'PA' => 'Panama',
			'PG' => 'Papua New Guinea',
			'PY' => 'Paraguay',
			'PE' => 'Peru',
			'PH' => 'Philippines',
			'PL' => 'Poland',
			'PT' => 'Portugal',
			'PR' => 'Puerto Rico',
			'QA' => 'Qatar',
			'RO' => 'Romania',
			'RU' => 'Russia',
			'RW' => 'Rwanda',
			'SM' => 'San Marino',
			'SA' => 'Saudi Arabia',
			'SN' => 'Senegal',
			'RS' => 'Serbia',
			'SL' => 'Sierra Leone',
			'SG' => 'Singapore',
			'SK' => 'Slovakia',
			'SI' => 'Slovenia',
			'ZA' => 'South Africa',
			'ES' => 'Spain',
			'LK' => 'Sri Lanka',
			'SZ' => 'Swaziland',
			'SE' => 'Sweden',
			'CH' => 'Switzerland',
			'SY' => 'Syrian Arab Republic',
			'TW' => 'Taiwan',
			'TJ' => 'Tajikistan',
			'TZ' => 'Tanzania',
			'TH' => 'Thailand',
			'TN' => 'Tunisia',
			'TR' => 'Turkey',
			'VI' => 'U.S. Virgin Islands',
			'UA' => 'Ukraine',
			'AE' => 'United Arab Emirates',
			'UY' => 'Uruguay',
			'UZ' => 'Uzbekistan',
			'VA' => 'Vatican City',
			'VE' => 'Venezuela',
			'VU' => 'Vietnam'
		);
	}
}

//--------------------------------------------------------------------- end geography



//---------------------------------------------------------------------
// IPs
//---------------------------------------------------------------------

//-------------------------------------------------
// IP as Number
//
// convert an IP to a number for cleaner comparison
//
// @param IP
// @return num or false
if(!function_exists('common_ip_to_number')){
	function common_ip_to_number($ip){
		if(!filter_var($ip, FILTER_VALIDATE_IP))
			return false;

		//ipv4 is easy
		if(filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_IPV4))
			return ip2long($ip);

		//ipv6 is a little more roundabout
		if(filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_IPV6)){
			$ip_n = inet_pton($ip);
			$bin = '';
			for($bit = strlen($ip_n) - 1; $bit >= 0; $bit--)
				$bin = sprintf('%08b', ord($ip_n[$bit])) . $bin;
			$dec = '0';
			for ($i = 0; $i < strlen($bin); $i++){
				$dec = bcmul($dec, '2', 0);
				$dec = bcadd($dec, $bin[$i], 0);
			}
			return $dec;
		}

		return false;
	}
}

//-------------------------------------------------
// Convert Netblock to Min/Max IPs
//
// @param cidr
// @return array or false
if(!function_exists('common_cidr_to_range')){
	function common_cidr_to_range($cidr){
		$range = array('min'=>0, 'max'=>0);
		$cidr = explode('/', $cidr);

		//ipv4?
		if(filter_var($cidr[0], FILTER_VALIDATE_IP, FILTER_FLAG_IPV4)){
			$range['min'] = long2ip((ip2long($cidr[0])) & ((-1 << (32 - (int)$cidr[1]))));
			$range['max'] = long2ip((ip2long($cidr[0])) + pow(2, (32 - (int)$cidr[1])) - 1);
			return $range;
		}

		//ipv6?  of course a little more complicated
		if(filter_var($cidr[0], FILTER_VALIDATE_IP, FILTER_FLAG_IPV6)){
			//parse the address into a binary string
			$firstaddrbin = inet_pton($cidr[0]);

			//convert the binary string to a string with hexadecimal characters (bin2hex)
			$firstaddrhex = reset(unpack('H*', $firstaddrbin));

			//overwriting first address string to make sure notation is optimal
			$cidr[0] = inet_ntop($firstaddrbin);

			//calculate the number of 'flexible' bits
			$flexbits = 128 - $cidr[1];

			//build the hexadecimal string of the last address
			$lastaddrhex = $firstaddrhex;

			//we start at the end of the string (which is always 32 characters long)
			$pos = 31;
			while($flexbits > 0){
				//get the character at this position
				$orig = substr($lastaddrhex, $pos, 1);

				//convert it to an integer
				$origval = hexdec($orig);

				//OR it with (2^flexbits)-1, with flexbits limited to 4 at a time
				$newval = $origval | (pow(2, min(4, $flexbits)) - 1);

				//convert it back to a hexadecimal character
				$new = dechex($newval);

				//and put that character back in the string
				$lastaddrhex = substr_replace($lastaddrhex, $new, $pos, 1);

				//we processed one nibble, move to previous position
				$flexbits -= 4;
				$pos -= 1;
			}

			//convert the hexadecimal string to a binary string (hex2bin)
			$lastaddrbin = pack('H*', $lastaddrhex);

			//and create an IPv6 address from the binary string
			$lastaddrstr = inet_ntop($lastaddrbin);

			//pack and done!
			$range['min'] = common_sanitize_ip($cidr[0]);
			$range['max'] = common_sanitize_ip($lastaddrstr);
			return $range;
		}

		return false;
	}
}

//--------------------------------------------------------------------- end IPs



//---------------------------------------------------------------------
// Time
//---------------------------------------------------------------------

//-------------------------------------------------
// Datediff
//
// a simple function to count the number of days
// between two dates
//
// @param date1
// @param date2
// @return days
if(!function_exists('common_datediff')){
	function common_datediff($date1, $date2){
		$date1 = date('Y-m-d', strtotime($date1));
		$date2 = date('Y-m-d', strtotime($date2));

		//same date, tricky tricky!
		if($date1 === $date2)
			return 0;

		try {
			$date1 = new DateTime($date1);
			$date2 = new DateTime($date2);
			$diff = $date1->diff($date2);

			return abs($diff->days);
		}
		catch(Exception $e){
			//this is a simple fallback using unix timestamps
			//however it will fail to consider things like
			//daylight saving
			$date1 = strtotime($date1);
			$date2 = strtotime($date2);
			return ceil(abs($date2 - $date1) / 60 / 60 / 24);
		}
	}
}

//--------------------------------------------------------------------- end time
?>