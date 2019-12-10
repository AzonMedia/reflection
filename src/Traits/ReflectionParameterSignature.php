<?php
declare(strict_types=1);


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

            //$ret .= ($RType->allowsNull() ? '?' : '').($RType->isBuiltin() ? '' : '\\').$RType.' ';
            $ret .= ($RType->allowsNull() ? '?' : '').($RType->isBuiltin() ? '' : '\\').$RType->getName().' ';
            if ($this->isVariadic()) {
                $ret .= '...';
            }
        }
        if ($this->isPassedByReference()) {
            $ret .= '&';
        }
        $ret .= '$'.$this->getName();
        if ($this->isDefaultValueAvailable()) {
            if ($this->isDefaultValueConstant()) {
                $ret .= ' = '.$this->getDefaultValueConstantName();
            } else {
                $ret .= ' = '.var_export($this->getDefaultValue(), TRUE);
            }
        }

        return $ret;
    }

    public function generateDocComment() : string
    {
        $ret = '';
        $ret .= '@param ';
        if ($RType = $this->getType()) {
            $ret .= ($RType->allowsNull() ? 'null|' : '').($RType->isBuiltin() ? '' : '\\').$RType.' ';
            if ($this->isPassedByReference()) {
                $ret .= '&';
            }
            if ($this->isVariadic()) {
                $ret .= '...';
            }
        } else {
            if ($this->isPassedByReference()) {
                $ret .= '&';
            }
            if ($this->isVariadic()) {
                $ret .= '...';
            } else {
                $ret .= 'type ';
            }
        }
        $ret .= '$'.$this->getName();
        return $ret;
    }
}