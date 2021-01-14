<?php
namespace IzuSoft\Logger;

use Exception;
use Monolog\Formatter\LineFormatter;
use Monolog\Logger;
use Monolog\Handler\HandlerInterface;
use Monolog\Handler\StreamHandler;
use ReflectionObject;
use function get_class;
use function gettype;
use function in_array;

/**
 * Class LogMessageToChannels
 * @package IzuSoft\Logger
 */
class LogMessageToChannels
{
    /**
     * The LogToChannels channels.
     *
     * @var Logger[]
     */
    protected array $channels = [];

    /**
     * @param string $channel The channel to log the record in
     * @param int $level The error level
     * @param mixed $message The error message
     * @param mixed $context Optional context arguments
     * @param int $nesting
     *
     * @return bool Whether the record has been processed
     */
    public function log(string $channel, int $level, $message, $context = null, int $nesting = 5): bool
    {
        try {
            // Add the logger if it doesn't exist
            if (!isset($this->channels[$channel])) {
                $handler = new StreamHandler(
                    storage_path() . DIRECTORY_SEPARATOR . 'logs' . DIRECTORY_SEPARATOR . $channel . '.log'
                );

                $this->addChannel($channel, $handler);
            }

            $message = $this->formatMessage($message, $nesting);
            $message = $context !== null
                ? $message . "\n```\n" . $this->formatMessage($context, $nesting) . "\n```\n"
                : $message . "\n";
            $this->channels[$channel]->{Logger::getLevelName($level)}($message, []);
            return true;
        }
        catch (Exception $exception) {

            return false;
        }
    }

    /**
     * Add a channel to log in
     *
     * @param string           $channelName The channel name
     * @param HandlerInterface $handler     The channel handler
     * @param string|null      $path        The path of the channel file, DEFAULT storage_path()/logs
     */
    protected function addChannel(string $channelName, HandlerInterface $handler, string $path = null): void
    {
        $this->channels[$channelName] = new Logger($channelName);
        /** @var StreamHandler $streamHandler */
        $streamHandler = (new $handler(
            $path === null
                ? storage_path() . DIRECTORY_SEPARATOR . 'logs' . DIRECTORY_SEPARATOR . $channelName . '.log'
                : $path . DIRECTORY_SEPARATOR . $channelName . '.log'
        ));
        $this->channels[$channelName]->pushHandler(
            $streamHandler->setFormatter(new LineFormatter("[%datetime%] %channel%.%level_name%: %message%\n", 'Y-m-d H:i:s', true, true))
        );
    }

    /**
     * Adds a log record at the DEBUG level.
     *
     * @param string $channel The channel name
     * @param mixed $message The log message
     * @param mixed $context The log context
     * @param int $nesting
     *
     * @return bool Whether the record has been processed
     */
    public function debug(string $channel, $message, $context = null, int $nesting = 5): bool
    {
        return $this->log($channel, Logger::DEBUG, $message, $context, $nesting);
    }

    /**
     * Adds a log record at the INFO level.
     *
     * @param  string $channel The channel name
     * @param  mixed $message The log message
     * @param  mixed $context The log context
     * @param int $nesting
     *
     * @return bool Whether the record has been processed
     */
    public function info(string $channel, $message, $context = null, int $nesting = 5): bool
    {
        return $this->log($channel, Logger::INFO, $message, $context, $nesting);
    }

    /**
     * Adds a log record at the NOTICE level.
     *
     * @param  string $channel The channel name
     * @param  mixed $message The log message
     * @param  mixed $context The log context
     * @param int $nesting
     *
     * @return bool Whether the record has been processed
     */
    public function notice(string $channel, $message, $context = null, int $nesting = 5): bool
    {
        return $this->log($channel, Logger::NOTICE, $message, $context, $nesting);
    }

    /**
     * Adds a log record at the WARNING level.
     *
     * @param  string $channel The channel name
     * @param  mixed $message The log message
     * @param  mixed $context The log context
     * @param int $nesting
     *
     * @return bool Whether the record has been processed
     */
    public function warning(string $channel, $message, $context = null, int $nesting = 5): bool
    {
        return $this->log($channel, Logger::WARNING, $message, $context, $nesting);
    }

    /**
     * Adds a log record at the ERROR level.
     *
     * @param  string $channel The channel name
     * @param  mixed $message The log message
     * @param  mixed $context The log context
     * @param int $nesting
     *
     * @return bool Whether the record has been processed
     */
    public function error(string $channel, $message, $context = null, int $nesting = 5): bool
    {
        return $this->log($channel, Logger::ERROR, $message, $context, $nesting);
    }

