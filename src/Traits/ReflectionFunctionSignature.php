<?php
declare(strict_types=1);


namespace Azonmedia\Reflection\Traits;


use Azonmedia\Reflection\Reflection;
use Azonmedia\Reflection\ReflectionParameter;

trait ReflectionFunctionSignature
{


    /**
     * Returns the argument list as a string comprised of the signatures for each parameter.
     * The returned string is like "int $param1, string $param2 = 'asd'"
     * @return string
     */
    public function getParametersList() : string
    {
        $ret = '';
        $param_arr = [];
        foreach ($this->getParameters() as $RParam) {
            try {
                if ($this instanceof \ReflectionMethod) {
                    $RParam = new ReflectionParameter([$this->getDeclaringClass()->getName(), $this->getName()], $RParam->getName());
                } else {
                    $RParam = new ReflectionParameter($this->getName(), $RParam->getName());
                }
            } catch (\ReflectionException $Exception) {
                //it may happen the function to be an alias to another one and ->name and ->getName() to return wrong name
                print get_class($Exception).': '.$Exception->getMessage().' in '.$Exception->getFile().'#'.$Exception->getLine().PHP_EOL;
                continue;
            }
            
            $param_arr[] = $RParam->getSignature();
        }

        return implode(', ', $param_arr);
    }

    /**
     * Returns the full method/function signature.
     * @param bool $with_generated_doc_block
     * @param bool $no_doc_block
     * @return string
     * @throws \ReflectionException
     */
    public function getSignature(bool $with_generated_doc_block = FALSE, bool $no_doc_block = FALSE) : string
    {
        if ($this instanceof \ReflectionMethod) {
            $modifiers = \Reflection::getModifierNames($this->getModifiers());
        } else {
            $modifiers = '';
        }

        $ret = '';
        if (!$no_doc_block) {
            $doc_comment = $this->getDocComment();
            if (!$doc_comment && $with_generated_doc_block) {

                $args_doc_arr = [];
                foreach ($this->getParameters() as $RParam) {
                    if ($this instanceof \ReflectionMethod) {
                        $RParam = new ReflectionParameter([$this->getDeclaringClass()->getName(), $this->getName()], $RParam->getName());
                    } else {
                        $RParam = new ReflectionParameter($this->getName(), $RParam->getName());
                    }
                    $args_doc_arr[] = $RParam->generateDocComment();
                }
                $args_doc_str = implode(PHP_EOL.' * ', $args_doc_arr);
                $ret_doc_str = '';
                if ($RType = $this->getReturnType()) {
                    $rtype_string = (string) $RType;
                    if (in_array($rtype_string, ['self', 'parent', 'static'])) {
                        $ret_doc_str .= '@return '.($RType->allowsNull() ? 'null|' : '').($RType->isBuiltin() ? '' : '').$RType;
                    } else {
                        $ret_doc_str .= '@return '.($RType->allowsNull() ? 'null|' : '').($RType->isBuiltin() ? '' : '\\').$RType;
                    }
                } else {
                    $ret_doc_str .= '@return void';
                }
                $doc_comment = <<<COMMENT
/**
 * $args_doc_str
 * $ret_doc_str
 */
COMMENT;
            }
            $ret .= $doc_comment.PHP_EOL;
        }

        if ($modifiers) {
            $ret .= implode(' ', $modifiers).' function ';
        } else {
            $ret .= 'function ';
        }

        if ($this->returnsReference()) {
            $ret .= '&';
        }
        $ret .= $this->name.'( '.$this->getParametersList().')';

        if ($RType = $this->getReturnType()) {
            //$ret .= ' : '.($RType->allowsNull() ? '?' : '').($RType->isBuiltin() ? '' : '\\').$RType;
            $ret .= ' : '.($RType->allowsNull() ? '?' : '').($RType->isBuiltin() ? '' : '\\').$RType->getName();
        }

        $ret .= ' { }'.PHP_EOL;

        if ($this instanceof \ReflectionMethod) {
            $ret = Reflection::indent($ret);
        }

        return $ret;
    }
}