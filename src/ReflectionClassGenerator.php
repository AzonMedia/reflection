<?php


namespace Azonmedia\Reflection;

/**
 * Class ReflectionClassGenerator
 * Allows for changing the class
 * @package Azonmedia\Reflection
 */
class ReflectionClassGenerator extends ReflectionClass
{

    protected $gen_name;

    /**
     * Allows for the class name change.
     * @param string $name
     */
    public function setClassName(string $name) : void
    {
        //since the $this->name property is a read only and can not be changed even with reflectio na new property needs to be used
        //TODO add validation
        $this->gen_name = $name;

    }

    public function getShortName() : string
    {
        if ($this->gen_name) {
            return substr($this->gen_name, strrpos($this->gen_name,'\\') + 1);
        } else {
            return parent::getShortName();
        }
    }

    public function getName() : string
    {
        if ($this->gen_name) {
            return $this->gen_name;
        } else {
            return parent::getName();
        }
    }

    public function getNamespaceName() : string
    {
        if ($this->gen_name) {
            return substr($this->gen_name, 0 , strrpos($this->gen_name,'\\'));
        } else {
            return parent::getNamespaceName();
        }
    }

}