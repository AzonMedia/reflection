<?php
declare(strict_types=1);

namespace Azonmedia\Reflection;

use Azonmedia\Reflection\Traits\ReflectionFunctionSignature;

class ReflectionMethod extends \ReflectionMethod
{
    use ReflectionFunctionSignature;
    use \Azonmedia\Reflection\Traits\ReflectionFunction;
}