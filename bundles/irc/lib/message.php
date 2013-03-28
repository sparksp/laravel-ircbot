<?php namespace IRC;

use Socket, Config, Event, Closure;

/**
 * IRC Message
 *
 * An immutable object representing an IRC message.
 *
 * @package  IRC
 * @category  Bundle
 * @author  Phill Sparks <me@phills.me.uk>
 * @copyright  2012 Phill Sparks
 * @license   MIT License <http://www.opensource.org/licenses/mit>
 */
final class Message {

	/**
	 * "<nickname> :No such nick/channel"
	 *
	 * Used to indicate the nickname parameter supplied to a
	 * command is currently unused.
	 */
	const ERR_NOSUCHNICK = 401;
	/**
	 * "<server name> :No such server"
	 *
	 * Used to indicate the server name given currently
	 * doesn't exist.
	 */
	const ERR_NOSUCHSERVER = 402;
	/**
	 * "<channel name> :No such channel"
	 *
	 * Used to indicate the given channel name is invalid.
	 */
	const ERR_NOSUCHCHANNEL = 403;
	/**
	 * "<channel name> :Cannot send to channel"
	 *
	 * Sent to a user who is either (a) not on a channel
	 * which is mode +n or (b) not a chanop (or mode +v) on
	 * a channel which has mode +m set and is trying to send
	 * a PRIVMSG message to that channel.
	 *
	 * @see IRC\Client::privmsg()
	 */
	const ERR_CANNOTSENDTOCHAN = 404;
	/**
	 * "<channel name> :You have joined too many channels"
	 *
	 * Sent to a user when they have joined the maximum
	 * number of allowed channels and they try to join
	 * another channel.
	 */
	const ERR_TOOMANYCHANNELS = 405;
	/**
	 * "<nickname> :There was no such nickname"
	 *
	 * Returned by WHOWAS to indicate there is no history
	 * information for that nickname.
	 *
	 * @see RPL_ENDOFWHOWAS
	 * @see IRC\Client::whowas()
	 */
	const ERR_WASNOSUCHNICK = 406;
	/**
	 * "<target> :Duplicate recipients. No message delivered"
	 *
	 * Returned to a client which is attempting to send a
	 * PRIVMSG/NOTICE using the user@host destination format
	 * and for a user@host which has several occurrences.
	 *
	 * @see IRC\Client::privmsg()
	 * @see IRC\Client::notice()
	 */
	const ERR_TOOMANYTARGETS = 407;
	/**
	 * ":No origin specified"
	 *
	 * PING or PONG message missing the originator parameter
	 * which is required since these commands must work
	 * without valid prefixes.
	 *
	 * @see IRC\Client::pong()
	 */
	const ERR_NOORIGIN = 409;
	/** ":No recipient given (<command>)" */
	const ERR_NORECIPIENT = 411;
	/**
	 * ":No text to send"
	 *
	 * Returned by PRIVMSG to indicate that the message wasn't
	 * delivered for some reason.
	 *
	 * @see IRC\Client::privmsg()
	 */
	const ERR_NOTEXTTOSEND = 412;
	/**
	 * "<mask> :No toplevel domain specified"
	 *
	 * Returned by PRIVMSG to indicate that the message wasn't
	 * delivered for some reason.
	 *
	 * {@link ERR_NOTOPLEVEL} and {@link ERR_WILDTOPLEVEL} are errors that
	 * are returned when an invalid use of
	 * "PRIVMSG $<server>" or "PRIVMSG #<host>" is attempted.
	 *
	 * @see IRC\Client::privmsg()
	 */
	const ERR_NOTOPLEVEL = 413;
	/**
	 * "<mask> :Wildcard in toplevel domain"
	 *
	 * Returned by PRIVMSG to indicate that the message wasn't
	 * delivered for some reason.
	 *
	 * {@link ERR_NOTOPLEVEL} and {@link ERR_WILDTOPLEVEL} are errors that
	 * are returned when an invalid use of
	 * "PRIVMSG $<server>" or "PRIVMSG #<host>" is attempted.
	 *
	 * @see IRC\Client::privmsg()
	 */
	const ERR_WILDTOPLEVEL = 414;
	/**
	 * "<command> :Unknown command"
	 *
	 * Returned to a registered client to indicate that the
	 * command sent is unknown by the server.
	 */
	const ERR_UNKNOWNCOMMAND = 421;
	/**
	 * ":MOTD File is missing"
	 *
	 * Server's MOTD file could not be opened by the server.
	 */
	const ERR_NOMOTD = 422;
	/**
	 * "<server> :No administrative info available"
	 *
	 * Returned by a server in response to an ADMIN message
	 * when there is an error in finding the appropriate
	 * information.
	 */
	const ERR_NOADMININFO = 423;
	/**
	 * ":File error doing <file op> on <file>"
	 *
	 * Generic error message used to report a failed file
	 * operation during the processing of a message.
	 */
	const ERR_FILEERROR = 424;
	/**
	 * ":No nickname given"
	 *
	 * Returned when a nickname parameter expected for a
	 * command and isn't found.
	 */
	const ERR_NONICKNAMEGIVEN = 431;
	/**
	 * "<nick> :Erroneus nickname"
	 *
	 * Returned after receiving a NICK message which contains
	 * characters which do not fall in the defined set.
	 *
	 * @see IRC\Client::nick()
	 */
	const ERR_ERRONEUSNICKNAME = 432;
	/**
	 * "<nick> :Nickname is already in use"
	 *
	 * Returned when a NICK message is processed that results
	 * in an attempt to change to a currently existing
	 * nickname.
	 *
	 * @see IRC\Client::nick()
	 */
	const ERR_NICKNAMEINUSE = 433;
	/**
	 * "<nick> :Nickname collision KILL"
	 *
	 * Returned by a server to a client when it detects a
	 * nickname collision (registered of a NICK that
	 * already exists by another server).
	 *
	 * @see IRC\Client::nick()
	 */
	const ERR_NICKCOLLISION = 436;
	/**
	 * "<nick> <channel> :They aren't on that channel"
	 *
	 * Returned by the server to indicate that the target
	 * user of the command is not on the given channel.
	 */
	const ERR_USERNOTINCHANNEL = 441;
	/**
	 * "<channel> :You're not on that channel"
	 *
	 * Returned by the server whenever a client tries to
	 * perform a channel effecting command for which the
	 * client isn't a member.
	 */
	const ERR_NOTONCHANNEL = 442;
	/**
	 * "<user> <channel> :is already on channel"
	 *
	 * Returned when a client tries to invite a user to a
	 * channel they are already on.
	 */
	const ERR_USERONCHANNEL = 443;
	/**
	 * "<user> :User not logged in"
	 *
	 * Returned by the summon after a SUMMON command for a
	 * user was unable to be performed since they were not
	 * logged in.
	 *
	 * @see IRC\Client::summon()
	 */
	const ERR_NOLOGIN = 444;
	/**
	 * ":SUMMON has been disabled"
	 *
	 * Returned as a response to the SUMMON command.  Must be
	 * returned by any server which does not implement it.
	 *
	 * @see IRC\Client::summon()
	 */
	const ERR_SUMMONDISABLED = 445;
	/**
	 * ":USERS has been disabled"
	 *
	 * Returned as a response to the USERS command.  Must be
	 * returned by any server which does not implement it.
	 *
	 * @see IRC\Client::users();
	 */
	const ERR_USERSDISABLED = 446;
	/**
	 * ":You have not registered"
	 *
	 * Returned by the server to indicate that the client
	 * must be registered before the server will allow it
	 * to be parsed in detail.
	 */
	const ERR_NOTREGISTERED = 451;
	/**
	 * "<command> :Not enough parameters"
	 *
	 * Returned by the server by numerous commands to
	 * indicate to the client that it didn't supply enough
	 * parameters.
	 */
	const ERR_NEEDMOREPARAMS = 461;
	/**
	 * ":You may not reregister"
	 *
	 * Returned by the server to any link which tries to
	 * change part of the registered details (such as
	 * password or user details from second USER message).
	 *
	 * @see IRC\Client::user()
	 */
	const ERR_ALREADYREGISTRED = 462;
	/**
	 * ":Your host isn't among the privileged"
	 *
	 * Returned to a client which attempts to register with
	 * a server which does not been setup to allow
	 * connections from the host the attempted connection
	 * is tried.
	 */
	const ERR_NOPERMFORHOST = 463;
	/**
	 * ":Password incorrect"
	 *
	 * Returned to indicate a failed attempt at registering
	 * a connection for which a password was required and
	 * was either not given or incorrect.
	 */
	const ERR_PASSWDMISMATCH = 464;
	/**
	 * ":You are banned from this server"
	 *
	 * Returned after an attempt to connect and register
	 * yourself with a server which has been setup to
	 * explicitly deny connections to you.
	 */
	const ERR_YOUREBANNEDCREEP = 465;
	/** "<channel> :Channel key already set" */
	const ERR_KEYSET = 467;
	/** "<channel> :Cannot join channel (+l)" */
	const ERR_CHANNELISFULL = 471;
	/** "<char> :is unknown mode char to me" */
	const ERR_UNKNOWNMODE = 472;
	/** "<channel> :Cannot join channel (+i)" */
	const ERR_INVITEONLYCHAN = 473;
	/** "<channel> :Cannot join channel (+b)" */
	const ERR_BANNEDFROMCHAN = 474;
	/** "<channel> :Cannot join channel (+k)" */
	const ERR_BADCHANNELKEY = 475;
	/**
	 * ":Permission Denied- You're not an IRC operator"
	 *
	 * Any command requiring operator privileges to operate
	 * must return this error to indicate the attempt was
	 * unsuccessful.
	 */
	const ERR_NOPRIVILEGES = 481;
	/**
	 * "<channel> :You're not channel operator"
	 *
	 * Any command requiring 'chanop' privileges (such as
	 * MODE messages) must return this error if the client
	 * making the attempt is not a chanop on the specified
	 * channel.
	 *
	 * @see IRC\Client::mode()
	 */
	const ERR_CHANOPRIVSNEEDED = 482;
	/**
	 * ":You cant kill a server!"
	 *
	 * Any attempts to use the KILL command on a server
	 * are to be refused and this error returned directly
	 * to the client.
	 *
	 * @see IRC\Client::kill()
	 */
	const ERR_CANTKILLSERVER = 483;
	/**
	 * ":No O-lines for your host"
	 *
	 * If a client sends an OPER message and the server has
	 * not been configured to allow connections from the
	 * client's host as an operator, this error must be
	 * returned.
	 *
	 * @see IRC\Client::oper()
	 */
	const ERR_NOOPERHOST = 491;
	/**
	 * ":Unknown MODE flag"
	 *
	 * Returned by the server to indicate that a MODE
	 * message was sent with a nickname parameter and that
	 * the a mode flag sent was not recognized.
	 *
	 * @see IRC\Client::mode()
	 */
	const ERR_UMODEUNKNOWNFLAG = 501;
	/**
	 * ":Cant change mode for other users"
	 *
	 * Error sent to any user trying to view or change the
	 * user mode for a user other than themselves.
	 */
	const ERR_USERSDONTMATCH = 502;
	/** Dummy reply number. Not used. */
	const RPL_NONE = 300;
	/**
	 * ":[<reply>{<space><reply>}]"
	 *
	 * Reply format used by USERHOST to list replies to
	 * the query list.  The reply string is composed as
	 * follows:
	 *
	 * <code>
	 * <reply> ::= <nick>['*'] '=' <'+'|'-'><hostname>
	 * </code>
	 *
	 * The '*' indicates whether the client has registered
	 * as an Operator.  The '-' or '+' characters represent
	 * whether the client has set an AWAY message or not
	 * respectively.
	 *
	 * @see IRC\Client::userhost()
	 */
	const RPL_USERHOST = 302;
	/**
	 * ":[<nick> {<space><nick>}]"
	 *
	 * Reply format used by ISON to list replies to the
	 * query list.
	 *
	 * @see IRC\Client::ison()
	 */
	const RPL_ISON = 303;
	/**
	 * "<nick> :<away message>"
	 *
	 * Used with the AWAY command (if allowed).  {@link RPL_AWAY}
	 * is sent to any client sending a PRIVMSG to a client
	 * which is away.  {@link RPL_AWAY} is only sent by the server
	 * to which the client is connected.
	 *
	 * @see IRC\Client::away(), IRC\Client::privmsg()
	 */
	const RPL_AWAY = 301;
	/**
	 * ":You are no longer marked as being away"
	 *
	 * Used with the AWAY command (if allowed). {@link RPL_UNAWAY}
	 * is sent when the client removes and sets an AWAY
	 * message.
	 *
	 * @see IRC\Client::away()
	 */
	const RPL_UNAWAY = 305;
	/**
	 * ":You have been marked as being away"
	 *
	 *
	 * Used with the AWAY command (if allowed). {@link RPL_NOWAWAY}
	 * is sent when the client removes and sets an AWAY
	 * message.
	 *
	 * @see IRC\Client::away()
	 */
	const RPL_NOWAWAY = 306;
	/**
	 * "<nick> <user> <host> * :<real name>"
	 *
	 * Generated in response to a WHOIS message. The '*' in
	 * {@link RPL_WHOISUSER} is there as the literal character and
	 * not as a wild card.
	 *
	 * @see IRC\Client::whois()
	 */
	const RPL_WHOISUSER = 311;
	/**
	 * "<nick> <server> :<server info>"
	 *
	 * Generated in response to a WHOIS or WHOWAS message.
	 *
	 * @see IRC\Client::whois(), IRC\Client::whowas()
	 */
	const RPL_WHOISSERVER = 312;
	/**
	 * "<nick> :is an IRC operator"
	 *
	 * Generated in response to a WHOIS message.
	 *
	 * @see IRC\Client::whois()
	 */
	const RPL_WHOISOPERATOR = 313;
	/**
	 * "<nick> <integer> :seconds idle"
	 *
	 * Generated in response to a WHOIS message.
	 *
	 * @see IRC\Client::whois()
	 */
	const RPL_WHOISIDLE = 317;
	/**
	 * "<nick> :End of /WHOIS list"
	 *
	 * Generated in response to a WHOIS message. The
	 * {@link RPL_ENDOFWHOIS} reply is used to mark the end of
	 * processing a WHOIS message.
	 *
	 * @see IRC\Client::whois()
	 */
	const RPL_ENDOFWHOIS = 318;
	/**
	 * "<nick> :{[@|+]<channel><space>}"
	 *
	 * Generated in response to a WHOIS message. Only
	 * {@link RPL_WHOISCHANNELS} may appear more than once (for
	 * long lists of channel names). The '@' and '+'
	 * characters next to the channel name indicate whether
	 * a client is a channel operator or has been granted
	 * permission to speak on a moderated channel.
	 *
	 * @see IRC\Client::whois()
	 */
	const RPL_WHOISCHANNELS = 319;
	/**
	 * "<nick> <user> <host> * :<real name>"
	 *
	 * When replying to a WHOWAS message, a server must use
	 * the replies {@link RPL_WHOWASUSER}, {@link RPL_WHOISSERVER} or
	 * {@link ERR_WASNOSUCHNICK} for each nickname in the presented
	 * list.  At the end of all reply batches, there must
	 * be {@link RPL_ENDOFWHOWAS} (even if there was only one reply
	 * and it was an error).
	 *
	 * @see IRC\Client::whowas()
	 */
	const RPL_WHOWASUSER = 314;
	/**
	 * "<nick> :End of WHOWAS"
	 *
	 * When replying to a WHOWAS message, a server must use
	 * the replies {@link RPL_WHOWASUSER}, {@link RPL_WHOISSERVER} or
	 * {@link ERR_WASNOSUCHNICK} for each nickname in the presented
	 * list.  At the end of all reply batches, there must
	 * be {@link RPL_ENDOFWHOWAS} (even if there was only one reply
	 * and it was an error).
	 *
	 * @see IRC\Client::whowas()
	 */
	const RPL_ENDOFWHOWAS = 369;
	/**
	 * "Channel :Users  Name"
	 *
	 * Replies {@link RPL_LISTSTART}, {@link RPL_LIST}, {@link RPL_LISTEND} mark
	 * the start, actual replies with data and end of the
	 * server's response to a LIST command.  If there are
	 * no channels available to return, only the start
	 * and end reply must be sent.
	 *
	 * @see IRC\Client::list()
	 */
	const RPL_LISTSTART = 321;
	/**
	 * "<channel> <# visible> :<topic>"
	 *
	 * Replies {@link RPL_LISTSTART}, {@link RPL_LIST}, {@link RPL_LISTEND} mark
	 * the start, actual replies with data and end of the
	 * server's response to a LIST command.  If there are
	 * no channels available to return, only the start
	 * and end reply must be sent.
	 *
	 * @see IRC\Client::list()
	 */
	const RPL_LIST = 322;
	/**
	 * ":End of /LIST"
	 *
	 * Replies {@link RPL_LISTSTART}, {@link RPL_LIST}, {@link RPL_LISTEND} mark
	 * the start, actual replies with data and end of the
	 * server's response to a LIST command.  If there are
	 * no channels available to return, only the start
	 * and end reply must be sent.
	 * @see IRC\Client::list()
	 */
	const RPL_LISTEND = 323;
	/** "<channel> <mode> <mode params>" */
	const RPL_CHANNELMODEIS = 324;
	/**
	 * "<channel> :No topic is set"
	 *
	 * When sending a TOPIC message to determine the
	 * channel topic, one of two replies is sent.  If
	 * the topic is set, {@link RPL_TOPIC} is sent back else
	 * {@link RPL_NOTOPIC}.
	 *
	 * @see IRC\Client::topic()
	 */
	const RPL_NOTOPIC = 331;
	/**
	 * "<channel> :<topic>"
	 *
	 * When sending a TOPIC message to determine the
	 * channel topic, one of two replies is sent.  If
	 * the topic is set, {@link RPL_TOPIC} is sent back else
	 * {@link RPL_NOTOPIC}.
	 *
	 * @see IRC\Client::topic()
	 */
	const RPL_TOPIC = 332;
	/**
	 * "<channel> <nick>"
	 *
	 * Returned by the server to indicate that the
	 * attempted INVITE message was successful and is
	 * being passed onto the end client.
	 *
	 * @see IRC\Client::invite()
	 */
	const RPL_INVITING = 341;
	/**
	 * "<user> :Summoning user to IRC"
	 *
	 * Returned by a server answering a SUMMON message to
	 * indicate that it is summoning that user.
	 *
	 * @see IRC\Client::summon()
	 */
	const RPL_SUMMONING = 342;
	/**
	 * "<version>.<debuglevel> <server> :<comments>"
	 *
	 * Reply by the server showing its version details.
	 * The <version> is the version of the software being
	 * used (including any patchlevel revisions) and the
	 * <debuglevel> is used to indicate if the server is
	 * running in "debug mode".
	 *
	 * The "comments" field may contain any comments about
	 * the version or further version details.
	 */
	const RPL_VERSION = 351;
	/**
	 * "<channel> <user> <host> <server> <nick> 
	 *  <H|G>[*][@|+] :<hopcount> <real name>"
	 *
	 * The {@link RPL_WHOREPLY} and {@link RPL_ENDOFWHO} pair are used
	 * to answer a WHO message.  The {@link RPL_WHOREPLY} is only
	 * sent if there is an appropriate match to the WHO
	 * query.  If there is a list of parameters supplied
	 * with a WHO message, a {@link RPL_ENDOFWHO} must be sent
	 * after processing each list item with <name> being
	 * the item.
	 *
	 * @see IRC\Client::who()
	 */
	const RPL_WHOREPLY = 352;
	/**
	 * "<name> :End of /WHO list"
	 * 
	 * The {@link RPL_WHOREPLY} and {@link RPL_ENDOFWHO} pair are used
	 * to answer a WHO message.  The {@link RPL_WHOREPLY} is only
	 * sent if there is an appropriate match to the WHO
	 * query.  If there is a list of parameters supplied
	 * with a WHO message, a {@link RPL_ENDOFWHO} must be sent
	 * after processing each list item with <name> being
	 * the item.
	 *
	 * @see IRC\Client::who()
	 */
	const RPL_ENDOFWHO = 315;
	/**
	 * "<channel> :[[@|+]<nick> [[@|+]<nick> [...]]]"
	 *
	 * To reply to a NAMES message, a reply pair consisting
	 * of {@link RPL_NAMREPLY} and {@link RPL_ENDOFNAMES} is sent by the
	 * server back to the client.  If there is no channel
	 * found as in the query, then only {@link RPL_ENDOFNAMES} is
	 * returned.  The exception to this is when a NAMES
	 * message is sent with no parameters and all visible
	 * channels and contents are sent back in a series of
	 * {@link RPL_NAMEREPLY} messages with a {@link RPL_ENDOFNAMES} to mark
	 * the end.
	 *
	 * @see IRC\Client::names()
	 */
	const RPL_NAMREPLY = 353;
	/**
	 * "<channel> :End of /NAMES list"
	 *
	 * To reply to a NAMES message, a reply pair consisting
	 * of {@link RPL_NAMREPLY} and {@link RPL_ENDOFNAMES} is sent by the
	 * server back to the client.  If there is no channel
	 * found as in the query, then only {@link RPL_ENDOFNAMES} is
	 * returned.  The exception to this is when a NAMES
	 * message is sent with no parameters and all visible
	 * channels and contents are sent back in a series of
	 * {@link RPL_NAMEREPLY} messages with a {@link RPL_ENDOFNAMES} to mark
	 * the end.
	 *
	 * @see IRC\Client::names()
	 */
	const RPL_ENDOFNAMES = 366;
	/**
	 * "<mask> <server> :<hopcount> <server info>"
	 *
	 * In replying to the LINKS message, a server must send
	 * replies back using the {@link RPL_LINKS} numeric and mark the
	 * end of the list using an {@link RPL_ENDOFLINKS} reply.
	 *
	 * @see IRC\Client::links()
	 */
	const RPL_LINKS = 364;
	/**
	 * "<mask> :End of /LINKS list"
	 *
	 * In replying to the LINKS message, a server must send
	 * replies back using the {@link RPL_LINKS} numeric and mark the
	 * end of the list using an {@link RPL_ENDOFLINKS} reply.
	 *
	 * @see IRC\Client::links()
	 */
	const RPL_ENDOFLINKS = 365;
	/**
	 * "<channel> <banid>"
	 *
	 * When listing the active 'bans' for a given channel,
	 * a server is required to send the list back using the
	 * {@link RPL_BANLIST} and {@link RPL_ENDOFBANLIST} messages.  A separate
	 * {@link RPL_BANLIST} is sent for each active banid.  After the
	 * banids have been listed (or if none present) a
	 * {@link RPL_ENDOFBANLIST} must be sent.
	 */
	const RPL_BANLIST = 367;
	/**
	 * "<channel> :End of channel ban list"
	 *
	 * When listing the active 'bans' for a given channel,
	 * a server is required to send the list back using the
	 * {@link RPL_BANLIST} and {@link RPL_ENDOFBANLIST} messages.  A separate
	 * {@link RPL_BANLIST} is sent for each active banid.  After the
	 * banids have been listed (or if none present) a
	 * {@link RPL_ENDOFBANLIST} must be sent.
	 */
	const RPL_ENDOFBANLIST = 368;
	/**
	 * ":<string>"
	 *
	 * A server responding to an INFO message is required to
	 * send all its 'info' in a series of {@link RPL_INFO} messages
	 * with a {@link RPL_ENDOFINFO} reply to indicate the end of the
	 * replies.
	 *
	 * @see IRC\Client::info()
	 */
	const RPL_INFO = 371;
	/**
	 * ":End of /INFO list"
	 *
	 * A server responding to an INFO message is required to
	 * send all its 'info' in a series of {@link RPL_INFO} messages
	 * with a {@link RPL_ENDOFINFO} reply to indicate the end of the
	 * replies.
	 *
	 * @see IRC\Client::info()
	 */
	const RPL_ENDOFINFO = 374;
	/**
	 * ":- <server> Message of the day - "
	 *
	 * When responding to the MOTD message and the MOTD file
	 * is found, the file is displayed line by line, with
	 * each line no longer than 80 characters, using
	 * {@link RPL_MOTD} format replies.  These should be surrounded
	 * by a {@link RPL_MOTDSTART} (before the {@link RPL_MOTD}s) and an
	 * {@link RPL_ENDOFMOTD} (after).
	 *
	 * @see IRC\Client::motd()
	 */
	const RPL_MOTDSTART = 375;
	/**
	 * ":- <text>"
	 *
	 * When responding to the MOTD message and the MOTD file
	 * is found, the file is displayed line by line, with
	 * each line no longer than 80 characters, using
	 * {@link RPL_MOTD} format replies.  These should be surrounded
	 * by a {@link RPL_MOTDSTART} (before the {@link RPL_MOTD}s) and an
	 * {@link RPL_ENDOFMOTD} (after).
	 *
	 * @see IRC\Client::motd()
	 */
	const RPL_MOTD = 372;
	/**
	 * ":End of /MOTD command"
	 *
	 * When responding to the MOTD message and the MOTD file
	 * is found, the file is displayed line by line, with
	 * each line no longer than 80 characters, using
	 * {@link RPL_MOTD} format replies.  These should be surrounded
	 * by a {@link RPL_MOTDSTART} (before the {@link RPL_MOTD}s) and an
	 * {@link RPL_ENDOFMOTD} (after).
	 *
	 * @see IRC\Client::motd()
	 */
	const RPL_ENDOFMOTD = 376;
	/**
	 * ":You are now an IRC operator"
	 *
	 * {@link RPL_YOUREOPER} is sent back to a client which has 
	 * just successfully issued an OPER message and gained
	 * operator status.
	 *
	 * @see IRC\Client::oper()
	 */
	const RPL_YOUREOPER = 381;
	/**
	 * "<config file> :Rehashing"
	 *
	 * If the REHASH option is used and an operator sends
	 * a REHASH message, an {@link RPL_REHASHING} is sent back to
	 * the operator.
	 *
	 * @see IRC\Client::rehash()
	 */
	const RPL_REHASHING = 382;
	/**
	 * "<server> :<string showing server's local time>"
	 *
	 * When replying to the TIME message, a server must send
	 * the reply using the {@link RPL_TIME} format above.  The string
	 * showing the time need only contain the correct day and
	 * time there.  There is no further requirement for the
	 * time string.
	 *
	 * @see IRC\Client::time()
	 */
	const RPL_TIME = 391;
	/**
	 * ":UserID   Terminal  Host"
	 *
	 * If the USERS message is handled by a server, the
	 * replies {@link RPL_USERSTART}, {@link RPL_USERS}, {@link RPL_ENDOFUSERS} and
	 * {@link RPL_NOUSERS} are used.  {@link RPL_USERSSTART} must be sent
	 * first, following by either a sequence of {@link RPL_USERS}
	 * or a single {@link RPL_NOUSER}.  Following this is
	 * {@link RPL_ENDOFUSERS}.
	 *
	 * @see IRC\Client::users()
	 */
	const RPL_USERSSTART = 392;
	/**
	 * ":%-8s %-9s %-8s"
	 *
	 * If the USERS message is handled by a server, the
	 * replies {@link RPL_USERSTART}, {@link RPL_USERS}, {@link RPL_ENDOFUSERS} and
	 * {@link RPL_NOUSERS} are used.  {@link RPL_USERSSTART} must be sent
	 * first, following by either a sequence of {@link RPL_USERS}
	 * or a single {@link RPL_NOUSER}.  Following this is
	 * {@link RPL_ENDOFUSERS}.
	 *
	 * @see IRC\Client::users()
	 */
	const RPL_USERS = 393;
	/**
	 * ":End of users"
	 *
	 * If the USERS message is handled by a server, the
	 * replies {@link RPL_USERSTART}, {@link RPL_USERS}, {@link RPL_ENDOFUSERS} and
	 * {@link RPL_NOUSERS} are used.  {@link RPL_USERSSTART} must be sent
	 * first, following by either a sequence of {@link RPL_USERS}
	 * or a single {@link RPL_NOUSER}.  Following this is
	 * {@link RPL_ENDOFUSERS}.
	 *
	 * @see IRC\Client::users()
	 */
	const RPL_ENDOFUSERS = 394;
	/**
	 * ":Nobody logged in"
	 *
	 * If the USERS message is handled by a server, the
	 * replies {@link RPL_USERSTART}, {@link RPL_USERS}, {@link RPL_ENDOFUSERS} and
	 * {@link RPL_NOUSERS} are used.  {@link RPL_USERSSTART} must be sent
	 * first, following by either a sequence of {@link RPL_USERS}
	 * or a single {@link RPL_NOUSER}.  Following this is
	 * {@link RPL_ENDOFUSERS}.
	 *
	 * @see IRC\Client::users()
	 */
	const RPL_NOUSERS = 395;
	/**
	 * "Link <version & debug level> <destination> 
	 *  <next server>"
	 *
	 * The RPL_TRACE* are all returned by the server in
	 * response to the TRACE message.  How many are
	 * returned is dependent on the the TRACE message and
	 * whether it was sent by an operator or not.  There
	 * is no predefined order for which occurs first.
	 * Replies {@link RPL_TRACEUNKNOWN}, {@link RPL_TRACECONNECTING} and
	 * {@link RPL_TRACEHANDSHAKE} are all used for connections
	 * which have not been fully established and are either
	 * unknown, still attempting to connect or in the
	 * process of completing the 'server handshake'.
	 * {@link RPL_TRACELINK} is sent by any server which handles
	 * a TRACE message and has to pass it on to another
	 * server.  The list of {@link RPL_TRACELINK}s sent in
	 * response to a TRACE command traversing the IRC
	 * network should reflect the actual connectivity of
	 * the servers themselves along that path.
	 * {@link RPL_TRACENEWTYPE} is to be used for any connection
	 * which does not fit in the other categories but is
	 * being displayed anyway.
	 *
	 * @see IRC\Client::trace()
	 */
	const RPL_TRACELINK = 200;
	/**
	 * "Try. <class> <server>"
	 *
	 * The RPL_TRACE* are all returned by the server in
	 * response to the TRACE message.  How many are
	 * returned is dependent on the the TRACE message and
	 * whether it was sent by an operator or not.  There
	 * is no predefined order for which occurs first.
	 * Replies {@link RPL_TRACEUNKNOWN}, {@link RPL_TRACECONNECTING} and
	 * {@link RPL_TRACEHANDSHAKE} are all used for connections
	 * which have not been fully established and are either
	 * unknown, still attempting to connect or in the
	 * process of completing the 'server handshake'.
	 * {@link RPL_TRACELINK} is sent by any server which handles
	 * a TRACE message and has to pass it on to another
	 * server.  The list of {@link RPL_TRACELINK}s sent in
	 * response to a TRACE command traversing the IRC
	 * network should reflect the actual connectivity of
	 * the servers themselves along that path.
	 * {@link RPL_TRACENEWTYPE} is to be used for any connection
	 * which does not fit in the other categories but is
	 * being displayed anyway.
	 *
	 * @see IRC\Client::trace()
	 */
	const RPL_TRACECONNECTING = 201;
	/**
	 * "H.S. <class> <server>"
	 *
	 * The RPL_TRACE* are all returned by the server in
	 * response to the TRACE message.  How many are
	 * returned is dependent on the the TRACE message and
	 * whether it was sent by an operator or not.  There
	 * is no predefined order for which occurs first.
	 * Replies {@link RPL_TRACEUNKNOWN}, {@link RPL_TRACECONNECTING} and
	 * {@link RPL_TRACEHANDSHAKE} are all used for connections
	 * which have not been fully established and are either
	 * unknown, still attempting to connect or in the
	 * process of completing the 'server handshake'.
	 * {@link RPL_TRACELINK} is sent by any server which handles
	 * a TRACE message and has to pass it on to another
	 * server.  The list of {@link RPL_TRACELINK}s sent in
	 * response to a TRACE command traversing the IRC
	 * network should reflect the actual connectivity of
	 * the servers themselves along that path.
	 * {@link RPL_TRACENEWTYPE} is to be used for any connection
	 * which does not fit in the other categories but is
	 * being displayed anyway.
	 *
	 * @see IRC\Client::trace()
	 */
	const RPL_TRACEHANDSHAKE = 202;
	/**
	 * "???? <class> [<client IP address in dot form>]"
	 *
	 * The RPL_TRACE* are all returned by the server in
	 * response to the TRACE message.  How many are
	 * returned is dependent on the the TRACE message and
	 * whether it was sent by an operator or not.  There
	 * is no predefined order for which occurs first.
	 * Replies {@link RPL_TRACEUNKNOWN}, {@link RPL_TRACECONNECTING} and
	 * {@link RPL_TRACEHANDSHAKE} are all used for connections
	 * which have not been fully established and are either
	 * unknown, still attempting to connect or in the
	 * process of completing the 'server handshake'.
	 * {@link RPL_TRACELINK} is sent by any server which handles
	 * a TRACE message and has to pass it on to another
	 * server.  The list of {@link RPL_TRACELINK}s sent in
	 * response to a TRACE command traversing the IRC
	 * network should reflect the actual connectivity of
	 * the servers themselves along that path.
	 * {@link RPL_TRACENEWTYPE} is to be used for any connection
	 * which does not fit in the other categories but is
	 * being displayed anyway.
	 *
	 * @see IRC\Client::trace()
	 */
	const RPL_TRACEUNKNOWN = 203;
	/**
	 * "Oper <class> <nick>"
	 *
	 * The RPL_TRACE* are all returned by the server in
	 * response to the TRACE message.  How many are
	 * returned is dependent on the the TRACE message and
	 * whether it was sent by an operator or not.  There
	 * is no predefined order for which occurs first.
	 * Replies {@link RPL_TRACEUNKNOWN}, {@link RPL_TRACECONNECTING} and
	 * {@link RPL_TRACEHANDSHAKE} are all used for connections
	 * which have not been fully established and are either
	 * unknown, still attempting to connect or in the
	 * process of completing the 'server handshake'.
	 * {@link RPL_TRACELINK} is sent by any server which handles
	 * a TRACE message and has to pass it on to another
	 * server.  The list of {@link RPL_TRACELINK}s sent in
	 * response to a TRACE command traversing the IRC
	 * network should reflect the actual connectivity of
	 * the servers themselves along that path.
	 * {@link RPL_TRACENEWTYPE} is to be used for any connection
	 * which does not fit in the other categories but is
	 * being displayed anyway.
	 *
	 * @see IRC\Client::trace()
	 */
	const RPL_TRACEOPERATOR = 204;
	/**
	 * "User <class> <nick>"
	 *
	 * The RPL_TRACE* are all returned by the server in
	 * response to the TRACE message.  How many are
	 * returned is dependent on the the TRACE message and
	 * whether it was sent by an operator or not.  There
	 * is no predefined order for which occurs first.
	 * Replies {@link RPL_TRACEUNKNOWN}, {@link RPL_TRACECONNECTING} and
	 * {@link RPL_TRACEHANDSHAKE} are all used for connections
	 * which have not been fully established and are either
	 * unknown, still attempting to connect or in the
	 * process of completing the 'server handshake'.
	 * {@link RPL_TRACELINK} is sent by any server which handles
	 * a TRACE message and has to pass it on to another
	 * server.  The list of {@link RPL_TRACELINK}s sent in
	 * response to a TRACE command traversing the IRC
	 * network should reflect the actual connectivity of
	 * the servers themselves along that path.
	 * {@link RPL_TRACENEWTYPE} is to be used for any connection
	 * which does not fit in the other categories but is
	 * being displayed anyway.
	 *
	 * @see IRC\Client::trace()
	 */
	const RPL_TRACEUSER = 205;
	/**
	 * "Serv <class> <int>S <int>C <server> 
	 *  <nick!user|*!*>@<host|server>"
	 *
	 * The RPL_TRACE* are all returned by the server in
	 * response to the TRACE message.  How many are
	 * returned is dependent on the the TRACE message and
	 * whether it was sent by an operator or not.  There
	 * is no predefined order for which occurs first.
	 * Replies {@link RPL_TRACEUNKNOWN}, {@link RPL_TRACECONNECTING} and
	 * {@link RPL_TRACEHANDSHAKE} are all used for connections
	 * which have not been fully established and are either
	 * unknown, still attempting to connect or in the
	 * process of completing the 'server handshake'.
	 * {@link RPL_TRACELINK} is sent by any server which handles
	 * a TRACE message and has to pass it on to another
	 * server.  The list of {@link RPL_TRACELINK}s sent in
	 * response to a TRACE command traversing the IRC
	 * network should reflect the actual connectivity of
	 * the servers themselves along that path.
	 * {@link RPL_TRACENEWTYPE} is to be used for any connection
	 * which does not fit in the other categories but is
	 * being displayed anyway.
	 *
	 * @see IRC\Client::trace()
	 */
	const RPL_TRACESERVER = 206;
	/**
	 * "<newtype> 0 <client name>"
	 *
	 * The RPL_TRACE* are all returned by the server in
	 * response to the TRACE message.  How many are
	 * returned is dependent on the the TRACE message and
	 * whether it was sent by an operator or not.  There
	 * is no predefined order for which occurs first.
	 * Replies {@link RPL_TRACEUNKNOWN}, {@link RPL_TRACECONNECTING} and
	 * {@link RPL_TRACEHANDSHAKE} are all used for connections
	 * which have not been fully established and are either
	 * unknown, still attempting to connect or in the
	 * process of completing the 'server handshake'.
	 * {@link RPL_TRACELINK} is sent by any server which handles
	 * a TRACE message and has to pass it on to another
	 * server.  The list of {@link RPL_TRACELINK}s sent in
	 * response to a TRACE command traversing the IRC
	 * network should reflect the actual connectivity of
	 * the servers themselves along that path.
	 * {@link RPL_TRACENEWTYPE} is to be used for any connection
	 * which does not fit in the other categories but is
	 * being displayed anyway.
	 *
	 * @see IRC\Client::trace()
	 */
	const RPL_TRACENEWTYPE = 208;
	/**
	 * "File <logfile> <debug level>"
	 *
	 * The RPL_TRACE* are all returned by the server in
	 * response to the TRACE message.  How many are
	 * returned is dependent on the the TRACE message and
	 * whether it was sent by an operator or not.  There
	 * is no predefined order for which occurs first.
	 * Replies {@link RPL_TRACEUNKNOWN}, {@link RPL_TRACECONNECTING} and
	 * {@link RPL_TRACEHANDSHAKE} are all used for connections
	 * which have not been fully established and are either
	 * unknown, still attempting to connect or in the
	 * process of completing the 'server handshake'.
	 * {@link RPL_TRACELINK} is sent by any server which handles
	 * a TRACE message and has to pass it on to another
	 * server.  The list of {@link RPL_TRACELINK}s sent in
	 * response to a TRACE command traversing the IRC
	 * network should reflect the actual connectivity of
	 * the servers themselves along that path.
	 * {@link RPL_TRACENEWTYPE} is to be used for any connection
	 * which does not fit in the other categories but is
	 * being displayed anyway.
	 *
	 * @see IRC\Client::trace()
	 */
	const RPL_TRACELOG = 261;
	/**
	 * "<linkname> <sendq> <sent messages> 
	 *  <sent bytes> <received messages> 
	 *  <received bytes> <time open>"
	 */
	const RPL_STATSLINKINFO = 211;
	/** "<command> <count>" */
	const RPL_STATSCOMMANDS = 212;
	/** "C <host> * <name> <port> <class>" */
	const RPL_STATSCLINE = 213;
	/** "N <host> * <name> <port> <class>" */
	const RPL_STATSNLINE = 214;
	/** "I <host> * <host> <port> <class>" */
	const RPL_STATSILINE = 215;
	/** "K <host> * <username> <port> <class>" */
	const RPL_STATSKLINE = 216;
	/**
	 * "Y <class> <ping frequency> <connect 
	 *  frequency> <max sendq>"
	 */
	const RPL_STATSYLINE = 218;
	/** "<stats letter> :End of /STATS report" */
	const RPL_ENDOFSTATS = 219;
	/** "L <hostmask> * <servername> <maxdepth>" */
	const RPL_STATSLLINE = 241;
	/** ":Server Up %d days %d:%02d:%02d" */
	const RPL_STATSUPTIME = 242;
	/** "O <hostmask> * <name>" */
	const RPL_STATSOLINE = 243;
	/** "H <hostmask> * <servername>" */
	const RPL_STATSHLINE = 244;
	/**
	 * "<user mode string>"
	 *
	 * To answer a query about a client's own mode,
	 * {@link RPL_UMODEIS} is sent back.
	 */
	const RPL_UMODEIS = 221;
	/**
	 * ":There are <integer> users and <integer> 
	 *  invisible on <integer> servers"
	 *
	 * In processing an LUSERS message, the server
	 * sends a set of replies from {@link RPL_LUSERCLIENT},
	 * {@link RPL_LUSEROP}, {@link RPL_USERUNKNOWN},
	 * {@link RPL_LUSERCHANNELS} and {@link RPL_LUSERME}.  When
	 * replying, a server must send back
	 * {@link RPL_LUSERCLIENT} and {@link RPL_LUSERME}.  The other
	 * replies are only sent back if a non-zero count
	 * is found for them.
	 */
	const RPL_LUSERCLIENT = 251;
	/**
	 * "<integer> :operator(s) online"
	 *
	 * In processing an LUSERS message, the server
	 * sends a set of replies from {@link RPL_LUSERCLIENT},
	 * {@link RPL_LUSEROP}, {@link RPL_USERUNKNOWN},
	 * {@link RPL_LUSERCHANNELS} and {@link RPL_LUSERME}.  When
	 * replying, a server must send back
	 * {@link RPL_LUSERCLIENT} and {@link RPL_LUSERME}.  The other
	 * replies are only sent back if a non-zero count
	 * is found for them.
	 */
	const RPL_LUSEROP = 252;
	/**
	 * "<integer> :unknown connection(s)"
	 *
	 * In processing an LUSERS message, the server
	 * sends a set of replies from {@link RPL_LUSERCLIENT},
	 * {@link RPL_LUSEROP}, {@link RPL_USERUNKNOWN},
	 * {@link RPL_LUSERCHANNELS} and {@link RPL_LUSERME}.  When
	 * replying, a server must send back
	 * {@link RPL_LUSERCLIENT} and {@link RPL_LUSERME}.  The other
	 * replies are only sent back if a non-zero count
	 * is found for them.
	 */
	const RPL_LUSERUNKNOWN = 253;
	/**
	 * "<integer> :channels formed"
	 *
	 * In processing an LUSERS message, the server
	 * sends a set of replies from {@link RPL_LUSERCLIENT},
	 * {@link RPL_LUSEROP}, {@link RPL_USERUNKNOWN},
	 * {@link RPL_LUSERCHANNELS} and {@link RPL_LUSERME}.  When
	 * replying, a server must send back
	 * {@link RPL_LUSERCLIENT} and {@link RPL_LUSERME}.  The other
	 * replies are only sent back if a non-zero count
	 * is found for them.
	 */
	const RPL_LUSERCHANNELS = 254;
	/**
	 * ":I have <integer> clients and <integer> servers"
	 *
	 * In processing an LUSERS message, the server
	 * sends a set of replies from {@link RPL_LUSERCLIENT},
	 * {@link RPL_LUSEROP}, {@link RPL_USERUNKNOWN},
	 * {@link RPL_LUSERCHANNELS} and {@link RPL_LUSERME}.  When
	 * replying, a server must send back
	 * {@link RPL_LUSERCLIENT} and {@link RPL_LUSERME}.  The other
	 * replies are only sent back if a non-zero count
	 * is found for them.
	 */
	const RPL_LUSERME = 255;
	/**
	 * "<server> :Administrative info"
	 *
	 * When replying to an ADMIN message, a server
	 * is expected to use replies {@link RLP_ADMINME}
	 * through to {@link RPL_ADMINEMAIL} and provide a text
	 * message with each.  For {@link RPL_ADMINLOC1} a
	 * description of what city, state and country
	 * the server is in is expected, followed by
	 * details of the university and department
	 * ({@link RPL_ADMINLOC2}) and finally the administrative
	 * contact for the server (an email address here
	 * is required) in {@link RPL_ADMINEMAIL}.
	 */
	const RPL_ADMINME = 256;
	/**
	 * ":<admin info>"
	 *
	 * When replying to an ADMIN message, a server
	 * is expected to use replies {@link RLP_ADMINME}
	 * through to {@link RPL_ADMINEMAIL} and provide a text
	 * message with each.  For {@link RPL_ADMINLOC1} a
	 * description of what city, state and country
	 * the server is in is expected, followed by
	 * details of the university and department
	 * ({@link RPL_ADMINLOC2}) and finally the administrative
	 * contact for the server (an email address here
	 * is required) in {@link RPL_ADMINEMAIL}.
	 */
	const RPL_ADMINLOC1 = 257;
	/**
	 * ":<admin info>"
	 *
	 * When replying to an ADMIN message, a server
	 * is expected to use replies {@link RLP_ADMINME}
	 * through to {@link RPL_ADMINEMAIL} and provide a text
	 * message with each.  For {@link RPL_ADMINLOC1} a
	 * description of what city, state and country
	 * the server is in is expected, followed by
	 * details of the university and department
	 * ({@link RPL_ADMINLOC2}) and finally the administrative
	 * contact for the server (an email address here
	 * is required) in {@link RPL_ADMINEMAIL}.
	 */
	const RPL_ADMINLOC2 = 258;
	/**
	 * ":<admin info>"
	 *
	 * When replying to an ADMIN message, a server
	 * is expected to use replies {@link RLP_ADMINME}
	 * through to {@link RPL_ADMINEMAIL} and provide a text
	 * message with each.  For {@link RPL_ADMINLOC1} a
	 * description of what city, state and country
	 * the server is in is expected, followed by
	 * details of the university and department
	 * ({@link RPL_ADMINLOC2}) and finally the administrative
	 * contact for the server (an email address here
	 * is required) in {@link RPL_ADMINEMAIL}.
	 */
	const RPL_ADMINEMAIL = 259;



