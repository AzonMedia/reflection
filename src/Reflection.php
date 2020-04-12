<?php
declare(strict_types=1);


namespace Azonmedia\Reflection;

/**
 * Class Reflection
 * @package Reflection
 *
 */
class Reflection extends \Reflection
{

    public const SOURCE_INDENTATION = 4;

    // https://www.php.net/manual/en/function.settype.php
    public const PHP_TYPES = [
        "boolean", "bool",
        "integer", "int",
        "float", "double",
        "string",
        "array",
        "object",
        "null"
    ];

    public static function indent(string $code, $indentations = 1)
    {
        $code = str_repeat(' ', self::SOURCE_INDENTATION * $indentations) . $code;
        $code = str_replace(PHP_EOL, PHP_EOL . str_repeat(' ', self::SOURCE_INDENTATION * $indentations), $code);
        return $code;
    }

    /**
     * Returns a ReflectionFunction based on the provided callable.
     * For example for a::b it will return a ReflectionMethod, while 'asd', will return ReflectionFunction
     *
     * This method is needed because the PHP callable supports many formats while
     *
     * @param callable $callable
     * @return \ReflectionFunctionAbstract
     *
     * @throws \ReflectionException
     * @author vesko@azonmedia.com
     * @created 5.10.2019
     */
    public static function getFromCallable(callable $callable): \ReflectionFunctionAbstract
    {
        if (is_string($callable)) {
            //this may be:
            //function - 'functionname'
            //static method call - 'classname::methodname'
            if (strpos($callable, '::') >= 1) {
                [$class, $method] = explode('::', $callable, 2);
                $ret = new \ReflectionMethod($class, $method);
            } else {
                //no need to do a function_exists call as the callable type to the $callable parameter already checks this
                $ret = new \ReflectionFunction($callable);
            }
        } elseif (is_array($callable)) {
            //this may be
            //dynamic call - [$instnace, 'methodname']
            //static call - ['classname', 'methodname']
            //in both cases the below should be OK
            $ret = new \ReflectionMethod($callable[0], $callable[1]);
        } elseif ($callable instanceof \Closure) {
            $ret = new \ReflectionFunction($callable);
        } else {
            //this is an object with __invoke defined
            $ret = new \ReflectionMethod($callable, '__invoke');
        }
        return $ret;
    }

    public static function getTypes(): array
    {
        return self::PHP_TYPES;
    }

    public static function isValidType(string $type): bool
    {
        return in_array($type, self::getTypes(), TRUE);
    }
}