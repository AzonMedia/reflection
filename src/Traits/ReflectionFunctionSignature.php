<?php


namespace Azonmedia\Reflection\Traits;


use Azonmedia\Reflection\ReflectionParameter;

trait ReflectionFunctionSignature
{
    /**
     * Returns the argument list as a string comprised of the signatures for each parameter.
     * @return string
     */
    public function getParametersList() : string
    {
        $ret = '';
        $param_arr = [];
        foreach ($this->getParameters() as $RParam) {
            $RParam = new ReflectionParameter([$this->getDeclaringClass()->name, $this->name], $RParam->name);
            $param_arr[] = $RParam->getSignature();
        }

        return implode(', ', $param_arr);
    }

    /**
     * Returns the full method/function signature.
     * @return string
     */
    public function getSignature() : string
    {
        $modifiers = \Reflection::getModifierNames($this->getModifiers());

        $ret = '';
        $ret .= '    '.implode(' ',$modifiers).' function ';
        if ($this->returnsReference()) {
            $ret .= '&';
        }
        $ret .= $this->name.'( '.$this->getParametersList().')';

        if ($RType = $this->getReturnType()) {
            $ret .= ' : '.($RType->allowsNull() ? '?' : '').($RType->isBuiltin() ? '' : '\\').$RType;
        }

        $ret .= ' { }'.PHP_EOL;

        return $ret;
    }
}