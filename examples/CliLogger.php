<?php

use Psr\Log\AbstractLogger;

/**
 * Based on:
 * https://gist.github.com/sallar/5257396.
 */
class CliLogger extends AbstractLogger
{
    public static $foreground_colors = [
        'bold' => '1',
        'dim' => '2',
        'black' => '0;30',
        'dark_gray' => '1;30',
        'blue' => '0;34',
        'light_blue' => '1;34',
        'green' => '0;32',
        'light_green' => '1;32',
        'cyan' => '0;36',
        'light_cyan' => '1;36',
        'red' => '0;31',
        'light_red' => '1;31',
        'purple' => '0;35',
        'light_purple' => '1;35',
        'brown' => '0;33',
        'yellow' => '1;33',
        'light_gray' => '0;37',
        'white' => '1;37',
        'normal' => '0;39',
    ];

    public static $background_colors = [
        'black' => '40',
        'red' => '41',
        'green' => '42',
        'yellow' => '43',
        'blue' => '44',
        'magenta' => '45',
        'cyan' => '46',
        'light_gray' => '47',
    ];

    public static $options = [
        'underline' => '4',
        'blink' => '5',
        'reverse' => '7',
        'hidden' => '8',
    ];

    public static $color = [
        'emergency' => ['foreground' => 'white', 'background' => ['red', 'underline']],
        'alert' => ['foreground' => 'white', 'background' => ['red']],
        'critical' => ['foreground' => 'red'],
        'error' => ['foreground' => 'light_red'],
        'warning' => ['foreground' => 'yellow'],
        'notice' => ['foreground' => 'normal'],
        'info' => ['foreground' => 'dark_gray'],
        'debug' => ['foreground' => 'dim'],
    ];

    /**
     * Catches static calls (Wildcard).
     *
     * @param string $foreground_color Text Color
     * @param array  $args             Options
     *
     * @return string Colored string
     */
    public static function __callStatic($foreground_color, $args)
    {
        [$string, $background] = $args;

        $colored_string = '';

        // Check if given foreground color found
        if (isset(self::$foreground_colors[$foreground_color])) {
            $colored_string .= "\033[".self::$foreground_colors[$foreground_color].'m';
        } else {
            die($foreground_color.' not a valid color');
        }

        if (is_array($background)) {
            foreach ($background as $option) {
                // Check if given background color found
                if (isset(self::$background_colors[$option])) {
                    $colored_string .= "\033[".self::$background_colors[$option].'m';
                } elseif (isset(self::$options[$option])) {
                    $colored_string .= "\033[".self::$options[$option].'m';
                }
            }
        }

        // Add string and end coloring
        $colored_string .= $string."\033[0m";

        return $colored_string;
    }

    /**
     * {@inheritdoc}
     */
    public function log($level, $message, array $context = []): void
    {
        $foreground = self::$color[$level]['foreground'];
        $background = self::$color[$level]['background'] ?? null;
        $level = strtoupper($level);
        $padding = str_repeat(' ', 10 - strlen($level));

        if (!empty($context)) {
            $contextContent = [];
            foreach ($context as $key => $value) {
                if (is_array($value) || is_object($value)) {
                    $value = str_replace("\n", "\n\t\t", print_r($value, true));
                }

                $contextContent[] = sprintf(
                    "\t\t%s%s",
                    self::dark_gray($key, ['bold']),
                    self::dark_gray(': '.$value, null)
                );
            }
            $parsedContext = "\n".implode("\n", $contextContent);
        }

        $formattedMessage = sprintf("[ %s ]%s%s%s\n", $level, $padding, $message, $parsedContext ?? null);

        echo self::$foreground($formattedMessage, $background);
    }
}
