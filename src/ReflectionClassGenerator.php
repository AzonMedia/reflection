<?php
declare(strict_types=1);


namespace Azonmedia\Reflection;

/**
 * Class ReflectionClassGenerator
 * Allows for changing the class
 * @package Azonmedia\Reflection
 */
class ReflectionClassGenerator extends ReflectionClass
{

    protected $gen_name;

    protected $gen_doc_comment;

    /**
     * @param string $doc_comment
     */
    public function setDocComment(string $doc_comment) : void
    {
        $this->gen_doc_comment = $doc_comment;
    }

    public function getDocComment()
    {
        $ret = parent::getDocComment();
        if ($this->gen_doc_comment) {
            $ret = $this->gen_doc_comment;
        }
        return $ret;
    }

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
        $ret = parent::getShortName();
        if ($this->gen_name) {
            $ret = substr($this->gen_name, strrpos($this->gen_name,'\\') + 1);
        }
        return $ret;
    }

    public function getName() : string
    {
        $ret = parent::getName();
        if ($this->gen_name) {
            $ret = $this->gen_name;
        }
        return $ret;
    }

    public function getNamespaceName() : string
    {
        $ret = parent::getNamespaceName();
        if ($this->gen_name) {
            $ret = substr($this->gen_name, 0 , strrpos($this->gen_name,'\\'));
        }
        return $ret;
    }

}