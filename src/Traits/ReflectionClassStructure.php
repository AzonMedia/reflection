<?php

namespace Azonmedia\Reflection\Traits;

use Azonmedia\Reflection\ReflectionMethod;

trait ReflectionClassStructure
{
    /**
     * Returns the full structure of the class as string.
     * @return string
     */
    public function getClassStructure() : string
    {
        $ret = '<?php'.PHP_EOL;

        $namespace = $this->getNamespaceName();
        if ($namespace) {
            $ret .= 'namespace '.$namespace.';'.PHP_EOL;
        }

        $modifiers = \Reflection::getModifierNames($this->getModifiers());
        if ($modifiers) {
            $ret .= implode(' ', $modifiers) . ' ';
        }
        $ret .= 'class ' . $this->getShortName();
        if ($ParentRClass = $this->getParentClass()) {
            $ret .= ' extends \\'.$ParentRClass->name;
        }
        if ($interfaces = $this->getOwnInterfaceNames()) {
            array_walk($interfaces, function(&$value){ $value = '\\'.$value; });
            $ret .= ' implements '.implode(', ',$interfaces);
        }
        $ret .= PHP_EOL;

        $ret .= '{'.PHP_EOL;

        if ($traits = $this->getTraitNames()) {
            array_walk($traits, function(&$value){ $value = '\\'.$value; });
            $ret .= ' use '.implode(', ',$traits).';';
        }

        $ret .= $this->getPropertiesDeclaration();
        $ret .= $this->getConstantsDeclaration();
        $ret .= $this->getMethodsDeclaration();

        $ret .= '}'.PHP_EOL;

        return $ret;
    }

    /**
     * Returns a multiline string with all the properties declarations.
     * @param int $filter
     * @return string
     */
    public function getPropertiesDeclaration(int $filter = 0) : string
    {
        $ret = '';
        $default_properties = $this->getDefaultProperties();
        foreach ($this->getProperties() as $RProperty) {
            if (!$RProperty->isDefault()) {
                //it was defined at runtime - not part of the class definition
                continue;
            }

            if ($RProperty->class === $this->name) {
                $modifiers = \Reflection::getModifierNames($RProperty->getModifiers());

                $ret .= '    '.implode(' ',$modifiers).' $'.$RProperty->name;
                if ($prop_value = $default_properties[$RProperty->name]) {
                    $ret .= ' = '.var_export($prop_value, TRUE);
                }
                $ret .= ';'.PHP_EOL;
            }

        }
        return $ret;
    }

    /**
     * Returns a multiline string with all constants declarations.
     * @return string
     */
    public function getConstantsDeclaration() : string
    {
        $ret = '';
        $constants = $this->getConstants();
        foreach ($this->getReflectionConstants() as $RConstant) {
            if ($RConstant->class === $this->name) { //is it defined in this class or is coming form the parent
                $modifiers = \Reflection::getModifierNames($RConstant->getModifiers());
                $ret .= '    '.implode(' ',$modifiers).' const '.$RConstant->name;
                $ret .= ' = '.var_export($constants[$RConstant->name], TRUE);
                $ret .= ';'.PHP_EOL;
            }
        }
        return $ret;
    }

    /**
     * Returns a multiline string with all methods signatures.
     * @return string
     * @throws \ReflectionException
     */
    public function getMethodsDeclaration() : string
    {

        $ret = '';
        foreach ($this->getMethods() as $RMethod) {
            if ($RMethod->class === $this->name) { //is it defined in this class or is coming form the parent
                $RMethod = new ReflectionMethod($this->name, $RMethod->name);
                $ret .= $RMethod->getSignature().PHP_EOL;
            }
        }

        return $ret;
    }

}