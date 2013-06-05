<?php

/**
 * General responses to return
 *
 * @package  IRC-General
 * @category  Bundle
 * @author  Brian Retterer
 * @copyright  2013 Brian Retterer <bretterer@gmail.com>
 * @license   MIT License <http://www.opensource.org/licenses/mit>
 */

use IRC\Message;


function triggerOn($message, $triggersArray) {

    $nick = strtolower($message->sender->nick);
    $body = end($message->params);
    $channel = $message->channel() ?: $message->sender->nick;

    foreach($triggersArray as $trigger => $returnMessage) {

        $match = '/^(.*)[:\s]*';
        if (preg_match('/^\b./', $trigger)) {
            $match .= '\b';
        }
        $match .= $trigger.'\b/';

        if (preg_match($match, $body, $m)) {
            $tell = $m[1];

            if(empty($tell)) {
                return Message::privmsg($channel, $returnMessage);
            } else {
                return Message::privmsg($channel, str_finish($tell, ": ") . $returnMessage);
            }
        }
    }
}

Message::listen('privmsg', function($message) { 
    $triggersArray = Config::get('irc-general::triggers');
    return triggerOn($message, $triggersArray);
});
