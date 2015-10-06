<?php

namespace FormEdible;

class Form
{
    /**
     * View: check if form is successfully sent
     * @return bool true
     */
    public static function success()
    {
        if (isset($_GET["success"])) {
            return true;
        }
    }

    /**
     * Get submitted form value to refill field
     * @param  string $name name of field
     * @return string       value of field
     */
    public static function value($name)
    {
        if (isset($_SESSION['form_values'][$name])) {
            return $_SESSION['form_values'][$name];
        }
    }

    /**
     * Get error message for failed form fields
     * @param  string $name    name of form field
     * @param  array  $message associative array of messages for requirements
     * @param  string $before  to place before error message
     * @param  string $after   to place after error message
     * @return string          the full message
     */
    public static function error($name, array $message, $before = null, $after = null)
    {
        // No error messages for these
        if (! isset($_SESSION['form_errors'][$name]) ||
            isset($_SESSION['form_errors'][$name]['text']) ||
            isset($_SESSION['form_errors'][$name]['honeypot'])
        ) {
            return;
        }

        // Required
        if (isset($_SESSION['form_errors'][$name]['required'])) {
            return $before . $message['required'] . $after;
        }

        // URL
        if (isset($_SESSION['form_errors'][$name]['url'])) {
            return $before . $message['url'] . $after;
        }

        // Email
        if (isset($_SESSION['form_errors'][$name]['email'])) {
            return $before . $message['email'] . $after;
        }

        // Int
        if (isset($_SESSION['form_errors'][$name]['int'])) {
            return $before . $message['int'] . $after;
        }
    }
}
