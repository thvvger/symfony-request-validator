<?php

namespace Thvvger\RequestValidator\FormRequest;

use Jawira\CaseConverter\Convert;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class FileRequestValidator
{
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
        $request = $this->getRequest();
        $reflection = new \ReflectionClass($this);

        // Fonction générique pour peupler les données
        $this->populateData($request->request->all(), $reflection);
        $this->populateData($request->files->all(), $reflection);
    }

    /**
     * Fonction pour peupler les propriétés de la classe avec les données de la requête
     *
     * @param array $data
     * @param \ReflectionClass $reflection
     */
    protected function populateData(array $data, \ReflectionClass $reflection): void
    {
        foreach ($data as $property => $value) {
            $attribute = self::camelCase($property);
            if (property_exists($this, $attribute)) {
                $reflectionProperty = $reflection->getProperty($attribute);
                $reflectionProperty->setValue($this, $value);
            }
        }
    }

    protected function validate(): void
    {
        $violations = $this->validator->validate($this);

        if (count($violations) < 1) {
            return;
        }

        $errors = [];
        foreach ($violations as $violation) {
            $attribute = self::snakeCase($violation->getPropertyPath());
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

    private static function camelCase(string $attribute): string
    {
        return (new Convert($attribute))->toCamel();
    }

    private static function snakeCase(string $attribute): string
    {
        return (new Convert($attribute))->toSnake();
    }
}
