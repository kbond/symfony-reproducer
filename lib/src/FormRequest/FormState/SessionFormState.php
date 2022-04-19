<?php

namespace Zenstruck\FormRequest\FormState;

use Symfony\Component\HttpFoundation\Session\Flash\AutoExpireFlashBag;
use Zenstruck\FormRequest\FormState;

/**
 * @author Kevin Bond <kevinbond@gmail.com>
 *
 * @internal
 */
final class SessionFormState extends FormState
{
    private const BAG_NAME = 'form_request';

    public function data(): array
    {
        // TODO: Implement data() method.
    }

    public function errors(): array
    {
        // TODO: Implement errors() method.
    }

    /**
     * @internal
     */
    public static function createSessionBag(): AutoExpireFlashBag
    {
        $bag = new AutoExpireFlashBag(self::BAG_NAME);
        $bag->setName(self::BAG_NAME);

        return $bag;
    }
}
