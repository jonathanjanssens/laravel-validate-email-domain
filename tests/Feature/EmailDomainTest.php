<?php

namespace Jtn\EmailDomain\Tests\Feature;

use PHPUnit\Framework\TestCase;
use Jtn\EmailDomain\EmailDomain;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Translation\FileLoader;
use Illuminate\Translation\Translator;
use Illuminate\Validation\Factory as Validator;

class EmailDomainTest extends TestCase
{

    public function getValidator($email, $domain)
    {
        $translator = new Translator(new FileLoader(new Filesystem(), null), 'en');
        $validator = (new Validator($translator))->make(['email' => $email], [
            'email'  => ['required', new EmailDomain($domain)],
        ]);

        return $validator;
    }

    public function test_simple_domain_validation()
    {
        $this->assertTrue(
            $this->getValidator('test@example.com', 'example.com')->passes()
        );

        $this->assertTrue(
            $this->getValidator('test@example.net', 'example.com')->fails()
        );
    }

    public function test_wildcard_domains_match()
    {
        $this->assertTrue(
            $this->getValidator('test@test.example.com', '*.example.com')->passes()
        );
    }

    public function test_wildcard_domains_failures()
    {
        $this->assertTrue(
            $this->getValidator('test@test.test.example.com', '*.example.com')->fails()
        );

        $this->assertTrue(
            $this->getValidator('test@mail.example.com', '*.example2.com')->fails()
        );

        $this->assertTrue(
            $this->getValidator('test@mail.example.com', 'example.com')->fails()
        );

    }

    public function test_validate_array_method()
    {
        $this->assertTrue(
            $this->getValidator('test@example.com', ['example.com', 'example.org'])->passes()
        );

        $this->assertTrue(
            $this->getValidator('test@mail.example.com', ['example.com', 'example.org', '*.example.com'])->passes()
        );

        $this->assertTrue(
            $this->getValidator('test@example.net', ['example.com', 'example.org'])->fails()
        );
    }

}
