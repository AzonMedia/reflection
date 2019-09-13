<?php


namespace Azonmedia\Reflection\Traits;


trait ReflectionParameterSignature
{

    /**
     * Returns the signature of the parameter as string.
     * @return string
     */
    public function getSignature() : string
    {
        $ret = '';
        if ($RType = $this->getType()) {
            $ret .= ($RType->allowsNull() ? '?' : '').($RType->isBuiltin() ? '' : '\\').$RType.' ';
        }
        if ($this->isPassedByReference()) {
            $ret .= '&';
        }
        $ret .= '$'.$this->name;
        if ($this->isDefaultValueAvailable()) {
            if ($this->isDefaultValueConstant()) {
                $ret .= ' = '.$this->getDefaultValueConstantName();
            } else {
                $ret .= ' = '.var_export($this->getDefaultValue(), TRUE);
            }
        }

        return $ret;
    }
}