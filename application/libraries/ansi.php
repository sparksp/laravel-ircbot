<?php

/**
 * ANSI
 *
 * Methods to format console output using ANSI escape codes.
 *
 * <code>
 *     // Without included text...
 *     echo ANSI::color(ANSI::RED)."red".ANSI::color(ANSI::YELLOW)."yellow".ANSI::reset()."\n";
 *     // With included text automatically resets...
 *     echo ANSI::color(ANSI::BLUE, false, "Some blue text")."\n";
 *     echo ANSI::color(ANSI::GREEN, true, "Some bold, green text")."\n";
 * </code>
 *
 * @author Phill Sparks <me@phills.me.uk>
 * @version 1.0
 * @package ansi
 */
abstract class ANSI {

	/**
	 * Escape Character
	 * @internal
	 */
	const ESC = "\x1B";

	/**
	 * Turn all attributes off 
	 * @internal
	 */
	const RESET = 0;

	/**
	 * Increased intensity text
	 * @internal
	 */
	const BRIGHT = 1;
	/**
	 * Alias for {@link BRIGHT}
	 * @internal
	 */
	const BOLD = 1;
	/**
	 * Normal intensity text
	 * @internal
	 */
	const NORMAL = 22;

	/**
	 * Underline
	 * @internal
	 */
	const UNDERLINE = 4;
	/**
	 * Turn underline off
	 * @internal
	 */
	const UNDERLINE_OFF = 24;

	/**
	 * Overline
	 * @internal
	 */
	const OVERLINE = 53;
	/**
	 * Turn overline off
	 * @internal
	 */
	const OVERLINE_OFF = 55;

	/**
	 * Framed text
	 * @internal
	 */
	const FRAMED = 51;
	/**
	 * Encircled text
	 * @internal
	 */
	const ENCIRCLED = 52;
	/**
	 * Turn framed and encircled off
	 * @internal
	 */
	const FRAMED_OFF = 53;

	/**
	 * Blink (less than 150 times per minute)
	 * @internal
	 */
	const BLINK = 5;
	/**
	 * Turn blink off
	 * @internal
	 */
	const BLINK_OFF = 25;

	/**
	 * Invert foreground and background
	 * @internal
	 */
	const NEGATIVE = 7;
	/**
	 * Restore foreground and background
	 * @internal
	 */
	const POSITIVE = 27;

	/**
	 * Set the foreground color
	 *
	 * {@link FOREGROUND} + COLOR
	 */
	const FOREGROUND = 30; // + color
	/**
	 * Set the background color
	 *
	 * {@link BACKGROUND} + COLOR
	 */
	const BACKGROUND = 40; // + color

	/** Color: Black */
	const BLACK = 0;
	/** Color: Red */
	const RED = 1;
	/** Color: Green */
	const GREEN = 2;
	/** Color: Yellow */
	const YELLOW = 3;
	/** Color: Blue */
	const BLUE = 4;
	/** Color: Magenta */
	const MAGENTA = 5;
	/** Color: Cyan */
	const CYAN = 6;
	/** Color: White */
	const WHITE = 7;

	/**
	 * Change the foreground or background color
	 *
	 * If text is provided then the returned string includes a reset command.
	 *
	 * @param  int     $color
	 * @param  bool    $bold
	 * @param  string  $text
	 * @return string
	 */
	public static function color($color, $bold = false, $text = '')
	{
		if ($color < 10)
		{
			$color += static::FOREGROUND;
		}
		$bold = (int) !!$bold;

		if ($text)
		{
			return static::escape('m', $bold, $color).$text.static::reset();
		}
		return static::escape('m', $bold, $color);
	}

	/**
	 * Enable bold
	 *
	 * If text is provided then the returned string includes a reset command.
	 *
	 * @param  string  $text
	 * @return string
	 */
	public static function bold($text = '')
	{
		return static::escape('m', static::BOLD) . ($text ? $text.static::reset() : '');
	}

	/**
	 * Turn all attributes off
	 *
	 * @return string
	 */
	public static function reset()
	{
		return static::escape('m', 0);
	}

	/**
	 * @param  char   $code
	 * @param  mixed  $params...
	 * @return string
	 */
	protected static function escape($code, $params)
	{
		$params = array_slice(func_get_args(), 1);
		return static::ESC.'['.implode(';', $params).$code;
	}

}
