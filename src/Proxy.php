<?php

namespace App;

use Zenstruck\Foundry\RepositoryProxy;

/**
 * @author Kevin Bond <kevinbond@gmail.com>
 */
interface Proxy
{
    public function _save(): static;

    public function _refresh(): static;

    public function _delete(): static;

    public function _repo(): RepositoryProxy;
}
