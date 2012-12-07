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
);

foreach ($greet as $nick => $lang)
{
	Cache::put("irc-language-$nick", $lang, 3.15569e7);
}

// List of greetings from around the world
// @link http://www.rogerdarlington.me.uk/Goodmorning.html
$greetings = array(
	"Afrikaans" => "Goeie môre",
	"Albanian" => "Mirëmëngjes",
	"Amharic" => "Endermen aderkh",
	"Arabic" => "Sabah-il-kheir",
	"Aramaic" => "Yasetel liesbukh",
	"Armenian" => "Bari luys",
	"Assyrian" => "Kedamtookh brikhta",
	"Azerbaijani" => "Sabahiniz Xeyr",
	"Bamougoum" => "Oli yah",
	"Bangla" => "Shuvo sokal",
	"Basque" => "Egun on",
	"Belarussian" => "Dobray ranitsy",
	"Bemba" => "Mwashibukeni",
	"Bengali" => "Shu-probhaat",
	"Bisaya" => "Maayong adlaw",
	"Bosnian" => "Dobro jutro",
	"Bulgarian" => "Dobro utro",
	"Cantonese" => "Zou san",
	"Catalan" => "Bon dia",
	"Cebuano" => "Maayong buntag!",
	"Chichewa" => "Mwadzuka bwanji",
	"Chinese" => "Zao shang hao", "Mandarin" => "Zao shang hao",
	"Cornish" => "Myttin da",
	"Corse" => "Bun ghjiornu",
	"Creole" => "Bonjou",
	"Croatian" => "Dobro jutro",
	"Czech" => "Dobré ráno",
	"Danish" => "God morgen",
	"Dari" => "Sob Bakhaer",
	"Divehi" => "Baajjaveri hedhuneh",
	"Dutch" => "Goedemorgen",
	"English" => "Good morning",
	"Esperanto" => "Bonan matenon",
	"Estonian" => "Tere hommikust",
	"Etsakor" => "Naigbia",
	"Fanti" => "Me ma wo akye",
	"Fijian" => "Sa Yadra",
	"Filipino" => "Magandang umaga po",
	"Finnish" => "Hyvää huomenta",
	"Flemish" => "Goeie morgen",
	"French" => "Bonjour",
	"Frisian" => "Goeie moarn",
	"Galician" => "Bos dias",
	"Georgian" => "Dila mshvidobisa",
	"German" => "Guten Morgen",
	"Greek" => "Kali mera",
	"Greenlandic" => "Iterluarit",
	"Gujarati" => "Subh Prabhat",
	"Hakka" => "On zoh",
	"Hausa" => "Inaa kwana",
	"Hawaiian" => "Aloha kakahiaka",
	"Hebrew" => "Boker tov",
	"Hiligaynon" => "Maayong aga",
	"Hindi" => "Shubh prabhat",
	"Hungarian" => "Jo reggelt",
	"Icelandic" => "Gódan daginn",
	"Ilocano" => "Naimbag nga Aldaw",
	"Indonesian" => "Selamat pagi",
	"Irish" => "Dia duit ar maidin",
	"Italian" => "Buon giorno",
	"Japanese" => "Ohayo gozaimasu",
	"Kannada" => "Shubhodaya",
	"Kapampangan" => "Mayap a abak",
	"Kazakh" => "Kairly Tan",
	"Khmer" => "Arrun Suo Sdey",
	"Kimeru" => "Muga rukiiri",
	"Kinyarwanda" => "Muraho",
	"Konkani" => "Dev Tuka Boro Dis Divum",
	"Korean" => "Annyunghaseyo",
	"Kurdish Badini" => "Spede bash",
	"Kurdish Sorani" => "Beyani bash",
	"Lao" => "Sabaidee",
	"Latvian" => "Labrit",
	"Lithuanian" => "Labas reytas",
	"Lozi" => "U zuhile",
	"Luganda" => "Wasuze otyano",
	"Luo" => "Oyawore",
	"Luxembourg" => "Gudde moien",
	"Macedonian" => "Dobro utro",
	"Malayalam" => "Suprabhatham",
	"Malay" => "Selamat pagi",
	"Maltese" => "Għodwa it-tajba",
	"Manx" => "Moghrey mie",
	"Maori" => "Ata marie",
	"Mapudungun" => "Mari mari",
	"Marathi" => "Suprabhat",
	"Mongolian" => "Öglouny mend",
	"Navajo" => "Yá'át'ééh abíní",
	"Ndebele" => "Livukenjani",
	"Nepali" => "Subha prabhat",
	"Northern Sotho" => "Thobela",
	"Norwegian" => "God morgen",
	"Owambo" => "Wa lalapo",
	"Pashto" => "Sahar de pa Khair",
	"Persian" => "Subbakhair",
	"Polish" => "Dzien dobry",
	"Polynesian" => "Ia ora na",
	"Portuguese" => "Bom dia",
	"Punjabi" => "Sat Shri Akal",
	"Rapa Nui" => "Iorana",
	"Romanian" => "Buna dimineata",
	"Russian" => "Dobraye ootra",
	"Samoan" => "Talofa lava",
	"Sanskrit" => "Suprabhataha",
	"Sardinian" => "Bona dia",
	"Serbian" => "Dobro jutro",
	"Shona" => "Mangwanani",
	"Sinhalese" => "Suba Udesanak Wewa",
	"Slovak" => "Dobré ráno",
	"Slovenian" => "Dobro jutro",
	"Somalian" => "Subax wanaagsan",
	"Southern Sotho" => "Dumela",
	"Spanish" => "Buenos dias",
	"Swahili" => "Habari za asubuhi",
	"Swazi" => "Sawubona",
	"Swedish" => "God morgon",
	"Tagalog" => "Magandang umaga",
	"Taiwanese" => "Gau cha",
	"Tamil" => "Kaalai Vannakkam",
	"Telugu" => "Subhodayamu",
	"Tetum" => "Dader diak",
	"Thai" => "Aroon-Sawass",
	"Tibetan" => "Nyado delek",
	"Tonga" => "Mwabuka buti",
	"Tswana" => "Dumela",
	"Turkish" => "Günaydin",
	"Turkmen" => "Ertiringiz haiyirli bolsun",
	"Ukrainian" => "Dobri ranok",
	"Urdu" => "Subha Ba-khair",
	"Uzbek" => "Khairli kun",
	"Vietnamese" => "Xin chao",
	"Welsh" => "Bore da",
	"Xhosa" => "Bhota",
	"Xitsonga" => "Avuxeni",
	"Yoruba" => "E karo",
	"Zulu" => "Sawubona",
	"!Kung san" => "Tuwa",
	"Australian" => "G'day",
	"Klingon" => "nuqneH", "tlhIngan Hol" => "nuqneH",
	"Vulcan" => "Dif-tor heh smusma",
	"Romulan" => "Brhon mnekha",
	"Ferengi" => "Welcome to our home. Please place your thumbprint on the legal waivers and deposit your admission fee in the slot by the door. Remember, my house is my house,",
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

	if (starts_with($body, "Rommie, I speak "))
	{
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
	}
	else if (starts_with($body, "Rommie, forget me"))
	{
		Cache::forget($langkey);
		return Message::notice($message->sender->nick, "I will stop greeting you now");
	}
};
Message::listen('privmsg', $observer);