	/**
	 * The sender of this message
	 *
	 * @var IRC\Sender
	 */
	protected $sender = null;

	/**
	 * The message command.
	 *
	 * @var string
	 */
	protected $command = '';

	/**
	 * The message parameters.
	 *
	 * @var array
	 */
	protected $params = array();

	/**
	 * The raw message string, useful for logging and debugging.
	 *
	 * @var string
	 */
	protected $raw = '';



	/**
	 * Create a new Message object
	 *
	 * @param  IRC\Sender  $sender   The sender of this message.
	 * @param  string      $command  The command sent.
	 * @param  array       $params   The command parameters sent.
	 * @param  string      $raw      The raw message, useful for logging and debugging.
	 */
	public function __construct(Sender $sender = null, $command, $params, $raw = '')
	{
		$this->sender  = $sender;
		$this->command = $command;
		$this->params  = $params;
		$this->raw     = $raw;
	}

	/**
	 * Parse a server message and return a {@link IRC\Message}
	 *
	 * @param  string  $string  The string to parse.
	 * @return IRC\Message|false  A message object or FALSE if the message was empty.
	 */
	public static function parse($string)
	{
		$sender  = null;
		$command = '';
		$params  = array();

		// message    =  [ ":" prefix SPACE ] command [ params ] crlf
		// prefix     =  servername / ( nickname [ [ "!" user ] "@" host ] )
		// command    =  1*letter / 3digit
		// params     =  *14( SPACE middle ) [ SPACE ":" trailing ]
		//                =/ 14( SPACE middle ) [ SPACE [ ":" ] trailing ]

		// nospcrlfcl =  %x01-09 / %x0B-0C / %x0E-1F / %x21-39 / %x3B-FF
		//                     ; any octet except NUL, CR, LF, " " and ":"
		// middle     =  nospcrlfcl *( ":" / nospcrlfcl )
		// trailing   =  *( ":" / " " / nospcrlfcl )

		// SPACE      =  %x20        ; space character
		// crlf       =  %x0D %x0A   ; "carriage return" "linefeed"

		$line = $string = rtrim($string, "\r\n");
		if ($line == '')
		{
			return false;
		}
		// Prefix (Sender)
		if ($line[0] == ':')
		{
			list($prefix, $line) = explode(' ', $line, 2);
			$sender = Sender::parse($prefix);
			unset($prefix);
		}
		// Command
		{
			list($command, $line) = explode(' ', $line, 2);
		}
		// Params
		if (false !== $p = strpos($line, ':'))
		{
			$params = $p > 0 ? explode(' ', substr($line, 0, $p - 1)) : array(); // middle
			$params[] = substr($line, $p + 1); // trailing
		}
		else
		{
			$params = explode(' ', $line); // middle
		}

		return new Message($sender, $command, $params, $string);
	}

