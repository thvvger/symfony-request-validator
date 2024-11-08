<?php


namespace Thvvger\RequestValidator;

use Symfony\Component\HttpKernel\Bundle\AbstractBundle;

class RequestValidatorBundle extends AbstractBundle
{
    public function getPath(): string
    {
        return __DIR__;
    }
}