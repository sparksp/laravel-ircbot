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

    $channel = $message->channel() ?: '#laravel';

    if (preg_match(Config::get('irc-qweb::config.regex'), $nicklower)) {

        $message = Config::get('irc-qweb::config.response');
        $search  = array("{{nick}}", "{{channel}}");
        $replace = array($nick, $channel);

        $message = str_replace($search, $replace, $message);

        return Message::privmsg($channel, $message);
    }

};

Message::listen('join', $qwebirc);