	/**
	 * Make an {@link IRC\Message} representing a client message, to be sent to a server
	 *
	 * <code>
	 *     Message::make('USER', 'laravel', 0, '*', ':Laravel Bot', $sender);
	 *     // Or
	 *     Message::make('USER', array('Laravel', 0, '*', ':Laravel Bot'), $sender);
	 * </code>
	 *
	 * @param  string      $command
	 * @param  mixed       $parameter,...  Array of parameters or a list of parameters
	 * @param  IRC\Sender  $sender
	 * @return IRC\Message
	 */
	public static function make($command, $parameter = '', $sender = null)
	{
		$args = func_get_args();
		$sender = null;

		// Sender will be (optionally) the last parameter
		if (count($args) and end($args) instanceof Sender)
		{
			$sender = array_pop($args);
		}

		// The command should be in uppercase
		$args[0] = strtoupper($args[0]);

		// Parameters can be an array themselves
		if (isset($args[1]) and is_array($args[1]))
		{
			// Splice the parameters into the array
			array_splice($args, 1, 1, $args[1]);
		}

		// The last argument may contain spaces, in this case it should be prefixed
		// with a colon (':').  Sometimes the colon will already be there as
		// required by some commands.
		$n = count($args) - 1;
		if ($n >= 0 and false !== strpos($args[$n], ' ') and $args[$n][0] !== ':')
		{
			$args[$n] = ':'.$args[$n];
		}

		// Clients SHOULD NOT use a prefix when sending a message; if they use one,
		// the only valid prefix is the registered nickname associated with the client.
		// if ( ! is_null($sender) and $sender->nick != '')
		// {
		//     $raw = ':'.$sender->nick.' ';
		// }

		$raw = implode(' ', $args);
		$command = array_shift($args);

		return new Message($sender, $command, $args, $raw);
	}

