<?php

/**
 * Respond to a user when they sign in as qwebirc or others
 *
 * @package  IRC-qweb
 * @category  Bundle
 * @author  Brian Retterer
 * @copyright  2013 Brian Retterer <bretterer@gmail.com>
 * @license   MIT License <http://www.opensource.org/licenses/mit>
 */

use IRC\Message;


$qwebirc = function($message)
{
    $nick = $message->sender->nick;
    $nicklower = strtolower($nick);

    $channel = $message->channel() ?: '#laravel-bot-test';
    if(preg_match(Config::get('irc-qweb::config.regex'),$nicklower)) {

        $message = Config::get('irc-qweb::config.response');
        $search = array("{{nick}}", "{{channel}}");
        $replace   = array($nick, $channel);

        $message = str_replace($search,$replace,$message);

        return Message::privmsg($channel, $message);
    }
    
    // if (Cache::has($langkey))
    // {
    //     // Use the cache to prevent re-greeting more than once per hour,
    //     // useful if someone has connection issues or if they get host-cloaked
    //     // by the server
    //     $greetkey = "irc-greeted-$nick";
    //     if (! Cache::has($greetkey))
    //     {
    //         Cache::put($greetkey, time(), 180); // 3 hours

    //         $language = Cache::get("irc-language-$nick", array_rand($greetings));
    //         $welcome  = $greetings[$language];

    //         $channel = $message->channel() ?: '#laravel';
    //         return Message::privmsg($channel, $welcome.' '.$message->sender->nick."! ($language)");
    //     }
    // }
};
Message::listen('join', $qwebirc);