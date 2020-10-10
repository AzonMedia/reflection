<?php
declare(strict_types=1);


namespace Azonmedia\Reflection;

use Azonmedia\Reflection\Traits\ReflectionClassStructure;

/**
 * Class ReflectionClass
 * Contains additional methods to the ones provided in \ReflectionClass
 * @package Reflection
 *
 */
class ReflectionClass extends \ReflectionClass
{

    use ReflectionClassStructure;

    /**
     * Returns an array of all parent classes
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
            if ($Rmethod->name === $method && $Rmethod->getDeclaringClass()->name  === $this->name) {
                $ret = TRUE;
            }
        } catch (\ReflectionException $exception) {
            //$ret remains FALSE
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

    /**
     * Checks does this class has own (not from a parent class) dynamic property
     * @param string $name
     * @return bool
     * @throws \ReflectionException
     */
    public function hasOwnDynamicProperty(string $name) : bool
    {
        $ret = FALSE;
        if ($this->hasProperty($name)) {
            $RProperty = $this->getProperty($name);
            if (!$RProperty->isStatic() && $RProperty->getDeclaringClass()->name  === $this->name) {
                $ret = TRUE;
            }
        }
        return $ret;
    }

    /**
     * Returns an array of ReflectionProperty of the dynamic properties that are defined in this class only excluding the ones defined in the parent.
     * @return \ReflectionProperty[]
     */
    public function getOwnDynamicProperties(?int $filter = NULL) : array
    {
        $ret = [];
        $properties = $this->getProperties($filter);
        foreach ($properties as $RProperty) {
            if (!$RProperty->isStatic() && $RProperty->getDeclaringClass()->name  === $this->name) {
                $ret[] = $RProperty;
            }
        }
        return $ret;
    }

    /**
     * Returns all dynamic properties as per $filter from this class and its parents until reaching a parent class with name $until_parent_class_name (does not include properties from this class)
     * @param int|null $filter
     * @param string $until_parent_class_name
     * @return array
     */
    public function getDynamicPropertiesUpToParentClass(?int $filter = null, string $until_parent_class_name = ''): array
    {
        /** @var \ReflectionProperty[] $ret */
        $ret = [];
        $class = $this->getName();
        do {
            $RClass = new static($class);
            //$ret = [...$ret, ...$RClass->getOwnDynamicProperties($filter)];//if there are overriden these must be skipped (for example the defaultvalue may be different)
            foreach ($RClass->getOwnDynamicProperties($filter) as $RProperty) {
                foreach ($ret as $ExistingRProperty) {
                    if ($RProperty->getName() === $ExistingRProperty->getName()) {
                        continue 2;
                    }
                }
                $ret[] = $RProperty;
            }
            $RParentClass = $RClass->getParentClass();
            if ($RParentClass) {
                $class = $RParentClass->getName();
            } else {
                $class = null;
            }
            if ($class === $until_parent_class_name) {
                break;
            }
        } while ($class);
        return $ret;
    }

    /**
     * Returns an array of ReflectionProperty of the static properties that are defined in this class only excluding the ones defined in the parent.
     * @return array
     */
    public function getOwnStaticProperties() : array
    {
        $ret = [];
        $properties = $this->getProperties();
        foreach ($properties as $RProperty) {
            if ($RProperty->isStatic() && $RProperty->getDeclaringClass()->name  === $this->name) {
                $ret[] = $RProperty;
            }
        }
        return $ret;
    }

    /**
     * Checks does this class has own (not from a parent class) static property
     * @param string $name
     * @return bool
     * @throws \ReflectionException
     */
    public function hasOwnStaticProperty(string $name) : bool
    {
        $ret = FALSE;
        if ($this->hasProperty($name)) {
            $RProperty = $this->getProperty($name);
            if ($RProperty->isStatic() && $RProperty->getDeclaringClass()->name  === $this->name) {
                $ret = TRUE;
            }
        }
        return $ret;
    }

    /**
     * Checks does this class has own (not from a parent class) constant
     * @param string $name
     * @return bool
     * @throws \ReflectionException
     */
    public function hasOwnConstant(string $name) : bool
    {
        $ret = FALSE;
        if ($this->hasConstant($name)) {
            $RClassConstant = new \ReflectionClassConstant($this->name, $name);
            if ($RClassConstant->getDeclaringClass()->name === $this->name) {
                $ret = TRUE;
            }
        }
        return $ret;
    }

    /**
     * Returns an array with ReflectionMethod that are declared in this class.
     * @param int|null $filter
     * @return ReflectionMethod[]
     */
    public function getOwnMethods(?int $filter = NULL) : array
    {
        $ret = [];
        $methods = $this->getMethods($filter);
        foreach ($methods as $RMethod) {
            if ($RMethod->getDeclaringClass()->name === $this->name) {
                $ret[] = $RMethod;
            }
        }
        return $ret;
    }

    /**
     * Returns only the interfaces implemented by this class, excluding the interfaces implemented by the parent class
     * or any interfaces extended by the already implemented interfaces by this class.
     * Returns and indexed array of strings (interface names).
     * @return array
     */
    public function getOwnInterfaceNames() : array
    {
        $ret = [];
        $interfaces_names = $this->getInterfaceNames();
        $RParentClass = $this->getParentClass();
        if ($RParentClass) {
            $parent_class_interfaces_names = $RParentClass->getInterfaceNames();
            $interfaces_names = array_diff($interfaces_names, $parent_class_interfaces_names);
        }
        //then make sure the implemented interfaces are not due to an interface implementing another one
        $parent_interfaces_names = [];
        foreach ($interfaces_names as $interfaces_name) {
            $RInterface = new $this($interfaces_name);
            $parent_interfaces_names = array_merge($parent_interfaces_names, $RInterface->getInterfaceNames());
        }
        return array_diff($interfaces_names, $parent_interfaces_names);
    }

    /**
     * Returns only the interfaces implemented by this class, excluding the interfaces implemented by the parent class
     * or any interfaces extended by the already implemented interfaces by this class.
     * Returns and indexed array of ReflectionClass
     * @return array
     */
    public function getOwnInterfaces() : array
    {
        $ret = [];
        foreach ($this->getOwnInterfaceNames() as $interface_name) {
            $ret[] = new $this($interface_name);
        }
        return $ret;
    }

    /**
     * Returns an array of strings containing class constants that match the provided value
     * @param $constant_value
     * @return array
     */
    public function getClassConstantsByValue(/* mixed */ $constant_value ) : array
    {
        $ret = [];
        $constants = self::getConstants();
        foreach ($constants as $const_name=>$const_value) {
            if ($constant_value === $const_value) {
                $ret[] = $const_name;
            }
        }
        return $ret;
    }

    public function getReverseConstantsList() : array
    {
        $ret = [];//contains a revers array of constant value => constant name, duplicates are skipped (meaning two constants with the same value)
        foreach (self::getConstants() as $const_name=>$const_value) {
            if (!array_key_exists($const_value, $ret)) {
                $ret[$const_value] = $const_name;
            }
        }
    }
}