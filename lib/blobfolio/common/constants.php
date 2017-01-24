<?php
//---------------------------------------------------------------------
// CONSTANTS
//---------------------------------------------------------------------
// this file contains constants that are used elsewhere so as not to
// clog those files



namespace blobfolio\common;

class constants {

	//-------------------------------------------------
	// Character Sets

	//accent=>regular
	const ACCENT_CHARS = array(
		'ª'=>'a',	'º'=>'o',	'À'=>'A',	'Á'=>'A',	'Â'=>'A',	'Ã'=>'A',
		'Ä'=>'A',	'Å'=>'A',	'Æ'=>'AE',	'Ç'=>'C',	'È'=>'E',	'É'=>'E',
		'Ê'=>'E',	'Ë'=>'E',	'Ì'=>'I',	'Í'=>'I',	'Î'=>'I',	'Ï'=>'I',
		'Ð'=>'D',	'Ñ'=>'N',	'Ò'=>'O',	'Ó'=>'O',	'Ô'=>'O',	'Õ'=>'O',
		'Ö'=>'O',	'Ù'=>'U',	'Ú'=>'U',	'Û'=>'U',	'Ü'=>'U',	'Ý'=>'Y',
		'Þ'=>'TH',	'ß'=>'s',	'à'=>'a',	'á'=>'a',	'â'=>'a',	'ã'=>'a',
		'ä'=>'a',	'å'=>'a',	'æ'=>'ae',	'ç'=>'c',	'è'=>'e',	'é'=>'e',
		'ê'=>'e',	'ë'=>'e',	'ì'=>'i',	'í'=>'i',	'î'=>'i',	'ï'=>'i',
		'ð'=>'d',	'ñ'=>'n',	'ò'=>'o',	'ó'=>'o',	'ô'=>'o',	'õ'=>'o',
		'ö'=>'o',	'ø'=>'o',	'ù'=>'u',	'ú'=>'u',	'û'=>'u',	'ü'=>'u',
		'ý'=>'y',	'þ'=>'th',	'ÿ'=>'y',	'Ø'=>'O',	'Ā'=>'A',	'ā'=>'a',
		'Ă'=>'A',	'ă'=>'a',	'Ą'=>'A',	'ą'=>'a',	'Ć'=>'C',	'ć'=>'c',
		'Ĉ'=>'C',	'ĉ'=>'c',	'Ċ'=>'C',	'ċ'=>'c',	'Č'=>'C',	'č'=>'c',
		'Ď'=>'D',	'ď'=>'d',	'Đ'=>'D',	'đ'=>'d',	'Ē'=>'E',	'ē'=>'e',
		'Ĕ'=>'E',	'ĕ'=>'e',	'Ė'=>'E',	'ė'=>'e',	'Ę'=>'E',	'ę'=>'e',
		'Ě'=>'E',	'ě'=>'e',	'Ĝ'=>'G',	'ĝ'=>'g',	'Ğ'=>'G',	'ğ'=>'g',
		'Ġ'=>'G',	'ġ'=>'g',	'Ģ'=>'G',	'ģ'=>'g',	'Ĥ'=>'H',	'ĥ'=>'h',
		'Ħ'=>'H',	'ħ'=>'h',	'Ĩ'=>'I',	'ĩ'=>'i',	'Ī'=>'I',	'ī'=>'i',
		'Ĭ'=>'I',	'ĭ'=>'i',	'Į'=>'I',	'į'=>'i',	'İ'=>'I',	'ı'=>'i',
		'Ĳ'=>'IJ',	'ĳ'=>'ij',	'Ĵ'=>'J',	'ĵ'=>'j',	'Ķ'=>'K',	'ķ'=>'k',
		'ĸ'=>'k',	'Ĺ'=>'L',	'ĺ'=>'l',	'Ļ'=>'L',	'ļ'=>'l',	'Ľ'=>'L',
		'ľ'=>'l',	'Ŀ'=>'L',	'ŀ'=>'l',	'Ł'=>'L',	'ł'=>'l',	'Ń'=>'N',
		'ń'=>'n',	'Ņ'=>'N',	'ņ'=>'n',	'Ň'=>'N',	'ň'=>'n',	'ŉ'=>'N',
		'Ŋ'=>'n',	'ŋ'=>'N',	'Ō'=>'O',	'ō'=>'o',	'Ŏ'=>'O',	'ŏ'=>'o',
		'Ő'=>'O',	'ő'=>'o',	'Œ'=>'OE',	'œ'=>'oe',	'Ŕ'=>'R',	'ŕ'=>'r',
		'Ŗ'=>'R',	'ŗ'=>'r',	'Ř'=>'R',	'ř'=>'r',	'Ś'=>'S',	'ś'=>'s',
		'Ŝ'=>'S',	'ŝ'=>'s',	'Ş'=>'S',	'ş'=>'s',	'Š'=>'S',	'š'=>'s',
		'Ţ'=>'T',	'ţ'=>'t',	'Ť'=>'T',	'ť'=>'t',	'Ŧ'=>'T',	'ŧ'=>'t',
		'Ũ'=>'U',	'ũ'=>'u',	'Ū'=>'U',	'ū'=>'u',	'Ŭ'=>'U',	'ŭ'=>'u',
		'Ů'=>'U',	'ů'=>'u',	'Ű'=>'U',	'ű'=>'u',	'Ų'=>'U',	'ų'=>'u',
		'Ŵ'=>'W',	'ŵ'=>'w',	'Ŷ'=>'Y',	'ŷ'=>'y',	'Ÿ'=>'Y',	'Ź'=>'Z',
		'ź'=>'z',	'Ż'=>'Z',	'ż'=>'z',	'Ž'=>'Z',	'ž'=>'z',	'ſ'=>'s',
		'Ș'=>'S',	'ș'=>'s',	'Ț'=>'T',	'ț'=>'t',	'€'=>'E',	'£'=>'',
		'Ơ'=>'O',	'ơ'=>'o',	'Ư'=>'U',	'ư'=>'u',	'Ầ'=>'A',	'ầ'=>'a',
		'Ằ'=>'A',	'ằ'=>'a',	'Ề'=>'E',	'ề'=>'e',	'Ồ'=>'O',	'ồ'=>'o',
		'Ờ'=>'O',	'ờ'=>'o',	'Ừ'=>'U',	'ừ'=>'u',	'Ỳ'=>'Y',	'ỳ'=>'y',
		'Ả'=>'A',	'ả'=>'a',	'Ẩ'=>'A',	'ẩ'=>'a',	'Ẳ'=>'A',	'ẳ'=>'a',
		'Ẻ'=>'E',	'ẻ'=>'e',	'Ể'=>'E',	'ể'=>'e',	'Ỉ'=>'I',	'ỉ'=>'i',
		'Ỏ'=>'O',	'ỏ'=>'o',	'Ổ'=>'O',	'ổ'=>'o',	'Ở'=>'O',	'ở'=>'o',
		'Ủ'=>'U',	'ủ'=>'u',	'Ử'=>'U',	'ử'=>'u',	'Ỷ'=>'Y',	'ỷ'=>'y',
		'Ẫ'=>'A',	'ẫ'=>'a',	'Ẵ'=>'A',	'ẵ'=>'a',	'Ẽ'=>'E',	'ẽ'=>'e',
		'Ễ'=>'E',	'ễ'=>'e',	'Ỗ'=>'O',	'ỗ'=>'o',	'Ỡ'=>'O',	'ỡ'=>'o',
		'Ữ'=>'U',	'ữ'=>'u',	'Ỹ'=>'Y',	'ỹ'=>'y',	'Ấ'=>'A',	'ấ'=>'a',
		'Ắ'=>'A',	'ắ'=>'a',	'Ế'=>'E',	'ế'=>'e',	'Ố'=>'O',	'ố'=>'o',
		'Ớ'=>'O',	'ớ'=>'o',	'Ứ'=>'U',	'ứ'=>'u',	'Ạ'=>'A',	'ạ'=>'a',
		'Ậ'=>'A',	'ậ'=>'a',	'Ặ'=>'A',	'ặ'=>'a',	'Ẹ'=>'E',	'ẹ'=>'e',
		'Ệ'=>'E',	'ệ'=>'e',	'Ị'=>'I',	'ị'=>'i',	'Ọ'=>'O',	'ọ'=>'o',
		'Ộ'=>'O',	'ộ'=>'o',	'Ợ'=>'O',	'ợ'=>'o',	'Ụ'=>'U',	'ụ'=>'u',
		'Ự'=>'U',	'ự'=>'u',	'Ỵ'=>'Y',	'ỵ'=>'y',	'ɑ'=>'a',	'Ǖ'=>'U',
		'ǖ'=>'u',	'Ǘ'=>'U',	'ǘ'=>'u',	'Ǎ'=>'A',	'ǎ'=>'a',	'Ǐ'=>'I',
		'ǐ'=>'i',	'Ǒ'=>'O',	'ǒ'=>'o',	'Ǔ'=>'U',	'ǔ'=>'u',	'Ǚ'=>'U',
		'ǚ'=>'u',	'Ǜ'=>'U',	'ǜ'=>'u'
	);

