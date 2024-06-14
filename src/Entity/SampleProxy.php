<?php

namespace App\Entity;

use Symfony\Component\VarExporter\Internal\LazyObjectRegistry;

/**
 * @author Kevin Bond <kevinbond@gmail.com>
 */
final class SampleProxy extends Sample
{
    public function __construct(private parent $_instance)
    {
        // the following is basically "unset()" for all properties (including private ones)
        foreach (LazyObjectRegistry::$classResetters[parent::class] ??= LazyObjectRegistry::getClassResetters(parent::class) as $reset) {
            $reset($this, []);
        }

        // now, __get will be called whenever a property is accessed
    }

    public function __get(string $name): mixed
    {
        // in foundry, refresh __instance

        return self::_property(new \ReflectionClass(parent::class), $name)->getValue($this->_instance);
    }

    private static function _property(\ReflectionClass $class, string $name): \ReflectionProperty
    {
        try {
            return $class->getProperty($name);
        } catch (\ReflectionException $e) {
            if (!$class = $class->getParentClass()) {
                throw $e;
            }

            return self::_property($class, $name);
        }
    }
}