	/**
	 * Listen for a message
	 * 
	 * @param  string $name
	 * @param  Closure $closure
	 */
	public static function listen($name, Closure $closure)
	{
		Event::listen('irc::message: '.$name, $closure);
	}

	/**
	 * Send an array of messages to the given socket.
	 * 
	 * @param  Message[] $list
	 * @param  Socket $socket
	 */
	public static function sendArray($list, Socket $socket)
	{
		foreach ($list as $message)
		{
			if ($message instanceof Message)
			{
				$message->send($socket);
			}
			else if (is_array($message))
			{
				static::sendArray($message, $socket);
			}
		}
	}

	/**
	 * Check if this message is a command response
	 *
	 * Numerics in the range from 001 to 099 are used for client-server
	 * connections only and should never travel between servers.  Replies
	 * generated in the response to commands are found in the range from 200
	 * to 399.
	 *
	 * @return bool
	 */
	public function isResponse() {
		return is_numeric($this->command) and
			(('001' <= $this->command and $this->command <= '099') or ('200' <= $this->command and $this->command <= '399'));
	}

	/**
	 * Check whether this is an error reply
	 *
	 * Error replies are found in the range from 400 to 599.
	 *
	 * @return bool
	 */
	public function isError()
	{
		return $this->command === 'ERROR' or ( is_numeric($this->command) and '400' <= $this->command and $this->command <= '599' );
	}

