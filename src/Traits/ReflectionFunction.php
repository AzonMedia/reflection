<?php
declare(strict_types=1);

namespace Azonmedia\Reflection\Traits;

trait ReflectionFunction
{

    /**
     * Returns an associative array of the arguments of a method.
     * @param array $args as provided by func_get_args()
     * @return array
     * @throws \InvalidArgumentException
     */
    public function getArgumentsAsArray(array $args): array
    {
        $ret = [];
        /** @var \ReflectionParameter[] $parameters */
        $parameters = $this->getParameters();
        if (count($args) !== count($parameters)) {
            throw new \InvalidArgumentException(sprintf('The provided number of arguments is %1$s while the expected number is %2f.', count($args), count($parameters) ));
        }
        $aa = 0;
        foreach ($args as $arg) {
            //TODO add a type check too!
            //if ($RType = $parameters[$aa]->getType()) {
            //}
            $ret[$parameters[$aa]->getName()] = $arg;
            $aa++;
        }
        print_r($ret);
        return $ret;
    }
}