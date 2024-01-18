<?php

namespace App\FormRequest;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Csrf\CsrfToken;
use Symfony\Component\Security\Csrf\CsrfTokenManagerInterface;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintViolationInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

/**
 * @author Kevin Bond <kevinbond@gmail.com>
 *
 * @phpstan-type Constraints = null|Constraint|Constraint[]|class-string<Constraint>|class-string<Constraint>[]
 */
final class Validator
{
    private const CSRF_TOKEN_ID = 'form';
    private const CSRF_TOKEN_FIELD = '_token';

    public function __construct(
        private Request $request,
        private ValidatorInterface $validator,
        private ?CsrfTokenManagerInterface $csrfTokenManager,
    ) {
    }

    /**
     * @param array<string,Constraints> $data
     */
    public function validate(array $data): Form
    {
        $csrfEnabled = null !== $this->csrfTokenManager;
        $form = new Form(csrfTokenId: $csrfEnabled ? self::CSRF_TOKEN_ID : null);

        if ($this->request->isMethodCacheable()) {
            // not submitted so return empty form
            return $form;
        }

        $decoded = [...$this->request->getPayload()->all(), ...$this->request->files->all()];

        // "null trim" values
        \array_walk_recursive($decoded, static function(&$value): void {
            if (!\is_string($value)) {
                return;
            }

            $value = \trim($value);

            if ('' === $value) {
                $value = null;
            }
        });

        foreach (\array_keys($data) as $field) {
            $value = $decoded[$field] ?? null;

            $form->set($field, $value);

            if (null === $constraints = $data[$field]) {
                // empty rule is just "allowed"
                continue;
            }

            if (\is_string($constraints)) {
                $constraints = [$constraints];
            }

            $constraints = \array_map(static function(string|Constraint $constraint): Constraint {
                if (\is_string($constraint)) {
                    return new $constraint();
                }

                return $constraint;
            }, $constraints);

            foreach ($this->validator->validate($value, $constraints) as $violation) {
                /** @var ConstraintViolationInterface $violation */
                $form->addError($field, $violation->getMessage());
            }
        }

        if (!$csrfEnabled) {
            return $form;
        }

        $token = $this->request->request->get(self::CSRF_TOKEN_FIELD);

        if (!$this->csrfTokenManager) {
            throw new \LogicException('CSRF not enabled in your application.');
        }

        if (!$this->csrfTokenManager->isTokenValid(new CsrfToken(self::CSRF_TOKEN_ID, $token))) {
            $form->addGlobalError('The CSRF token is invalid. Please try to resubmit the form.');
        }

        return $form;
    }
}