    /**
     * Adds a log record at the CRITICAL level.
     *
     * @param  string $channel The channel name
     * @param  mixed $message The log message
     * @param  mixed $context The log context
     * @param int $nesting
     *
     * @return Boolean Whether the record has been processed
     */
    public function critical(string $channel, $message, $context = null, int $nesting = 5): bool
    {
        return $this->log($channel, Logger::CRITICAL, $message, $context, $nesting);
    }

    /**
     * Adds a log record at the ALERT level.
     *
     * @param  string $channel The channel name
     * @param  mixed $message The log message
     * @param  mixed $context The log context
     * @param int $nesting
     *
     * @return bool Whether the record has been processed
     */
    public function alert(string $channel, $message, $context = null, int $nesting = 5): bool
    {
        return $this->log($channel, Logger::ALERT, $message, $context, $nesting);
    }

    /**
     * Adds a log record at the EMERGENCY level.
     *
     * @param  string $channel The channel name
     * @param  mixed $message The log message
     * @param  mixed $context The log context
     * @param int $nesting
     *
     * @return bool Whether the record has been processed
     */
    public function emergency(string $channel, $message, $context = null, int $nesting = 5): bool
    {
        return $this->log($channel, Logger::EMERGENCY, $message, $context, $nesting);
    }

    /**
     * Format the parameters for the logger.
     *
     * @param mixed $message
     * @param int $nesting
     * @return mixed
     */
    protected function formatMessage($message, int $nesting)
    {
        if ($message === null) {
            return 'null';
        }

        if (empty($message)) {
            return 'empty';
        }

        if ($message instanceof Exception) {
            return $message;
        }

        if (is_array($message) || is_object($message)) {
            return self::printRLevel($message, $nesting);
        }

        if (is_string($message) && $json = json_decode($message, true)) {
            return json_encode($json, JSON_PRETTY_PRINT);
        }

        return $message;
    }

    /**
     * @param $data
     * @param int $nesting
     * @return string
     */
    protected static function printRLevel($data, int $nesting): string
    {
        static $innerLevel = 1;

        static $tabLevel = 1;

        static $cache = array();

        $output     = '';
        $type       = gettype($data);
        $tabs       = str_repeat('    ', $tabLevel);
        $quoteTabs = str_repeat('    ', $tabLevel - 1);

        $recursiveType = array('object', 'array');

        // Recursive
        if (in_array($type, $recursiveType, true))
        {
            $elements = array();

            // If type is object, try to get properties by Reflection.
            if ($type === 'object')
            {
                if (self::inArrayRecursive($data, $cache)) {
                    return "\n{$quoteTabs}*RECURSION*\n";
                }

                // Cache the data
                $cache[] = $data;

                $output     = get_class($data) . ' ' . ucfirst($type);
                $ref        = new ReflectionObject($data);
                $properties = $ref->getProperties();

                foreach ($properties as $property)
                {
                    $property->setAccessible(true);

                    $pType = '\''.$property->getName().'\'';

                    if ($property->isProtected())
                    {
                        $pType = 'protected:' .$pType;
                    }
                    elseif ($property->isPrivate())
                    {
                        $pType = 'private:' .$pType;
                    }

                    if ($property->isStatic())
                    {
                        $pType = 'static:' .$pType;
                    }

                    $elements[$pType] = $property->getValue($data);
                }
            }
            // If type is array, just return it's value.
            elseif ($type === 'array')
            {
                $output = ucfirst($type);
                $elements = $data;
            }

            // Start dumping data
            if ($nesting === 0 || $innerLevel <= $nesting)
            {
                // Start recursive print
                $output .= "\n{$quoteTabs}(";

                foreach ($elements as $key => $element)
                {
                    $output .= "\n{$tabs}{$key} => ";

                    // Increment level
                    $tabLevel += 2;
                    $innerLevel++;

                    $output  .= in_array(gettype($element), $recursiveType, true) ? self::printRLevel($element, $nesting) : $element;

                    // Decrement level
                    $tabLevel -= 2;
                    $innerLevel--;
                }

                $output .= "\n{$quoteTabs})\n";
            }
            else
            {
                $output .= "\n{$quoteTabs}*MAX LEVEL*\n";
            }
        }

        // Clean cache
        if($innerLevel === 1)
        {
            $cache = array();
        }

        return $output;
    }

    /**
     * решает проблемму "Nesting level too deep"
     * @param $needle
     * @param $haystack
     * @return bool
     */
    private static function inArrayRecursive($needle, $haystack): bool
    {
        foreach($haystack AS $element) {
            if($element === $needle) {
                return true;
            }
        }

        return false;
    }
}