	//uncaught unicode upper=>lower
	const CASE_CHARS = array(
		"\xC7\x85"=>"\xC7\x86",			//453=>454
		"\xC7\x88"=>"\xC7\x89",			//456=>457
		"\xC7\x8B"=>"\xC7\x8C",			//459=>460
		"\xC7\xB2"=>"\xC7\xB3",			//498=>499
		"\xCF\xB7"=>"\xCF\xB8",			//1015=>1016
		"\xCF\xB9"=>"\xCF\xB2",			//1017=>1010
		"\xCF\xBA"=>"\xCF\xBB",			//1018=>1019
		"\xE1\xBE\x88"=>"\xE1\xBE\x80",	//8072=>8064
		"\xE1\xBE\x89"=>"\xE1\xBE\x81",	//8073=>8065
		"\xE1\xBE\x8A"=>"\xE1\xBE\x82",	//8074=>8066
		"\xE1\xBE\x8B"=>"\xE1\xBE\x83",	//8075=>8067
		"\xE1\xBE\x8C"=>"\xE1\xBE\x84",	//8076=>8068
		"\xE1\xBE\x8D"=>"\xE1\xBE\x85",	//8077=>8069
		"\xE1\xBE\x8E"=>"\xE1\xBE\x86",	//8078=>8070
		"\xE1\xBE\x8F"=>"\xE1\xBE\x87",	//8079=>8071
		"\xE1\xBE\x98"=>"\xE1\xBE\x90",	//8088=>8080
		"\xE1\xBE\x99"=>"\xE1\xBE\x91",	//8089=>8081
		"\xE1\xBE\x9A"=>"\xE1\xBE\x92",	//8090=>8082
		"\xE1\xBE\x9B"=>"\xE1\xBE\x93",	//8091=>8083
		"\xE1\xBE\x9C"=>"\xE1\xBE\x94",	//8092=>8084
		"\xE1\xBE\x9D"=>"\xE1\xBE\x95",	//8093=>8085
		"\xE1\xBE\x9E"=>"\xE1\xBE\x96",	//8094=>8086
		"\xE1\xBE\x9F"=>"\xE1\xBE\x97",	//8095=>8087
		"\xE1\xBE\xA8"=>"\xE1\xBE\xA0",	//8104=>8096
		"\xE1\xBE\xA9"=>"\xE1\xBE\xA1",	//8105=>8097
		"\xE1\xBE\xAA"=>"\xE1\xBE\xA2",	//8106=>8098
		"\xE1\xBE\xAB"=>"\xE1\xBE\xA3",	//8107=>8099
		"\xE1\xBE\xAC"=>"\xE1\xBE\xA4",	//8108=>8100
		"\xE1\xBE\xAD"=>"\xE1\xBE\xA5",	//8109=>8101
		"\xE1\xBE\xAE"=>"\xE1\xBE\xA6",	//8110=>8102
		"\xE1\xBE\xAF"=>"\xE1\xBE\xA7",	//8111=>8103
		"\xE1\xBE\xBC"=>"\xE1\xBE\xB3",	//8124=>8115
		"\xE1\xBF\x8C"=>"\xE1\xBF\x83",	//8140=>8131
		"\xE1\xBF\xBC"=>"\xE1\xBF\xB3",	//8188=>8179
		"\xE2\x85\xA0"=>"\xE2\x85\xB0",	//8544=>8560
		"\xE2\x85\xA1"=>"\xE2\x85\xB1",	//8545=>8561
		"\xE2\x85\xA2"=>"\xE2\x85\xB2",	//8546=>8562
		"\xE2\x85\xA3"=>"\xE2\x85\xB3",	//8547=>8563
		"\xE2\x85\xA4"=>"\xE2\x85\xB4",	//8548=>8564
		"\xE2\x85\xA5"=>"\xE2\x85\xB5",	//8549=>8565
		"\xE2\x85\xA6"=>"\xE2\x85\xB6",	//8550=>8566
		"\xE2\x85\xA7"=>"\xE2\x85\xB7",	//8551=>8567
		"\xE2\x85\xA8"=>"\xE2\x85\xB8",	//8552=>8568
		"\xE2\x85\xA9"=>"\xE2\x85\xB9",	//8553=>8569
		"\xE2\x85\xAA"=>"\xE2\x85\xBA",	//8554=>8570
		"\xE2\x85\xAB"=>"\xE2\x85\xBB",	//8555=>8571
		"\xE2\x85\xAC"=>"\xE2\x85\xBC",	//8556=>8572
		"\xE2\x85\xAD"=>"\xE2\x85\xBD",	//8557=>8573
		"\xE2\x85\xAE"=>"\xE2\x85\xBE",	//8558=>8574
		"\xE2\x85\xAF"=>"\xE2\x85\xBF",	//8559=>8575
		"\xE2\x92\xB6"=>"\xE2\x93\x90",	//9398=>9424
		"\xE2\x92\xB7"=>"\xE2\x93\x91",	//9399=>9425
		"\xE2\x92\xB8"=>"\xE2\x93\x92",	//9400=>9426
		"\xE2\x92\xB9"=>"\xE2\x93\x93",	//9401=>9427
		"\xE2\x92\xBA"=>"\xE2\x93\x94",	//9402=>9428
		"\xE2\x92\xBB"=>"\xE2\x93\x95",	//9403=>9429
		"\xE2\x92\xBC"=>"\xE2\x93\x96",	//9404=>9430
		"\xE2\x92\xBD"=>"\xE2\x93\x97",	//9405=>9431
		"\xE2\x92\xBE"=>"\xE2\x93\x98",	//9406=>9432
		"\xE2\x92\xBF"=>"\xE2\x93\x99",	//9407=>9433
		"\xE2\x93\x80"=>"\xE2\x93\x9A",	//9408=>9434
		"\xE2\x93\x81"=>"\xE2\x93\x9B",	//9409=>9435
		"\xE2\x93\x82"=>"\xE2\x93\x9C",	//9410=>9436
		"\xE2\x93\x83"=>"\xE2\x93\x9D",	//9411=>9437
		"\xE2\x93\x84"=>"\xE2\x93\x9E",	//9412=>9438
		"\xE2\x93\x85"=>"\xE2\x93\x9F",	//9413=>9439
		"\xE2\x93\x86"=>"\xE2\x93\xA0",	//9414=>9440
		"\xE2\x93\x87"=>"\xE2\x93\xA1",	//9415=>9441
		"\xE2\x93\x88"=>"\xE2\x93\xA2",	//9416=>9442
		"\xE2\x93\x89"=>"\xE2\x93\xA3",	//9417=>9443
		"\xE2\x93\x8A"=>"\xE2\x93\xA4",	//9418=>9444
		"\xE2\x93\x8B"=>"\xE2\x93\xA5",	//9419=>9445
		"\xE2\x93\x8C"=>"\xE2\x93\xA6",	//9420=>9446
		"\xE2\x93\x8D"=>"\xE2\x93\xA7",	//9421=>9447
		"\xE2\x93\x8E"=>"\xE2\x93\xA8",	//9422=>9448
		"\xE2\x93\x8F"=>"\xE2\x93\xA9",	//9423=>9449
		"\xF0\x90\xA6"=>"\xF0\x91\x8E",	//66598=>66638
		"\xF0\x90\xA7"=>"\xF0\x91\x8F"	//66599=>66639
	);

