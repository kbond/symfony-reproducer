<?php

namespace Zenstruck\FormRequest\HttpFoundation;

use Symfony\Component\HttpFoundation\Session\SessionFactoryInterface;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Zenstruck\FormRequest\FormState\SessionFormState;

/**
 * @author Kevin Bond <kevinbond@gmail.com>
 */
final class FormSessionFactory implements SessionFactoryInterface
{
    public function __construct(private SessionFactoryInterface $inner)
    {
    }

    public function createSession(): SessionInterface
    {
        $session = $this->inner->createSession();
        $session->registerBag(SessionFormState::createSessionBag());

        return $session;
    }
}
