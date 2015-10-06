<?php

namespace FormEdible\Services;

class ValidateFields
{
    protected $fields;

    protected $post;

    public function __construct($fields, $post)
    {
        $this->fields = $fields;
        $this->post = $post;
    }

    /**
     * Validate each form field based on its requirements
     * @return void
     */
    public function validate()
    {
        $errors = null;

        foreach ($this->post as $k => $v) {
            // Required
            if (! empty($this->fields[$k]['required'])) {
                if ($v === '') {
                    $errors[$k]['required'] = true;
                }
            }

            // Type honeypot|text|url|email|int
            switch ($this->fields[$k]['type']) {
                case 'honeypot':
                    if ($v !== '') {
                        $errors[$k]['honeypot'] = true;
                    }
                    break;
                case 'url':
                    if (! filter_var($v, FILTER_VALIDATE_URL)) {
                        $errors[$k]['url'] = true;
                    }
                    break;
                case 'email':
                    if (! filter_var($v, FILTER_VALIDATE_EMAIL)) {
                        $errors[$k]['email'] = true;
                    }
                    break;
                case 'int':
                    if (! is_numeric($v)) {
                        $errors[$k]['int'] = true;
                    }
                    break;
            }
        }

        return $errors;
    }
}
