<?php

namespace Jtn\EmailDomain;

use Illuminate\Contracts\Validation\Rule;

class EmailDomain implements Rule
{

    /**
     * @var string
     */
    protected $domain;

    /**
     * @var bool
     */
    protected $strict = true;

    public function __construct($domain)
    {
        $this->domain = $domain;
        return $this;
    }

    /**
     * @return $this
     */
    public function nonStrict()
    {
        $this->strict = false;
        return $this;
    }

    /**
     * @return $this
     */
    public function strict()
    {
        $this->strict = true;
        return $this;
    }

    /**
     * Determine if the validation rule passes.
     *
     * @param  string $attribute
     * @param  mixed $value
     * @return bool
     */
    public function passes($attribute, $value)
    {
        if(is_array($this->domain)) {
            foreach($this->domain as $domain) {
                if($this->check($value, $domain)) return true;
            }
        } else {
            return $this->check($value, $this->domain);
        }

        return false;
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message()
    {
        if(is_array($this->domain)) {
            return 'The email must be one of ' . implode(', ', $this->domain);
        }

        return 'The email must match ' . $this->domain;
    }

    /**
     * @param $value
     * @param $validDomain
     * @return bool
     */
    protected function check($value, $validDomain)
    {
        $parts = explode('@', $value);
        $domain = array_last($parts);

        if(str_contains($validDomain, '*')) {
            return $this->wildcardPasses($domain, $validDomain);
        }

        return $domain === $validDomain;
    }

    /**
     * @param $value
     * @param $validDomain
     * @return int
     */
    protected function wildcardPasses($value, $validDomain)
    {
        $domain = str_replace('.', '\.', $validDomain);

        $pattern = $this->strict ? '[^\.\n\r]+' : '.*';
        $regex = str_replace('*', $pattern, $domain);

        $regex = '/^' . $regex . '$/';
        return preg_match($regex, $value) === 1;
    }

}
