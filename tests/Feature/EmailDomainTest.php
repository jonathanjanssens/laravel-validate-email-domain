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

    public function getValidator($email, $domain, $strict = true)
    {
        $translator = new Translator(new FileLoader(new Filesystem(), null), 'en');
        $validator = (new Validator($translator))->make(['email' => $email], [
            'email'  => ['required', $this->getRule($domain, $strict)],
        ]);

        return $validator;
    }

    protected function getRule($domain, $strict)
    {
        if($strict) {
            return new EmailDomain($domain);
        }

        $rule = (new EmailDomain($domain))->nonStrict();
        return $rule;
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

    public function test_strict_mode()
    {
        $val = $this->getValidator('test@a.b.c.d.e.f.example.com', '*.example.com', false);
        $this->assertTrue($val->passes());

        $val = $this->getValidator('test@example.com', '*.example.com', false);
        $this->assertTrue($val->fails());

        $val = $this->getValidator('test@example.com', ['*.example.com', 'example.com'], false);
        $this->assertTrue($val->passes());
    }

}
