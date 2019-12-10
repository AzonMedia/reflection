<?php
declare(strict_types=1);


namespace Azonmedia\Reflection;


use Azonmedia\Reflection\Traits\ReflectionParameterSignature;

class ReflectionParameter extends \ReflectionParameter
{
    use ReflectionParameterSignature;
}