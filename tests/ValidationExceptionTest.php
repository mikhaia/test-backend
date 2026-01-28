<?php

namespace Tests;

use App\Exception\ValidationException;

class ValidationExceptionTest extends TestCase
{
    public function testValidationExceptionStoresErrors()
    {
        $errors = ['Title is required', 'Description is too long'];
        $exception = new ValidationException($errors);

        $this->assertEquals($errors, $exception->getErrors());
        $this->assertEquals('Validation error', $exception->getMessage());
        $this->assertEquals(400, $exception->getCode());
    }

    public function testValidationExceptionWithEmptyErrors()
    {
        $errors = [];
        $exception = new ValidationException($errors);

        $this->assertEquals($errors, $exception->getErrors());
        $this->assertEquals('Validation error', $exception->getMessage());
        $this->assertEquals(400, $exception->getCode());
    }

    public function testValidationExceptionWithSingleError()
    {
        $errors = ['Invalid input'];
        $exception = new ValidationException($errors);

        $this->assertEquals($errors, $exception->getErrors());
        $this->assertEquals('Validation error', $exception->getMessage());
        $this->assertEquals(400, $exception->getCode());
    }
}
