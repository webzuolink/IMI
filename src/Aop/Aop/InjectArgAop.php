<?php
namespace Imi\Aop\Aop;

use Imi\Aop\PointCutType;
use Imi\Aop\AroundJoinPoint;
use Imi\Aop\Annotation\Around;
use Imi\Aop\Annotation\Aspect;
use Imi\Aop\Annotation\PointCut;
use Imi\Aop\Annotation\InjectArg;
use Imi\Bean\Annotation\AnnotationManager;
use Imi\Util\ClassObject;

/**
 * @Aspect(PHP_INT_MAX)
 */
class InjectArgAop
{
    /**
     * 自动事务支持
     * @PointCut(
     *         type=PointCutType::ANNOTATION,
     *         allow={
     *             InjectArg::class
     *         }
     * )
     * @Around
     * @return mixed
     */
    public function parse(AroundJoinPoint $joinPoint)
    {
        $class = get_parent_class($joinPoint->getTarget());
        $injectArgs = AnnotationManager::getMethodAnnotations($class, $joinPoint->getMethod(), InjectArg::class);
        $args = ClassObject::convertArgsToKV($class, $joinPoint->getMethod(), $joinPoint->getArgs());

        foreach($injectArgs as $injectArg)
        {
            $args[$injectArg->name] = $injectArg->value;
        }

        $args = array_values($args);

        return $joinPoint->proceed($args);
    }
}
