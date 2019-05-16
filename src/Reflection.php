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


    /**
     * Returns a ReflectionFunction based on the provided callable.
     * For example for a::b it will return a ReflectionMethod, while 'asd', will return ReflectionFunction
     *
     * This method is needed because the PHP callable supports many formats while
     *
     * @param callable $callable
     * @return \ReflectionFunctionAbstract
     *
     * @author vesko@azonmedia.com
     * @created 5.10.2019
     */
    public static function getFromCallable(callable $callable) : \ReflectionFunctionAbstract
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
        } elseif(is_array($callable)) {
            //this may be
            //dynamic call - [$instnace, 'methodname']
            //static call - ['classname', 'methodname']
            //in both cases the below should be OK
            $ret = new \ReflectionMethod($callable[0], $callable[1]);
        } elseif($callable instanceof \Closure) {
            $ret = new \ReflectionFunction($callable);
        } else {
            //this is an object with __invoke defined
            $ret = new \ReflectionMethod($callable, '__invoke');
        }
        return $ret;
    }
}