	//quote and apostrophe curly=>straight
	const QUOTE_CHARS = array(
		//Windows codepage 1252
		"\xC2\x82"=>"'",		// U+0082⇒U+201A single low-9 quotation mark
		"\xC2\x84"=>'"',		// U+0084⇒U+201E double low-9 quotation mark
		"\xC2\x8B"=>"'",		// U+008B⇒U+2039 single left-pointing angle quotation mark
		"\xC2\x91"=>"'",		// U+0091⇒U+2018 left single quotation mark
		"\xC2\x92"=>"'",		// U+0092⇒U+2019 right single quotation mark
		"\xC2\x93"=>'"',		// U+0093⇒U+201C left double quotation mark
		"\xC2\x94"=>'"',		// U+0094⇒U+201D right double quotation mark
		"\xC2\x9B"=>"'",		// U+009B⇒U+203A single right-pointing angle quotation mark

		//Regular Unicode		// U+0022 quotation mark (")
								// U+0027 apostrophe     (')
		"\xC2\xAB"=>'"',		// U+00AB left-pointing double angle quotation mark
		"\xC2\xBB"=>'"',		// U+00BB right-pointing double angle quotation mark
		"\xE2\x80\x98"=>"'",	// U+2018 left single quotation mark
		"\xE2\x80\x99"=>"'",	// U+2019 right single quotation mark
		"\xE2\x80\x9A"=>"'",	// U+201A single low-9 quotation mark
		"\xE2\x80\x9B"=>"'",	// U+201B single high-reversed-9 quotation mark
		"\xE2\x80\x9C"=>'"',	// U+201C left double quotation mark
		"\xE2\x80\x9D"=>'"',	// U+201D right double quotation mark
		"\xE2\x80\x9E"=>'"',	// U+201E double low-9 quotation mark
		"\xE2\x80\x9F"=>'"',	// U+201F double high-reversed-9 quotation mark
		"\xE2\x80\xB9"=>"'",	// U+2039 single left-pointing angle quotation mark
		"\xE2\x80\xBA"=>"'",	// U+203A single right-pointing angle quotation mark
	);

