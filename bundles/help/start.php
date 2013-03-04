<?php

/**
 * Tells people how to get help
 *
 * @package  help
 * @category  bundle
 * @author  Shawn Mccool
 * @license   MIT License <http://www.opensource.org/licenses/mit>
 */

IRC\Message::listen('privmsg', function($message)
{
    if (starts_with($body, "!help")) {
        $help_text = "Before asking for help, use paste.laravel.com to provide 1. ALL relevant code (controllers, routes, models, views, anything) 2. expected and actual behaviour 3. any error messages you're getting. Thank you!";

        $channel = $message->channel() ?: '#laravel';

        return Message::privmsg($channel, $help_text);
    }
});