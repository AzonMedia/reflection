<?php
declare(strict_types=1);

namespace Azonmedia\Reflection\Traits;

use Azonmedia\Reflection\Reflection;
use Azonmedia\Reflection\ReflectionMethod;

trait ReflectionClassStructure
{
    /**
     * Returns the full structure of the class as string.
     * @return string
     */
    public function getClassStructure(bool $with_generated_doc_block = FALSE) : string
    {
        $ret = '<?php'.PHP_EOL.PHP_EOL;
declare(strict_types=1);

        $namespace = $this->getNamespaceName();
        if ($namespace) {
            $ret .= 'namespace '.$namespace.';'.PHP_EOL.PHP_EOL;
        }

        if ($with_generated_doc_block) {
            $ret .= $this->getOrGenerateDocComment().PHP_EOL;
        } else {
            $ret .= $this->getDocComment().PHP_EOL;
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
            $ret .= ' use '.implode(', ',$traits).';'.PHP_EOL;
            $ret .= PHP_EOL;
        }


        $ret .= $this->getPropertiesDeclaration(0, $with_generated_doc_block);

        $ret .= PHP_EOL;

        $ret .= $this->getConstantsDeclaration($with_generated_doc_block);

        $ret .= PHP_EOL;

        $ret .= $this->getMethodsDeclaration($with_generated_doc_block);

        $ret .= '}'.PHP_EOL;

        return $ret;
    }

    /**
     * Returns a multiline string with all the properties declarations.
     * @param int $filter
     * @return string
     */
    public function getPropertiesDeclaration(int $filter = 0, bool $with_generated_doc_block = FALSE) : string
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

                $prop_value = $default_properties[$RProperty->name];

                $doc_comment = $RProperty->getDocComment();

                if (!$doc_comment && $with_generated_doc_block) {
                    $prop_type = '';
                    if ($prop_value) {
                        $prop_type = gettype($prop_value);
                    }
                    $doc_comment = <<<COMMENT
/**
 * @var $prop_type
 */
COMMENT;
                }
                $ret .= $doc_comment.PHP_EOL;

                $ret .= implode(' ',$modifiers).' $'.$RProperty->name;
                if ($prop_value) {
                    $ret .= ' = '.var_export($prop_value, TRUE);
                }
                $ret .= ';'.PHP_EOL.PHP_EOL;
            }

        }
        $ret = Reflection::indent($ret);
        return $ret;
    }

    /**
     * Returns a multiline string with all constants declarations.
     * @return string
     */
    public function getConstantsDeclaration(bool $with_generated_doc_block = FALSE) : string
    {
        $ret = '';
        $constants = $this->getConstants();
        foreach ($this->getReflectionConstants() as $RConstant) {
            if ($RConstant->class === $this->name) { //is it defined in this class or is coming form the parent

                $doc_comment = $RConstant->getDocComment();

                if (!$doc_comment && $with_generated_doc_block) {
                    $const_type = gettype($constants[$RConstant->name]);
                    $doc_comment = <<<COMMENT
/**
 * @var $const_type
 */
COMMENT;
                }
                $ret .= $doc_comment.PHP_EOL;

                $modifiers = \Reflection::getModifierNames($RConstant->getModifiers());
                $ret .= implode(' ',$modifiers).' const '.$RConstant->name;
                $ret .= ' = '.var_export($constants[$RConstant->name], TRUE);
                $ret .= ';'.PHP_EOL.PHP_EOL;
            }
        }
        $ret = Reflection::indent($ret);
        return $ret;
    }

    /**
     * Returns a multiline string with all methods signatures.
     * @return string
     * @throws \ReflectionException
     */
    public function getMethodsDeclaration(bool $with_generated_doc_block = FALSE) : string
    {

        $ret = '';
        foreach ($this->getMethods() as $RMethod) {
            if ($RMethod->class === $this->name) { //is it defined in this class or is coming form the parent
                $RMethod = new ReflectionMethod($this->name, $RMethod->name);
                $ret .= $RMethod->getSignature($with_generated_doc_block).PHP_EOL;
            }
        }

        return $ret;
    }
    
    public function getOrGenerateDocComment() : string
    {
        $ret = $this->getDocComment();
        if (!$ret) {
            $class_name = $this->getShortName();
            $class_ns = $this->getNamespaceName();
            $ret = <<<COMMENT
/**
 * Class $class_name
 * @package $class_ns
 */
COMMENT;

        }
        return $ret;
    }

}