	//characters to use in random string
	const RANDOM_CHARS = array(
		'A','B','C','D','E','F','G','H','J','K','L',
		'M','N','P','Q','R','S','T','U','V','W','X',
		'Y','Z','2','3','4','5','6','7','8','9'
	);



	//-------------------------------------------------
	// MIME Types

	const MIME_DEFAULT = 'application/octet-stream';



	//-------------------------------------------------
	// Geography

	const COUNTRIES = array(
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
		'GB'=>array(
			'name'=>'United Kingdom',
			'region'=>'Europe',
			'currency'=>'GBP'
		),
		'AF'=>array(
			'name'=>'Afghanistan',
			'region'=>'Asia',
			'currency'=>'AFN'
		),
		'AL'=>array(
			'name'=>'Albania',
			'region'=>'Europe',
			'currency'=>'ALL'
		),
		'AR'=>array(
			'name'=>'Argentina',
			'region'=>'South America',
			'currency'=>'ARS'
		),
		'AU'=>array(
			'name'=>'Australia',
			'region'=>'Australia',
			'currency'=>'AUD'
		),
		'AT'=>array(
			'name'=>'Austria',
			'region'=>'Europe',
			'currency'=>'EUR'
		),
		'BD'=>array(
			'name'=>'Bangladesh',
			'region'=>'Asia',
			'currency'=>'BDT'
		),
		'BE'=>array(
			'name'=>'Belgium',
			'region'=>'Europe',
			'currency'=>'EUR'
		),
		'BO'=>array(
			'name'=>'Bolivia',
			'region'=>'South America',
			'currency'=>'BOB'
		),
		'BA'=>array(
			'name'=>'Bosnia and Herzegovina',
			'region'=>'Europe',
			'currency'=>'BAM'
		),
		'BR'=>array(
			'name'=>'Brazil',
			'region'=>'South America',
			'currency'=>'BRL'
		),
		'BG'=>array(
			'name'=>'Bulgaria',
			'region'=>'Europe',
			'currency'=>'BGN'
		),
		'KH'=>array(
			'name'=>'Cambodia',
			'region'=>'Asia',
			'currency'=>'KHR'
		),
		'CL'=>array(
			'name'=>'Chile',
			'region'=>'South America',
			'currency'=>'CLP'
		),
		'CN'=>array(
			'name'=>'China',
			'region'=>'Asia',
			'currency'=>'CNY'
		),
		'CO'=>array(
			'name'=>'Colombia',
			'region'=>'South America',
			'currency'=>'COU'
		),
		'CR'=>array(
			'name'=>'Costa Rica',
			'region'=>'South America',
			'currency'=>'CRC'
		),
		'HR'=>array(
			'name'=>'Croatia',
			'region'=>'Europe',
			'currency'=>'HRK'
		),
		'CZ'=>array(
			'name'=>'Czech Republic',
			'region'=>'Europe',
			'currency'=>'CZK'
		),
		'DK'=>array(
			'name'=>'Denmark',
			'region'=>'Europe',
			'currency'=>'DKK'
		),
		'DO'=>array(
			'name'=>'Dominican Republic',
			'region'=>'North America',
			'currency'=>'DOP'
		),
		'EC'=>array(
			'name'=>'Ecuador',
			'region'=>'South America',
			'currency'=>'USD'
		),
		'EG'=>array(
			'name'=>'Egypt',
			'region'=>'Africa',
			'currency'=>'EGP'
		),
		'SV'=>array(
			'name'=>'El Salvador',
			'region'=>'North America',
			'currency'=>'SVC'
		),
		'EE'=>array(
			'name'=>'Estonia',
			'region'=>'Europe',
			'currency'=>'EUR'
		),
		'ET'=>array(
			'name'=>'Ethiopia',
			'region'=>'Africa',
			'currency'=>'ETB'
		),
		'FJ'=>array(
			'name'=>'Fiji',
			'region'=>'Australia',
			'currency'=>'FJD'
		),
		'FI'=>array(
			'name'=>'Finland',
			'region'=>'Europe',
			'currency'=>'EUR'
		),
		'FR'=>array(
			'name'=>'France',
			'region'=>'Europe',
			'currency'=>'EUR'
		),
		'DE'=>array(
			'name'=>'Germany',
			'region'=>'Europe',
			'currency'=>'EUR'
		),
		'GI'=>array(
			'name'=>'Gibraltar',
			'region'=>'Europe',
			'currency'=>'GIP'
		),
		'GR'=>array(
			'name'=>'Greece',
			'region'=>'Europe',
			'currency'=>'EUR'
		),
		'GL'=>array(
			'name'=>'Greenland',
			'region'=>'North America',
			'currency'=>'DKK'
		),
		'GU'=>array(
			'name'=>'Guam',
			'region'=>'Australia',
			'currency'=>'USD'
		),
		'GT'=>array(
			'name'=>'Guatemala',
			'region'=>'North America',
			'currency'=>'GTQ'
		),
		'GG'=>array(
			'name'=>'Guernsey',
			'region'=>'Europe',
			'currency'=>'GBP'
		),
		'HT'=>array(
			'name'=>'Haiti',
			'region'=>'North America',
			'currency'=>'USD'
		),
		'HN'=>array(
			'name'=>'Honduras',
			'region'=>'North America',
			'currency'=>'HNL'
		),
		'HK'=>array(
			'name'=>'Hong Kong',
			'region'=>'Asia',
			'currency'=>'HKD'
		),
		'HU'=>array(
			'name'=>'Hungary',
			'region'=>'Europe',
			'currency'=>'HUF'
		),
		'IS'=>array(
			'name'=>'Iceland',
			'region'=>'Europe',
			'currency'=>'ISK'
		),
		'IN'=>array(
			'name'=>'India',
			'region'=>'Asia',
			'currency'=>'INR'
		),
		'ID'=>array(
			'name'=>'Indonesia',
			'region'=>'Asia',
			'currency'=>'IDR'
		),
		'IQ'=>array(
			'name'=>'Iraq',
			'region'=>'Asia',
			'currency'=>'IQD'
		),
		'IE'=>array(
			'name'=>'Ireland',
			'region'=>'Europe',
			'currency'=>'EUR'
		),
		'IM'=>array(
			'name'=>'Isle of Man',
			'region'=>'Europe',
			'currency'=>'GBP'
		),
		'IL'=>array(
			'name'=>'Israel',
			'region'=>'Asia',
			'currency'=>'ILS'
		),
		'IT'=>array(
			'name'=>'Italy',
			'region'=>'Europe',
			'currency'=>'EUR'
		),
		'JM'=>array(
			'name'=>'Jamaica',
			'region'=>'North America',
			'currency'=>'JMD'
		),
		'JP'=>array(
			'name'=>'Japan',
			'region'=>'Asia',
			'currency'=>'JPY'
		),
		'JE'=>array(
			'name'=>'Jersey',
			'region'=>'Europe',
			'currency'=>'GBP'
		),
		'JO'=>array(
			'name'=>'Jordan',
			'region'=>'Asia',
			'currency'=>'JOD'
		),
		'KZ'=>array(
			'name'=>'Kazakhstan',
			'region'=>'Asia',
			'currency'=>'KZT'
		),
		'KE'=>array(
			'name'=>'Kenya',
			'region'=>'Africa',
			'currency'=>'KES'
		),
		'KR'=>array(
			'name'=>'Korea, South',
			'region'=>'Asia',
			'currency'=>'KRW'
		),
		'KW'=>array(
			'name'=>'Kuwait',
			'region'=>'Asia',
			'currency'=>'KWD'
		),
		'KG'=>array(
			'name'=>'Kyrgyzstan',
			'region'=>'Asia',
			'currency'=>'KGS'
		),
		'LA'=>array(
			'name'=>'Laos',
			'region'=>'Asia',
			'currency'=>'LAK'
		),
		'LV'=>array(
			'name'=>'Latvia',
			'region'=>'Europe',
			'currency'=>'EUR'
		),
		'LB'=>array(
			'name'=>'Lebanon',
			'region'=>'Asia',
			'currency'=>'LBP'
		),
		'LI'=>array(
			'name'=>'Liechtenstein',
			'region'=>'Europe',
			'currency'=>'CHF'
		),
		'LT'=>array(
			'name'=>'Lithuania',
			'region'=>'Europe',
			'currency'=>'LTL'
		),
		'LU'=>array(
			'name'=>'Luxembourg',
			'region'=>'Europe',
			'currency'=>'EUR'
		),
		'MO'=>array(
			'name'=>'Macao',
			'region'=>'Asia',
			'currency'=>'MOP'
		),
		'MK'=>array(
			'name'=>'Macedonia',
			'region'=>'Europe',
			'currency'=>'MKD'
		),
		'MG'=>array(
			'name'=>'Madagascar',
			'region'=>'Africa',
			'currency'=>'MGA'
		),
		'MW'=>array(
			'name'=>'Malawi',
			'region'=>'Africa',
			'currency'=>'MWK'
		),
		'MY'=>array(
			'name'=>'Malaysia',
			'region'=>'Asia',
			'currency'=>'MYR'
		),
		'MT'=>array(
			'name'=>'Malta',
			'region'=>'Europe',
			'currency'=>'EUR'
		),
		'MX'=>array(
			'name'=>'Mexico',
			'region'=>'North America',
			'currency'=>'MXV'
		),
		'MD'=>array(
			'name'=>'Moldova',
			'region'=>'Europe',
			'currency'=>'MDL'
		),
		'MC'=>array(
			'name'=>'Monaco',
			'region'=>'Europe',
			'currency'=>'EUR'
		),
		'MN'=>array(
			'name'=>'Mongolia',
			'region'=>'Asia',
			'currency'=>'MNT'
		),
		'ME'=>array(
			'name'=>'Montenegro',
			'region'=>'Europe',
			'currency'=>'EUR'
		),
		'MA'=>array(
			'name'=>'Morocco',
			'region'=>'Africa',
			'currency'=>'MAD'
		),
		'MZ'=>array(
			'name'=>'Mozambique',
			'region'=>'Africa',
			'currency'=>'MZN'
		),
		'MM'=>array(
			'name'=>'Myanmar (Burma)',
			'region'=>'Asia',
			'currency'=>'MMK'
		),
		'NA'=>array(
			'name'=>'Namibia',
			'region'=>'Africa',
			'currency'=>'NAD'
		),
		'NP'=>array(
			'name'=>'Nepal',
			'region'=>'Asia',
			'currency'=>'NPR'
		),
		'NL'=>array(
			'name'=>'Netherlands',
			'region'=>'Europe',
			'currency'=>'EUR'
		),
		'NZ'=>array(
			'name'=>'New Zealand',
			'region'=>'Australia',
			'currency'=>'NZD'
		),
		'NI'=>array(
			'name'=>'Nicaragua',
			'region'=>'North America',
			'currency'=>'NIO'
		),
		'NO'=>array(
			'name'=>'Norway',
			'region'=>'Europe',
			'currency'=>'NOK'
		),
		'PK'=>array(
			'name'=>'Pakistan',
			'region'=>'Asia',
			'currency'=>'PKR'
		),
		'PA'=>array(
			'name'=>'Panama',
			'region'=>'North America',
			'currency'=>'PAB'
		),
		'PG'=>array(
			'name'=>'Papua New Guinea',
			'region'=>'Australia',
			'currency'=>'PGK'
		),
		'PY'=>array(
			'name'=>'Paraguay',
			'region'=>'South America',
			'currency'=>'PYG'
		),
		'PE'=>array(
			'name'=>'Peru',
			'region'=>'South America',
			'currency'=>'PEN'
		),
		'PH'=>array(
			'name'=>'Philippines',
			'region'=>'Asia',
			'currency'=>'PHP'
		),
		'PL'=>array(
			'name'=>'Poland',
			'region'=>'Europe',
			'currency'=>'PLN'
		),
		'PT'=>array(
			'name'=>'Portugal',
			'region'=>'Europe',
			'currency'=>'EUR'
		),
		'PR'=>array(
			'name'=>'Puerto Rico',
			'region'=>'North America',
			'currency'=>'USD'
		),
		'QA'=>array(
			'name'=>'Qatar',
			'region'=>'Asia',
			'currency'=>'QAR'
		),
		'RO'=>array(
			'name'=>'Romania',
			'region'=>'Europe',
			'currency'=>'RON'
		),
		'RU'=>array(
			'name'=>'Russia',
			'region'=>'Europe',
			'currency'=>'RUB'
		),
		'RW'=>array(
			'name'=>'Rwanda',
			'region'=>'Africa',
			'currency'=>'RWF'
		),
		'SM'=>array(
			'name'=>'San Marino',
			'region'=>'Europe',
			'currency'=>'EUR'
		),
		'SA'=>array(
			'name'=>'Saudi Arabia',
			'region'=>'Asia',
			'currency'=>'SAR'
		),
		'SN'=>array(
			'name'=>'Senegal',
			'region'=>'Africa',
			'currency'=>'XOF'
		),
		'RS'=>array(
			'name'=>'Serbia',
			'region'=>'Europe',
			'currency'=>'RSD'
		),
		'SL'=>array(
			'name'=>'Sierra Leone',
			'region'=>'Africa',
			'currency'=>'SLL'
		),
		'SG'=>array(
			'name'=>'Singapore',
			'region'=>'Asia',
			'currency'=>'SGD'
		),
		'SK'=>array(
			'name'=>'Slovakia',
			'region'=>'Europe',
			'currency'=>'EUR'
		),
		'SI'=>array(
			'name'=>'Slovenia',
			'region'=>'Europe',
			'currency'=>'EUR'
		),
		'ZA'=>array(
			'name'=>'South Africa',
			'region'=>'Africa',
			'currency'=>'ZAR'
		),
		'ES'=>array(
			'name'=>'Spain',
			'region'=>'Europe',
			'currency'=>'EUR'
		),
		'LK'=>array(
			'name'=>'Sri Lanka',
			'region'=>'Asia',
			'currency'=>'LKR'
		),
		'SZ'=>array(
			'name'=>'Swaziland',
			'region'=>'Africa',
			'currency'=>'SZL'
		),
		'SE'=>array(
			'name'=>'Sweden',
			'region'=>'Europe',
			'currency'=>'SEK'
		),
		'CH'=>array(
			'name'=>'Switzerland',
			'region'=>'Europe',
			'currency'=>'CHW'
		),
		'SY'=>array(
			'name'=>'Syrian Arab Republic',
			'region'=>'Asia',
			'currency'=>'SYP'
		),
		'TW'=>array(
			'name'=>'Taiwan',
			'region'=>'Asia',
			'currency'=>'TWD'
		),
		'TJ'=>array(
			'name'=>'Tajikistan',
			'region'=>'Asia',
			'currency'=>'TJS'
		),
		'TZ'=>array(
			'name'=>'Tanzania',
			'region'=>'Africa',
			'currency'=>'TZS'
		),
		'TH'=>array(
			'name'=>'Thailand',
			'region'=>'Asia',
			'currency'=>'THB'
		),
		'TN'=>array(
			'name'=>'Tunisia',
			'region'=>'Africa',
			'currency'=>'TND'
		),
		'TR'=>array(
			'name'=>'Turkey',
			'region'=>'Europe',
			'currency'=>'TRY'
		),
		'VI'=>array(
			'name'=>'U.S. Virgin Islands',
			'region'=>'North America',
			'currency'=>'USD'
		),
		'UA'=>array(
			'name'=>'Ukraine',
			'region'=>'Europe',
			'currency'=>'UAH'
		),
		'AE'=>array(
			'name'=>'United Arab Emirates',
			'region'=>'Asia',
			'currency'=>'AED'
		),
		'UY'=>array(
			'name'=>'Uruguay',
			'region'=>'South America',
			'currency'=>'UYU'
		),
		'UZ'=>array(
			'name'=>'Uzbekistan',
			'region'=>'Asia',
			'currency'=>'UZS'
		),
		'VA'=>array(
			'name'=>'Vatican City',
			'region'=>'Europe',
			'currency'=>'EUR'
		),
		'VE'=>array(
			'name'=>'Venezuela',
			'region'=>'South America',
			'currency'=>'VEF'
		),
		'VU'=>array(
			'name'=>'Vietnam',
			'region'=>'Asia',
			'currency'=>'VND'
		)
	);