	/**
	 * Check if this message is a numeric response
	 *
	 * @return bool
	 */
	public function isNumeric()
	{
		return is_numeric($this->command);
	}

	/**
	 * Send the message to the given socket
	 *
	 * @param  Socket  $socket
	 * @return Message
	 * @uses   Socket::write()
	 */
	public function send(Socket $socket)
	{
		$socket->write($this->raw."\r\n");
		Client::log($this, true);
	}

	/**
	 * Get the target(s) of this message
	 *
	 * @return string
	 */
	public function target()
	{
		switch ($this->command) {
			case 'PRIVMSG':
			case 'NOTICE':
			case 'JOIN':
			case 'PART':
				if ($this->params[0] !== '*')
					return $this->params[0];

			default:
				if ($this->isNumeric())
					return $this->params[0];
				return null;
		}
	}

	/**
	 * Return the target, unless the target is $unless, in which case return $then.
	 *
	 * @param  string  $unless
	 * @param  string  $then
	 * @return string
	 * @uses   target()
	 */
	public function targetUnlessThen($unless, $then)
	{
		$target = $this->target();
		return (strcasecmp($target, $unless) === 0) ? $then : $target;
	}

	/**
	 * Return the channel this message is about, if any.
	 *
	 * @return string
	 */
	public function channel()
	{
		$channel = $this->isNumeric() ? $this->params[1] : $this->params[0];
		if ($channel[0] == '#' or $channel[0] == '&')
		{
			return $channel;
		}
		else
		{
			return null;
		}
	}

