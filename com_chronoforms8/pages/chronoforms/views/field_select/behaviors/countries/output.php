<?php
/**
* ChronoForms 8
* Copyright (c) 2023 ChronoEngine.com, All rights reserved.
* Author: (ChronoEngine.com Team)
* license:     GNU General Public License version 2 or later; see LICENSE.txt
* Visit http://www.ChronoEngine.com for regular updates and information.
**/
defined('_JEXEC') or die('Restricted access');

$list = [
	"AF" => "Afghanistan",
	"AX" => "Aland Islands",
	"AL" => "Albania",
	"DZ" => "Algeria",
	"AS" => "American Samoa",
	"AD" => "Andorra",
	"AO" => "Angola",
	"AI" => "Anguilla",
	"AG" => "Antigua",
	"AR" => "Argentina",
	"AM" => "Armenia",
	"AW" => "Aruba",
	"AU" => "Australia",
	"AT" => "Austria",
	"AZ" => "Azerbaijan",
	"BS" => "Bahamas",
	"BH" => "Bahrain",
	"BD" => "Bangladesh",
	"BB" => "Barbados",
	"BY" => "Belarus",
	"BE" => "Belgium",
	"BZ" => "Belize",
	"BJ" => "Benin",
	"BM" => "Bermuda",
	"BT" => "Bhutan",
	"BO" => "Bolivia",
	"BA" => "Bosnia",
	"BW" => "Botswana",
	"BV" => "Bouvet Island",
	"BR" => "Brazil",
	"VG" => "British Virgin Islands",
	"BN" => "Brunei",
	"BG" => "Bulgaria",
	"BF" => "Burkina Faso",
	"MM" => "Burma",
	"BI" => "Burundi",
	"TC" => "Caicos Islands",
	"KH" => "Cambodia",
	"CM" => "Cameroon",
	"CA" => "Canada",
	"CV" => "Cape Verde",
	"KY" => "Cayman Islands",
	"CF" => "Central African Republic",
	"TD" => "Chad",
	"CL" => "Chile",
	"CN" => "China",
	"CX" => "Christmas Island",
	"CC" => "Cocos Islands",
	"CO" => "Colombia",
	"KM" => "Comoros",
	"CG" => "Congo Brazzaville",
	"CD" => "Congo",
	"CK" => "Cook Islands",
	"CR" => "Costa Rica",
	"CI" => "Cote Divoire",
	"HR" => "Croatia",
	"CU" => "Cuba",
	"CY" => "Cyprus",
	"CZ" => "Czech Republic",
	"DK" => "Denmark",
	"DJ" => "Djibouti",
	"DM" => "Dominica",
	"DO" => "Dominican Republic",
	"EC" => "Ecuador",
	"EG" => "Egypt",
	"SV" => "El Salvador",
	// "GB" => "United Kingdom",
	"GQ" => "Equatorial Guinea",
	"ER" => "Eritrea",
	"EE" => "Estonia",
	"ET" => "Ethiopia",
	"EU" => "European Union",
	"FK" => "Falkland Islands",
	"FO" => "Faroe Islands",
	"FJ" => "Fiji",
	"FI" => "Finland",
	"FR" => "France",
	"GF" => "French Guiana",
	"PF" => "French Polynesia",
	"TF" => "French Territories",
	"GA" => "Gabon",
	"GM" => "Gambia",
	"GE" => "Georgia",
	"DE" => "Germany",
	"GH" => "Ghana",
	"GI" => "Gibraltar",
	"GR" => "Greece",
	"GL" => "Greenland",
	"GD" => "Grenada",
	"GP" => "Guadeloupe",
	"GU" => "Guam",
	"GT" => "Guatemala",
	"GW" => "Guinea-Bissau",
	"GN" => "Guinea",
	"GY" => "Guyana",
	"HT" => "Haiti",
	"HM" => "Heard Island",
	"HN" => "Honduras",
	"HK" => "Hong Kong",
	"HU" => "Hungary",
	"IS" => "Iceland",
	"IN" => "India",
	"IO" => "Indian Ocean Territory",
	"ID" => "Indonesia",
	"IR" => "Iran",
	"IQ" => "Iraq",
	"IE" => "Ireland",
	"IL" => "Israel",
	"IT" => "Italy",
	"JM" => "Jamaica",
	"JP" => "Japan",
	"JO" => "Jordan",
	"KZ" => "Kazakhstan",
	"KE" => "Kenya",
	"KI" => "Kiribati",
	"KW" => "Kuwait",
	"KG" => "Kyrgyzstan",
	"LA" => "Laos",
	"LV" => "Latvia",
	"LB" => "Lebanon",
	"LS" => "Lesotho",
	"LR" => "Liberia",
	"LY" => "Libya",
	"LI" => "Liechtenstein",
	"LT" => "Lithuania",
	"LU" => "Luxembourg",
	"MO" => "Macau",
	"MK" => "Macedonia",
	"MG" => "Madagascar",
	"MW" => "Malawi",
	"MY" => "Malaysia",
	"MV" => "Maldives",
	"ML" => "Mali",
	"MT" => "Malta",
	"MH" => "Marshall Islands",
	"MQ" => "Martinique",
	"MR" => "Mauritania",
	"MU" => "Mauritius",
	"YT" => "Mayotte",
	"MX" => "Mexico",
	"FM" => "Micronesia",
	"MD" => "Moldova",
	"MC" => "Monaco",
	"MN" => "Mongolia",
	"ME" => "Montenegro",
	"MS" => "Montserrat",
	"MA" => "Morocco",
	"MZ" => "Mozambique",
	"NA" => "Namibia",
	"NR" => "Nauru",
	"NP" => "Nepal",
	"AN" => "Netherlands Antilles",
	"NL" => "Netherlands",
	"NC" => "New Caledonia",
	"PG" => "New Guinea",
	"NZ" => "New Zealand",
	"NI" => "Nicaragua",
	"NE" => "Niger",
	"NG" => "Nigeria",
	"NU" => "Niue",
	"NF" => "Norfolk Island",
	"KP" => "North Korea",
	"MP" => "Northern Mariana Islands",
	"NO" => "Norway",
	"OM" => "Oman",
	"PK" => "Pakistan",
	"PW" => "Palau",
	"PS" => "Palestine",
	"PA" => "Panama",
	"PY" => "Paraguay",
	"PE" => "Peru",
	"PH" => "Philippines",
	"PN" => "Pitcairn Islands",
	"PL" => "Poland",
	"PT" => "Portugal",
	"PR" => "Puerto Rico",
	"QA" => "Qatar",
	"RE" => "Reunion",
	"RO" => "Romania",
	"RU" => "Russia",
	"RW" => "Rwanda",
	"SH" => "Saint Helena",
	"KN" => "Saint Kitts and Nevis",
	"LC" => "Saint Lucia",
	"PM" => "Saint Pierre",
	"VC" => "Saint Vincent",
	"WS" => "Samoa",
	"SM" => "San Marino",
	"GS" => "Sandwich Islands",
	"ST" => "Sao Tome",
	"SA" => "Saudi Arabia",
	"SN" => "Senegal",
	"CS" => "Serbia",
	"RS" => "Serbia",
	"SC" => "Seychelles",
	"SL" => "Sierra Leone",
	"SG" => "Singapore",
	"SK" => "Slovakia",
	"SI" => "Slovenia",
	"SB" => "Solomon Islands",
	"SO" => "Somalia",
	"ZA" => "South Africa",
	"KR" => "South Korea",
	"ES" => "Spain",
	"LK" => "Sri Lanka",
	"SD" => "Sudan",
	"SR" => "Suriname",
	"SJ" => "Svalbard",
	"SZ" => "Swaziland",
	"SE" => "Sweden",
	"CH" => "Switzerland",
	"SY" => "Syria",
	"TW" => "Taiwan",
	"TJ" => "Tajikistan",
	"TZ" => "Tanzania",
	"TH" => "Thailand",
	"TL" => "Timorleste",
	"TG" => "Togo",
	"TK" => "Tokelau",
	"TO" => "Tonga",
	"TT" => "Trinidad",
	"TN" => "Tunisia",
	"TR" => "Turkey",
	"TM" => "Turkmenistan",
	"TV" => "Tuvalu",
	"UG" => "Uganda",
	"UA" => "Ukraine",
	"AE" => "United Arab Emirates",
	"UK" => "United Kingdom",
	"US" => "United States",
	"UY" => "Uruguay",
	"UM" => "Us Minor Islands",
	"VI" => "Us Virgin Islands",
	"UZ" => "Uzbekistan",
	"VU" => "Vanuatu",
	"VA" => "Vatican City",
	"VE" => "Venezuela",
	"VN" => "Vietnam",
	"WF" => "Wallis and Futuna",
	"EH" => "Western Sahara",
	"YE" => "Yemen",
	"ZM" => "Zambia",
	"ZW" => "Zimbabwe",
];

foreach($list as $code => $country){
	if(!empty($element["countries"]["iso_value"])){
		$element["options"] = trim($element["options"]) . "\n" . $code."=".$country;
	}else{
		$element["options"] = trim($element["options"]) . "\n" . $country."=".$country;
	}
}

$element["options"] = trim($element["options"]);