<?php

use PHPUnit\Framework\TestCase;

class InputValidatorTest extends TestCase
{
    private $validator;

    protected function setUp(): void
    {
        $this->validator = new InputValidator();
    }

    public function testSanitizeEncodesHtml()
    {
        $input = "<script>Hello</script>";
        // htmlspecialchars encodes tags
        $expected = "&lt;script&gt;Hello&lt;/script&gt;";
        $this->assertEquals($expected, $this->validator->sanitize($input));
    }

    public function testSanitizeTrimsWhitespace()
    {
        $input = "  Hello World  ";
        $expected = "Hello World";
        $this->assertEquals($expected, $this->validator->sanitize($input));
    }

    public function testIsValidEmail()
    {
        $this->assertTrue($this->validator->isValidEmail('test@example.com'));
        $this->assertFalse($this->validator->isValidEmail('invalid-email'));
    }

    public function testValidateRequired()
    {
        $data = ['username' => 'test', 'password' => ''];
        $required = ['username', 'password'];
        $errors = $this->validator->validateRequired($data, $required);
        
        $this->assertArrayNotHasKey('username', $errors);
        $this->assertArrayHasKey('password', $errors);
        $this->assertEquals('Password is required', $errors['password']);
    }
}