	/**
	 * Return $count parameters.  If there are less than $count parameters
	 * then $default will be used to populate up to $count items.  If there
	 * are more than $count parameters then last item will contain all the
	 * remaining parameters joined together.
	 *
	 * @param  int $count
	 * @param  mixed $default
	 * @return string[]
	 */
	public function params($count, $default = null)
	{
		$params = $this->params;
		if (count($params) - $count > 0)
		{
			$text = implode(' ', array_slice($params, $count - 1));
			$params = array_slice($params, 0, $count - 1);
			$params[] = $text;
		}
		if (count($params) < $count)
		{
			$array = is_array($default);
			for ($i = count($params); $i < $count; $i++)
			{
				$params[$i] = $array ? $default[$i] : $default;
			}
		}
		return $params;
	}

	/**
	 * Get the string, nick, user, host or server from this Sender.
	 *
	 * @param  string  $name  One of string, nick, user, host or server
	 * @return string  The value of $name
	 */
	public function __get($name)
	{
		return $this->$name;
	}

	/**
	 * Check that the string, nick, user, host or server are present.
	 *
	 * @param  string  $name  One of string, nick, user, host or server
	 * @return bool
	 */
	public function __isset($name)
	{
		return isset($this->$name) and ! empty($this->$name);
	}

	/**
	 * Message objects are immutable, their values may not be changed.
	 *
	 * @param  string
	 * @param  mixed
	 * @internal
	 */
	public final function __set($name, $value) {}

