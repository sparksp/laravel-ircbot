<?php

use IRC\Message;

$flipper = function($message)
  {
    echo "flipper\n";
    
    $nick = $message->sender->nick;
    $body = $message->body;
    $channel = $message->channel() ?: '#laravel';

    $table = '┻━┻';
    if (str_contains($body, $table))
    {
      return Message::privmsg($channel, '┬─┬ ノ( ゜-゜ノ)');
    }
  };
Message::listen('privmsg', $flipper);
