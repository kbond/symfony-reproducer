<?php

namespace App;

use Doctrine\Persistence\ObjectManager;
use Zenstruck\Foundry\Factory;
use Zenstruck\Foundry\RepositoryProxy;

/**
 * @author Kevin Bond <kevinbond@gmail.com>
 */
trait IsProxy
{
    public function _save(): static
    {
        $om = self::_objectManager();
        $om->persist($this->initializeLazyObject());
        $om->flush();

        return $this;
    }

    public function _refresh(): static
    {
        $om = self::_objectManager();

        if ($om->contains($this->initializeLazyObject())) {
            $om->refresh($this->lazyObjectReal);

            return $this;
        }

        $id = $om->getClassMetadata(parent::class)->getIdentifierValues($this->lazyObjectReal);

        if (!$id || !$object = $om->find(parent::class, $id)) {
            throw new \RuntimeException('object no longer exists...');
        }

        $this->lazyObjectReal = $object;

        return $this;
    }

    public function _delete(): static
    {
        $om = self::_objectManager();
        $om->remove($this->initializeLazyObject());
        $om->flush();

        return $this;
    }

    public function _repo(): RepositoryProxy
    {
        return Factory::configuration()->repositoryFor(parent::class);
    }

    private static function _objectManager(): ObjectManager
    {
        return Factory::configuration()->objectManagerFor(parent::class);
    }
}