	/**
	 * Message objects are immutable, their values may not be unset.
	 *
	 * @param  string
	 * @internal
	 */
	public final function __unset($name) {}



	/**
	 * Create IRC commands
	 *
	 * @todo Delete me when <b>everything</b> is implemented.
	 * @param  string  $method $he command to send.
	 * @param  array   $parameters The options to send with the command.
	 * @return IRC\Message
	 */
	public static function __callStatic($method, $parameters)
	{
		array_unshift($parameters, strtoupper($method));
		return forward_static_call_array(array(__CLASS__, 'make'), $parameters);
	}

	/**
	 * Password message
	 *
	 * The PASS command is used to set a 'connection password'.  The
	 * optional password can and MUST be set before any attempt to register
	 * the connection is made.  Currently this requires that user send a
	 * PASS command before sending the NICK/USER combination.
	 *
	 * @see IRC\Message::ERR_NEEDMOREPARAMS
	 * @see IRC\Message::ERR_ALREADYREGISTRED
	 *
	 * @link http://tools.ietf.org/html/rfc2812#section-3.1.1
	 *
	 * @param  string  $password
	 * @return IRC\Message
	 * @uses   make()
	 */
	public static function pass($password)
	{
		//    Command: PASS
		// Parameters: <password>
		return static::make('PASS', $password);
	}