	const PROVINCES = array(
		'AB'=>'Alberta',
		'BC'=>'British Columbia',
		'MB'=>'Manitoba',
		'NB'=>'New Brunswick',
		'NL'=>'Newfoundland',
		'NT'=>'Northwest Territories',
		'NS'=>'Nova Scotia',
		'NU'=>'Nunavut',
		'ON'=>'Ontario',
		'PE'=>'Prince Edward Island',
		'QC'=>'Quebec',
		'SK'=>'Saskatchewan',
		'YT'=>'Yukon'
	);

	const REGIONS = array(
		'Africa',
		'Asia',
		'Australia',
		'Europe',
		'North America',
		'South America'
	);

	const STATES = array(
		'AL'=>'Alabama',
		'AK'=>'Alaska',
		'AZ'=>'Arizona',
		'AR'=>'Arkansas',
		'CA'=>'California',
		'CO'=>'Colorado',
		'CT'=>'Connecticut',
		'DE'=>'Delaware',
		'DC'=>'District of Columbia',
		'FL'=>'Florida',
		'GA'=>'Georgia',
		'HI'=>'Hawaii',
		'ID'=>'Idaho',
		'IL'=>'Illinois',
		'IN'=>'Indiana',
		'IA'=>'Iowa',
		'KS'=>'Kansas',
		'KY'=>'Kentucky',
		'LA'=>'Louisiana',
		'ME'=>'Maine',
		'MD'=>'Maryland',
		'MA'=>'Massachusetts',
		'MI'=>'Michigan',
		'MN'=>'Minnesota',
		'MS'=>'Mississippi',
		'MO'=>'Missouri',
		'MT'=>'Montana',
		'NE'=>'Nebraska',
		'NV'=>'Nevada',
		'NH'=>'New Hampshire',
		'NJ'=>'New Jersey',
		'NM'=>'New Mexico',
		'NY'=>'New York',
		'NC'=>'North Carolina',
		'ND'=>'North Dakota',
		'OH'=>'Ohio',
		'OK'=>'Oklahoma',
		'OR'=>'Oregon',
		'PA'=>'Pennsylvania',
		'RI'=>'Rhode Island',
		'SC'=>'South Carolina',
		'SD'=>'South Dakota',
		'TN'=>'Tennessee',
		'TX'=>'Texas',
		'UT'=>'Utah',
		'VT'=>'Vermont',
		'VA'=>'Virginia',
		'WA'=>'Washington',
		'WV'=>'West Virginia',
		'WI'=>'Wisconsin',
		'WY'=>'Wyoming',
		'AA'=>'Armed Forces Americas',
		'AE'=>'Armed Forces Europe',
		'AP'=>'Armed Forces Pacific',
		'AS'=>'American Samoa',
		'FM'=>'Federated States of Micronesia',
		'GU'=>'Guam Gu',
		'MH'=>'Marshall Islands',
		'MP'=>'Northern Mariana Islands',
		'PW'=>'Palau',
		'PR'=>'Puerto Rico',
		'VI'=>'Virgin Islands'
	);



