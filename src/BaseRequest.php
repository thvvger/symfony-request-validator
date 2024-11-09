<?php

namespace Thvvger\RequestValidator;

use Jawira\CaseConverter\Convert;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Validator\ConstraintViolationList;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class BaseRequest
{
    protected array $errors = [];

    public function __construct(
        protected ValidatorInterface $validator,
        protected RequestStack $requestStack,
    ) {
        $this->populate();
        $this->validate();
    }

    public function getRequest(): Request
    {
        return $this->requestStack->getCurrentRequest();
    }

    protected function populate(): void
    {
        $requestData = $this->getRequest()->toArray();
        foreach ($requestData as $property => $value) {
            $attribute = $this->camelCase($property);
            if (property_exists($this, $attribute)) {
                $this->{$attribute} = $value;
            }
        }
    }

    protected function validate(): void
    {
        $violations = $this->validator->validate($this);
        if (count($violations) > 0) {
            $this->handleValidationErrors($violations);
        }
    }

    protected function handleValidationErrors(ConstraintViolationList $violations): void
    {
        $errors = [];
        foreach ($violations as $violation) {
            $attribute = $this->snakeCase($violation->getPropertyPath());
            $errors[] = [
                'property' => $attribute,
                'value' => $violation->getInvalidValue(),
                'message' => $violation->getMessage(),
            ];
        }

        $response = new JsonResponse([
            'message' => $violations[0]->getPropertyPath()  . ' ' . $violations[0]->getMessage(),
            'errors' => $errors
        ], Response::HTTP_BAD_REQUEST);

        $response->send();

        exit;
    }

    private function camelCase(string $attribute): string
    {
        return (new Convert($attribute))->toCamel();
    }

    private function snakeCase(string $attribute): string
    {
        return (new Convert($attribute))->toSnake();
    }
}