	/**
	 * Nick message
	 *
	 * NICK command is used to give user a nickname or change the existing one.
	 *
	 * @see IRC\Message::ERR_NONICKNAMEGIVEN
	 * @see IRC\Message::ERR_ERRONEUSNICKNAME
	 * @see IRC\Message::ERR_NICKNAMEINUSE
	 * @see IRC\Message::ERR_NICKCOLLISION
	 * @see IRC\Message::ERR_UNAVAILRESOURCE
	 * @see IRC\Message::ERR_RESTRICTED
	 *
	 * @link http://tools.ietf.org/html/rfc2812#section-3.1.2
	 *
	 * @param  string $nick
	 * @return IRC\Message
	 * @uses   make()
	 */
	public static function nick($nick)
	{
		//    Command: NICK
		// Parameters: <nickname>
		return static::make('NICK', $nick);
	}

	/**
	 * User message
	 *
	 * The USER command is used at the beginning of connection to specify
	 * the username and realname of a new user.
	 *
	 * @see IRC\Message::ERR_NEEDMOREPARAMS
	 * @see IRC\Message::ERR_ALREADYREGISTRED
	 *
	 * @link http://tools.ietf.org/html/rfc2812#section-3.1.3
	 *
	 * @param  string $username
	 * @param  string $realname
	 * @return IRC\Message
	 * @uses   make()
	 */
	public static function user($username, $realname)
	{
		//    Command: USER
		// Parameters: <user> <mode> <unused> <realname>
		return static::make('USER', $username, 8, '*', ":$realname");
	}

	/**
	 * Quit
	 *
	 * Leave the server with an optional message
	 *
	 * @param  string  $message
	 * @return IRC\Message
	 * @uses   make()
	 */
	public static function quit($message = '')
	{
		//    Command: QUIT
		// Parameters: [ <Quit Message> ]
		return static::make('QUIT', ":$message (laravel.com)");
	}

	/**
	 * Join message
	 *
	 * Join a channel
	 *
	 * @see IRC\Message::ERR_NEEDMOREPARAMS
	 * @see IRC\Message::ERR_BANNEDFROMCHAN
	 * @see IRC\Message::ERR_INVITEONLYCHAN
	 * @see IRC\Message::ERR_BADCHANNELKEY
	 * @see IRC\Message::ERR_CHANNELISFULL
	 * @see IRC\Message::ERR_BADCHANMASK
	 * @see IRC\Message::ERR_NOSUCHCHANNEL
	 * @see IRC\Message::ERR_TOOMANYCHANNELS
	 * @see IRC\Message::ERR_TOOMANYTARGETS
	 * @see IRC\Message::ERR_UNAVAILRESOURCE
	 * @see IRC\Message::RPL_TOPIC
	 *
	 * @link http://tools.ietf.org/html/rfc2812#section-3.2.1
	 *
	 * @param  string  $channel
	 * @param  string  $key
	 * @return IRC\Message
	 * $uses make()
	 */
	public static function join($channel, $key = '')
	{
		return static::make('JOIN', $channel, $key);
	}

	/**
	 * Part message
	 *
	 * Leave a channel (or channels) with an optional message.
	 *
	 * @see IRC\Message::ERR_NEEDMOREPARAMS
	 * @see IRC\Message::ERR_NOSUCHCHANNEL
	 * @see IRC\Message::ERR_NOTONCHANNEL
	 *
	 * @link http://tools.ietf.org/html/rfc2812#section-3.2.2
	 *
	 * @param  string  $channel
	 * @param  string  $message
	 * @return IRC\Message
	 * @uses   make()
	 */
	public static function part($channel, $message = '')
	{
		//    Command: PART
		// Parameters: <channel> *( "," <channel> ) [ <Part Message> ]
		return static::make('PART', $channel, ":$message");
	}

	/**
	 * Private messages
	 *
	 * Send a private message to a user or channel ($target).
	 *
	 * @see IRC\Message::ERR_NORECIPIENT
	 * @see IRC\Message::ERR_NOTEXTTOSEND
	 * @see IRC\Message::ERR_CANNOTSENDTOCHAN
	 * @see IRC\Message::ERR_NOTOPLEVEL
	 * @see IRC\Message::ERR_WILDTOPLEVEL
	 * @see IRC\Message::ERR_TOOMANYTARGETS
	 * @see IRC\Message::ERR_NOSUCHNICK
	 * @see IRC\Message::RPL_AWAY
	 *
	 * @link http://tools.ietf.org/html/rfc2812#section-3.3.1
	 *
	 * @param  string  $target  User or Channel
	 * @param  string  $text
	 * @return IRC\Message
	 * @uses   make()
	 */
	public static function privmsg($target, $text)
	{
		//    Command: PRIVMSG
		// Parameters: <msgtarget> <text to be sent>
		return static::make('PRIVMSG', $target, ":$text");
	}

	/**
	 * Notice
	 *
	 * @link http://tools.ietf.org/html/rfc2812#section-3.3.2
	 *
	 * @param  string  $target  User or Channel
	 * @param  string  $text
	 * @return IRC\Message
	 * @uses   make()
	 */
	public static function notice($target, $text)
	{
		//    Command: NOTICE
		// Parameters: <msgtarget> <text>
		return static::make('NOTICE', $target, ":$text");
	}

	/**
	 * Whois query
	 *
	 * This command is used to query information about particular user.
	 *
	 * @see IRC\Message::ERR_NOSUCHSERVER
	 * @see IRC\Message::ERR_NONICKNAMEGIVEN
	 * @see IRC\Message::RPL_WHOISUSER
	 * @see IRC\Message::RPL_WHOISCHANNELS
	 * @see IRC\Message::RPL_WHOISSERVER
	 * @see IRC\Message::RPL_AWAY
	 * @see IRC\Message::RPL_WHOISOPERATOR
	 * @see IRC\Message::RPL_WHOISIDLE
	 * @see IRC\Message::ERR_NOSUCHNICK
	 * @see IRC\Message::RPL_ENDOFWHOIS
	 *
	 * @link http://tools.ietf.org/html/rfc2812#section-3.6.2
	 *
	 * @param  string  $user
	 * @return IRC\Message
	 * @uses   make()
	 */
	public static function whois($user)
	{
		//    Command: WHOIS
		// Parameters: [ <target> ] <mask> *( "," <mask> )
		return static::make('WHOIS', $user);
	}

	/**
	 * Pong message
	 *
	 * A PONG message is a reply to ping message.
	 *
	 * @see IRC\Message::ERR_NOORIGIN
	 * @see IRC\Message::ERR_NOSUCHSERVER
	 *
	 * @link http://tools.ietf.org/html/rfc2812#section-3.7.3
	 *
	 * @param  string  $daemon
	 * @return IRC\Message
	 * @uses make()
	 */
	public static function pong($daemon)
	{
		return static::make('PONG', ":$daemon");
	}

}
