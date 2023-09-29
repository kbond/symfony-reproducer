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
 */
final class Validator
{
    private const DEFAULT_CSRF_TOKEN_ID = 'form';
    private const CSRF_TOKEN_FIELD = '_token';
    private const CSRF_TOKEN_HEADER = 'X-CSRF-TOKEN';

    private string $csrfTokenId = self::DEFAULT_CSRF_TOKEN_ID;
    private bool $csrfEnabled;

    public function __construct(
        private Request $request,
        private ValidatorInterface $validator,
        private ?CsrfTokenManagerInterface $csrfTokenManager,
    ) {
    }

    public function disableCsrf(): self
    {
        $clone = clone $this;
        $clone->csrfEnabled = false;

        return $clone;
    }

    public function enableCsrf(string $tokenId = self::DEFAULT_CSRF_TOKEN_ID): self
    {
        $clone = clone $this;
        $clone->csrfTokenId = $tokenId;
        $clone->csrfEnabled = true;

        return $clone;
    }

    /**
     * @param array<string,null|Constraint|Constraint[]> $data
     */
    public function validate(array $data): Form
    {
        $csrfEnabled = $this->isCsrfEnabled();
        $form = new Form(csrfTokenId: $csrfEnabled ? $this->csrfTokenId : null);

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

            foreach ($this->validator->validate($value, $constraints) as $violation) {
                /** @var ConstraintViolationInterface $violation */
                $form->addError($field, $violation->getMessage());
            }
        }

        if (!$csrfEnabled) {
            return $form;
        }

        $token = $this->request->request->get(
            self::CSRF_TOKEN_FIELD, // try _token field
            $this->request->headers->get(self::CSRF_TOKEN_HEADER) // try header
        );

        if (!$this->csrfTokenManager) {
            throw new \LogicException('CSRF not enabled in your application.');
        }

        if (!$this->csrfTokenManager->isTokenValid(new CsrfToken($this->csrfTokenId, $token))) {
            // TODO: alternate behaviour: throw TokenMismatch exception to convert to 419 in event listener
            $form->addGlobalError('The CSRF token is invalid. Please try to resubmit the form.');
        }

        return $form;
    }

    private function isCsrfEnabled(): bool
    {
        if (isset($this->csrfEnabled)) {
            return $this->csrfEnabled;
        }

        if ('html' !== $this->request->getPreferredFormat()) {
            // disable by default if no html
            return $this->csrfEnabled = false;
        }

        // enable by default if available
        return $this->csrfEnabled = null !== $this->csrfTokenManager;
    }
}
