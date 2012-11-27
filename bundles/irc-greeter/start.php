<?php

/**
 * Greets people as they join a channel the bot is in.
 *
 * @package  IRC-Greeter
 * @category  Bundle
 * @author  Phill Sparks <me@phills.me.uk>
 * @copyright  2012 Phill Sparks
 * @license   MIT License <http://www.opensource.org/licenses/mit>
 */

use IRC\Message;

// List of users to always greet, nicks lowercase, and an optional custom greeting
$greet = array(
	'daylerees'    => "Welsh",
	'ericbarnes'   => null,
	'jasonlewis'   => "Australian",
	'ianlandsman'  => null,
	'phillsparks'  => "Ferengi",
	'shawnmccool'  => "Dutch",
	'taylorotwell' => null,
    'joellarson'   => "Gangnam Style",
);

foreach ($greet as $nick => $lang)
{
	Cache::put("irc-language-$nick", $lang, 3.15569e7);
}

// List of greetings from around the world
// @link http://www.rogerdarlington.me.uk/Goodmorning.html
$greetings = array(
	"Afrikaans" => "Goeie môre",
	"Albanian" => "Mirëmëngjes", "shqip" => "Mirëmëngjes",
	"Amharic" => "Endermen aderkh", "amarəñña" => "Endermen aderkh",
	"Arabic" => "Sabah-il-kheir", "al arabiya" => "Sabah-il-kheir",
	"Aramaic" => "Yasetel liesbukh", "lišānā 'aramā'ā" => "Yasetel liesbukh",
	"Armenian" => "Bari luys", "Hayeren" => "Bari luys", "Hayeren lezou" => "Bari luys",
	"Assyrian" => "Kedamtookh brikhta",
	"Azerbaijani" => "Sabahiniz Xeyr", "Azərbaycan dili" => "Sabahiniz Xeyr", "Азәрбајҹан дили" => "Sabahiniz Xeyr",
	"Bamougoum" => "Oli yah",
	"Bangla" => "Shuvo sokal",
	"Basque" => "Egun on", "euskara" => "Egun on",
	"Belarussian" => "Dobray ranitsy",
	"Bemba" => "Mwashibukeni", "iciBemba" => "Mwashibukeni", "ciBemba" => "Mwashibukeni", "ichiBemba" => "Mwashibukeni", "chiBemba" => "Mwashibukeni",
	"Bengali" => "Shu-probhaat", "baɛṅlā" => "Shu-probhaat",
	"Bisaya" => "Maayong adlaw",
	"Bosnian" => "Dobro jutro", "Bosanski" => "Dobro jutro",
	"Bulgarian" => "Dobro utro", "bãlgarski" => "Dobro utro", "bãlgarski ezik" => "Dobro utro",
	"Cantonese" => "Zou san", "gwóngdōngwá" => "Zou san", "yuetyuh" => "Zou san", // Chinese
	"Catalan" => "Bon dia", "català" => "Bon dia",
	"Cebuano" => "Maayong buntag!", "Sinugboanon" => "Maayong buntag!", "Sugboanon" => "Maayong buntag!",
	"Chichewa" => "Mwadzuka bwanji", "Chicheŵa" => "Mwadzuka bwanji",
	"Chinese: Mandarin" => "Zao shang hao", "pŭtōnghùa" => "Zao shang hao", "gúoyŭ" => "Zao shang hao", "húayŭ" => "Zao shang hao",
	"Cornish" => "Myttin da", "Kernewek" => "Myttin da", "Kernowek" => "Myttin da", "Kernuak" => "Myttin da", "Curnoack" => "Myttin da",
	"Corse" => "Bun ghjiornu",
	"Creole" => "Bonjou", "Kreyòl ayisyen" => "Bonjou", // Haitian
	"Croatian" => "Dobro jutro", "Hrvatski" => "Dobro jutro",
	"Czech" => "Dobré ráno", "čeština", "český jazyk",
	"Danish" => "God morgen", "dansk" => "God morgen",
	"Dari" => "Sob Bakhaer",
	"Divehi" => "Baajjaveri hedhuneh",
	"Dutch" => "Goedemorgen", "Nederlands" => "Goedemorgen",
	"English" => "Good morning",
	"Esperanto" => "Bonan matenon", "Esperanto" => "Bonan matenon",
	"Estonian" => "Tere hommikust", "eesti keel" => "Tere hommikust",
	"Etsakor" => "Naigbia",
	"Fanti" => "Me ma wo akye",
	"Fijian" => "Sa Yadra", "Vakaviti" => "Sa Yadra",
	"Filipino" => "Magandang umaga po",
	"Finnish" => "Hyvää huomenta", "suomi" => "Hyvää huomenta", "suomen kieli" => "Hyvää huomenta",
	"Flemish" => "Goeie morgen", "Vlaams",
	"French" => "Bonjour", "français" => "Bonjour",
	"Frisian" => "Goeie moarn", "Noordfreesk" => "Goeie moarn", "Nordfrasch" => "Goeie moarn", "Frysk" => "Goeie moarn",
    "Galician" => "Bos dias", "Galego" => "Bos dias",
	"Georgian" => "Dila mshvidobisa", "kʻartʻuli", "kʻartʻuli ena",
	"German" => "Guten Morgen", "Deutsch" => "Guten Morgen",
	"Greek" => "Kali mera", "ellēniká" => "Kali mera",
	"Greenlandic" => "Iterluarit", "Kalaallisut" => "Iterluarit",
	"Gujarati" => "Subh Prabhat", "gujarātī" => "Subh Prabhat",
	"Hakka" => "On zoh", // Chinese
	"Hausa" => "Inaa kwana",
	"Hawaiian" => "Aloha kakahiaka", "ʻōlelo Hawaiʻi" => "Aloha kakahiaka",
	"Hebrew" => "Boker tov",
	"Hiligaynon" => "Maayong aga",
	"Hindi" => "Shubh prabhat",
	"Hungarian" => "Jo reggelt", "magyar" => "Jo reggelt",
	"Icelandic" => "Gódan daginn", "Íslenska" => "Gódan daginn",
	"Ilocano" => "Naimbag nga Aldaw", "ilokano" => "Naimbag nga Aldaw",
	"Indonesian" => "Selamat pagi",
	"Irish Gaelic" => "Dia duit ar maidin", "Gaeilge" => "Dia duit ar maidin",
	"Italian" => "Buon giorno", "italiano" => "Buon giorno",
	"Japanese" => "Ohayo gazaimasu", "nihongo" => "Ohayo gazaimasu",
	"Kannada" => "Shubhodaya", "kannaḍa" => "Shubhodaya",
	"Kapampangan" => "Mayap a abak",
	"Kazakh" => "Kairly Tan",
	"Khmer" => "Arrun Suo Sdey",
	"Kimeru" => "Muga rukiiri",
	"Kinyarwanda" => "Muraho", "Ikinyarwanda" => "Muraho",
	"Konkani" => "Dev Tuka Boro Dis Divum", "kōṅkaṇī" => "Dev Tuka Boro Dis Divum", "koṅkaṇi" => "Dev Tuka Boro Dis Divum",
	"Korean" => "Annyunghaseyo", "han-guk-eo" => "Annyunghaseyo",
	"Kurdish Badini" => "Spede bash",
	"Kurdish Sorani" => "Beyani bash",
	"Lao" => "Sabaidee",
	"Latvian" => "Labrit", "latviešu valoda" => "Labrit",
	"Lithuanian" => "Labas reytas", "lietuvių kalba" => "Labas reytas",
	"Lozi" => "U zuhile",
	"Luganda" => "Wasuze otyano",
	"Luo" => "Oyawore", "Dholuo" => "Oyawore",
	"Luxembourg" => "Gudde moien", "Lëtzebuergesch" => "Gudde moien", "Luxembourgish" => "Gudde moien",
	"Macedonian" => "Dobro utro", "Makedonski" => "Dobro utro", "makedonski jazik" => "Dobro utro",
	"Malayalam" => "Suprabhatham", "malayāḷam" => "Suprabhatham",
	"Malay" => "Selamat pagi", "Bahasa melayu" => "Selamat pagi",
	"Maltese" => "Għodwa it-tajba", "Malti" => "Għodwa it-tajba",
	"Manx" => "Moghrey mie", "Gaelg" => "Moghrey mie", "Gailck" => "Moghrey mie",
	"Maori" => "Ata marie", "te Reo Māori" => "Ata marie",
	"Mapudungun" => "Mari mari",
	"Marathi" => "Suprabhat", "marāṭhī" => "Suprabhat",
	"Mongolian" => "Öglouny mend", "монгол" => "Öglouny mend",
	"Navajo" => "Yá'át'ééh abíní", "Diné Bizaad" => "Yá'át'ééh abíní",
	"Ndebele" => "Livukenjani",
	"Nepali" => "Subha prabhat", "nēpālī" => "Subha prabhat",
	"Northern Sotho" => "Thobela", "Sesotho sa Leboa" => "Thobela",
	"Norwegian" => "God morgen", "Norsk" => "God morgen",
	"Owambo" => "Wa lalapo",
	"Pashto" => "Sahar de pa Khair",
	"Persian" => "Subbakhair",
	"Polish" => "Dzien dobry", "polski" => "Dzien dobry", "język polski" => "Dzien dobry", "polszczyzna" => "Dzien dobry",
	"Polynesian" => "Ia ora na",
	"Portuguese" => "Bom dia", "português" => "Bom dia",
	"Punjabi" => "Sat Shri Akal", "panjābi" => "Sat Shri Akal",
	"Rapa Nui" => "Iorana",
	"Romanian" => "Buna dimineata", "limba română" => "Buna dimineata", "român"=> "Buna dimineata",
	"Russian" => "Dobraye ootra", "Русский язык" => "Dobraye ootra", "jazyk" => "Dobraye ootra",
	"Samoan" => "Talofa lava", "Gagana Samoa" => "Talofa lava",
	"Sanskrit" => "Suprabhataha",
	"Sardinian" => "Bona dia", "Limba Sarda" => "Bona dia", "sardu" => "Bona dia",
	"Serbian" => "Dobro jutro", "српски" => "Dobro jutro",
	"Shona" => "Mangwanani", "chiShona" => "Mangwanani",
	"Sinhalese" => "Suba Udesanak Wewa",
	"Slovak" => "Dobré ráno", "slovenčina" => "Dobré ráno",
	"Slovenian" => "Dobro jutro", "slovenščina" => "Dobro jutro",
	"Somalian" => "Subax wanaagsan",
	"Southern Sotho" => "Dumela", "seSotho" => "Dumela",
	"Spanish" => "Buenos dias", "español" => "Buenos dias",
	"Swahili" => "Habari za asubuhi", "Kiswahili" => "Habari za asubuhi",
	"Swazi" => "Sawubona",
	"Swedish" => "God morgon", "Svenska" => "God morgon", "Bork Bork" => "God morgon",
	"Tagalog" => "Magandang umaga", "Tagalog" => "Magandang umaga",
	"Taiwanese" => "Gau cha",
	"Tamil" => "Kaalai Vannakkam",
	"Telugu" => "Subhodayamu",
	"Tetum" => "Dader diak", "Tetun" => "Dader diak",
	"Thai" => "Aroon-Sawass", "paasaa-tai" => "Aroon-Sawass",
	"Tibetan" => "Nyado delek", "pö-gay" => "Nyado delek",
	"Tonga" => "Mwabuka buti", "chiTonga" => "Mwabuka buti",
	"Tswana" => "Dumela", "Setswana" => "Dumela",
	"Turkish" => "Günaydin", "Türkçe" => "Günaydin",
	"Turkmen" => "Ertiringiz haiyirli bolsun", "түркmенче" => "Ertiringiz haiyirli bolsun",
	"Ukrainian" => "Dobri ranok", "Українська" => "Dobri ranok",
	"Urdu" => "Subha Ba-khair",
	"Uzbek" => "Khairli kun",
	"Vietnamese" => "Xin chao", "tiếng việt" => "Xin chao",
	"Welsh" => "Bore da", "Cymraeg" => "Bore da",
	"Xhosa" => "Bhota", "isiXhosa" => "Bhota",
	"Xitsonga" => "Avuxeni", "Tsonga" => "Avuxeni",
	"Yoruba" => "E karo",
	"Zulu" => "Sawubona", "isiZulu" => "Sawubona",
	"!Kung san" => "Tuwa",
	"Australian" => "G'day",
	"Klingon" => "nuqneH", "tlhIngan Hol" => "nuqneH",
	"Vulcan" => "Dif-tor heh smusma",
	"Ferengi" => "Welcome to our home. Please place your thumbprint on the legal waivers and deposit your admission fee in the slot by the door. Remember, my house is my house. ",
    "Gangnam Style" => "Eeeeeey sexy",
);

