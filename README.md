# Laravel Email Domain Validation

Validation rule for Laravel 5.5 to validate that a given email
address belongs to the provided domain.

Wildcard domains and multiple domains are supported.

## Basic Usage

If your class implements the Laravel `ValidatesRequests` trait
you can validate a simple domain as follows.

```
use Jtn\EmailDomain;

$this->validate(request()->all(), [
    'email' => ['email', new EmailDomain('example.com')]
])
```

This validation rule will only pass if the email provided 
is `@example.com`.

## Wildcard Usage

```
$this->validate(request()->all(), [
    'email' => ['email', new EmailDomain('*.example.com')]
])
```

This rule wil match any of `mail.example.com`,
`test.example.com`, etc. To match `mail.test.example.com` the
rule must be `new EmailDomain('*.*.example.com')`.

## Match Multiple Domains

To match multiple domains simply pass an array of accepted 
domains to the constructor. You can pass any number of domains
and wildcards as an array to check them all.

```
$this->validate(request()->all(), [
    'email' => [
        'email',
        new EmailDomain(['example.org', 'example.com'])
    ]
])
```
