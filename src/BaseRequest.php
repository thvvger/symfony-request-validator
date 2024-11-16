<?php

namespace Thvvger\RequestValidator;

use Jawira\CaseConverter\Convert;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class BaseRequest
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

        $json = [];

        try {
            $json = $request->toArray();
        } catch (\Exception $exception) {
            // Ignorer l'exception si le contenu n'est pas JSON
        }

        $data = array_merge($json, $request->request->all(), $request->files->all());

        $this->populateData($data, new \ReflectionClass($this));
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
            if (property_exists($this, $property)) {
                $reflectionProperty = $reflection->getProperty($property);
                $type = $reflectionProperty->getType();

                if ($type) {
                    $expectedType = $type->getName();

                    // Vérifie si le type de la valeur correspond au type attendu
                    if (
                        ($expectedType === \Symfony\Component\HttpFoundation\File\UploadedFile::class && !$value instanceof \Symfony\Component\HttpFoundation\File\UploadedFile) ||
                        ($expectedType !== \Symfony\Component\HttpFoundation\File\UploadedFile::class && gettype($value) !== strtolower($expectedType))
                    ) {
                        continue; // Ignore l'affectation si le type ne correspond pas
                    }
                }

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
        foreach ($violations as $violation)
            $errors[$violation->getPropertyPath()] = $violation->getMessage();

        $response = new JsonResponse([
            'message' => 'Erreur de validation',
            'errors' => $errors
        ], Response::HTTP_BAD_REQUEST);

        $response->headers->set('Access-Control-Allow-Origin', '*');
        $response->headers->set('Access-Control-Allow-Methods', 'GET, POST, PUT, DELETE, OPTIONS');
        $response->headers->set('Access-Control-Allow-Headers', 'Content-Type, Authorization');

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