$languages = array_keys($greetings);
$lowerlang = array_map('strtolower', $languages);

$langreg = '/\b(?:'.implode('|', array_map('preg_quote', $languages)).')\b/i';

$greeter = function($message) use ($greetings)
{
	$nick = strtolower($message->sender->nick);
	$langkey = "irc-language-$nick";
	if (Cache::has($langkey))
	{
		// Use the cache to prevent re-greeting more than once per hour,
		// useful if someone has connection issues or if they get host-cloaked
		// by the server
		$greetkey = "irc-greeted-$nick";
		if (! Cache::has($greetkey))
		{
			Cache::put($greetkey, time(), 180); // 3 hours

			$language = Cache::get("irc-language-$nick", array_rand($greetings));
			$welcome  = $greetings[$language];

			$channel = $message->channel() ?: '#laravel';
			return Message::privmsg($channel, $welcome.' '.$message->sender->nick."! ($language)");
		}
	}
};
Message::listen('join', $greeter);

// Rommie will watch out for people saying a language, and will greet them with that
// language next time she sees them
$observer = function($message) use ($langreg, $languages, $lowerlang)
{
	$nick = strtolower($message->sender->nick);
	$body = end($message->params);

	$langkey = "irc-language-$nick";

	if (preg_match($langreg, $body, $m))
	{
		$language = strtolower($m[0]);
		$position = array_search($language, $lowerlang);
		$language = $languages[$position];

		$curlang = Cache::get($langkey);

		if ($curlang !== $language)
		{
			Cache::put($langkey, $language, 3.15569e7);
			return Message::notice($message->sender->nick, "I will now greet you in $language");
		}
	}
	else if ($body == 'Rommie, forget me')
	{
		Cache::forget($langkey);
		return Message::notice($message->sender->nick, "I will stop greeting you now");
	}
};
Message::listen('privmsg', $observer);
