<?php
declare(strict_types=1);


namespace Azonmedia\Reflection;

/**
 * Class ReflectionClass
 * @package Reflection
 *
 */
class ReflectionClass extends \ReflectionClass
{
    /**
     * @return array
     */
    public function getParentClasses() : array
    {
        $stack = array();
        $parent_class = $this;
        while ($parent_class = $parent_class->getParentClass()) {
            $stack[] = $parent_class->name;
        }
        return $stack;
    }

    /**
     * Returns bool does this class extend the provided $class
     * @param string $class
     * @return bool
     */
    public function extendsClass(string $class) : bool
    {
        return in_array($class,$this->getParentClasses());
    }

    /**
     * Returns the inheritance distance between this class and the provided $class (which should be a parent class)
     * @param string $class
     * @return int
     */
    public function extendsClassDistance(string $class) : int
    {
        $pos = array_search($class, $this->getParentClasses());
        if ($pos===false) {
            return $pos;
        } else {
            return $pos+1;//0 position means 1 distance
        }
    }

    /**
     * Checks does the class define the provided methods as its own (not through inheritance)
     * @param string $method Method name
     * @return bool
     */
    public function hasOwnMethod(string $method) : bool
    {
        $ret = FALSE;
        try {
            $Rmethod = $this->getMethod($method);
            if ($Rmethod->name == $method && $Rmethod->class == $this->name) {
                $ret = TRUE;
            }
        } catch (\ReflectionException $exception) {

        }
        return $ret;
    }

    /**
     * Will return TRUE only if the class has the $name property and it is static.
     * @param string $name
     * @return bool
     */
    public function hasStaticProperty(string $name) : bool
    {
        $static_properties = $this->getStaticProperties();
        $ret = array_key_exists($name, $static_properties);
        return $ret;
    }

    public function hasOwnDynamicProperty(string $name) : bool
    {
        $ret = FALSE;
        if ($this->hasProperty($name)) {
            $RProperty = $this->getProperty($name);
            if (!$RProperty->isStatic() && $RProperty->class === $this->name) {
                $ret = TRUE;
            }
        }
        return $ret;
    }

    public function hasOwnStaticProperty(string $name) : bool
    {
        $ret = FALSE;
        if ($this->hasProperty($name)) {
            $RProperty = $this->getProperty($name);
            if ($RProperty->isStatic() && $RProperty->class === $this->name) {
                $ret = TRUE;
            }
        }
        return $ret;
    }

    public function hasOwnConstant(string $name) : bool
    {
        $ret = FALSE;
        if ($this->hasConstant($name)) {
            $RClassConstant = new \ReflectionClassConstant($this->name, $name);
            if ($RClassConstant->class === $this->name) {
                $ret = TRUE;
            }
        }
        return $ret;
    }
}