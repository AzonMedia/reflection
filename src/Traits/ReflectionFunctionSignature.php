<?php


namespace Azonmedia\Reflection\Traits;


trait ReflectionFunctionSignature
{
    /**
     * Returns the argument list as a signature
     * @return string
     */
    public function getArgumentList() : string
    {

    }

    /**
     * Returns the full method/function signature
     * @return string
     */
    public function getSignature() : string
    {
        $ret = '';
        $function_arr = ['function'];
        if ($this instanceof \ReflectionMethod) {
            if ($this->isAbstract()) {
                array_unshift($method_arr, 'abstract');
            }
            if ($this->isPublic()) {
                array_unshift($method_arr, 'public');
            }
            if ($this->isProtected()) {
                array_unshift($method_arr, 'protected');
            }
            if ($this->isPrivate()) {
                array_unshift($method_arr, 'private');
            }
            if ($this->isStatic()) {
                $method_arr[] = 'static';
            }
        }

    }
}