	//-------------------------------------------------
	// Miscellaneous

	//from e.g. parse_url
	const URL_PARTS = array(
		'scheme'=>'',
		'host'=>'',
		'user'=>'',
		'pass'=>'',
		'port'=>'',
		'path'=>'',
		'query'=>'',
		'fragment'=>''
	);

	//svg attribute corrections
	const SVG_ATTR_CORRECTIONS = array(
		'xmlns="&ns_svg;"'=>'xmlns="http://www.w3.org/2000/svg"',
		'xmlns:xlink="&ns_xlink;"'=>'xmlns:xlink="http://www.w3.org/1999/xlink"',
		'id="Layer_1"'=>''
	);

	//clean svg options
	const SVG_CLEAN_OPTIONS = array(
		'random_id'=>false,
		'strip_title'=>false
	);

	//blank image
	const BLANK_IMAGE = 'data:image/gif;base64,R0lGODlhAQABAIAAAP///wAAACwAAAAAAQABA
AACAkQBADs=';

	//default binary paths for WebP
	const CWEBP = '/usr/bin/cwebp';
	const GIF2WEBP = '/usr/bin/gif2webp';

	//excerpt arguments
	const EXCERPT = array(
		'length'=>200,
		'suffix'=>'…',
		'unit'=>'character'
	);

	//truthy bools
	const TRUE_BOOLS = array(
		'1',
		'on',
		'true',
		'yes'
	);

	//falsey bools
	const FALSE_BOOLS = array(
		'0',
		'off',
		'false',
		'no'
	);
}

?>