<?php
declare(strict_types=1);


namespace Azonmedia\Reflection;


use Azonmedia\Reflection\Traits\ReflectionFunctionSignature;

class ReflectionFunction extends \ReflectionFunction
{
    use ReflectionFunctionSignature;
}