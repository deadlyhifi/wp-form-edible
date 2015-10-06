<?php

namespace FormEdible;

use FormEdible\Services\SanitizeFields;
use FormEdible\Services\ValidateFields;
use FormEdible\Services\Emailer;

class Setup
{
    /**
     * Slug of page form is on
     * @var string
     */
    protected $page;

    /**
     * Name of submit button
     * @var string
     */
    protected $submit;

    /**
     * Array of form fields and their requirements
     * @var array
     */
    protected $fields;

    /**
     * Header and ting
     * @var array
     */
    protected $mail;

    /**
     * Store manipulated $_POST
     * @var array
     */
    protected $post;

    /**
     * Form errors
     * @var array
     */
    protected $errors = array();

    public function __construct($page)
    {
        $this->page = $page;

        add_action('wp', [$this, 'run']);
    }

    /**
     * Set the submit button name
     * @param string $name
     */
    public function setSubmit($name)
    {
        $this->submit = $name;
    }

    /**
     * Set form fields and requirements
     * @param array $fields
     */
    public function setFields(array $fields)
    {
        $this->fields = $fields;
    }

    public function setMail(array $mail)
    {
        $this->mail = $mail;
    }

    /**
     * Make it rain, if we're on the right page and the form has been submitted
     * @return void
     */
    public function run()
    {
        if (! is_page($this->page) || ! isset($_POST[$this->submit])) {
            return;
        }

        $this->cleanup();
        $this->validate();
        $this->sessions();
        $this->sendEmail();
        $this->redirect();
    }

    /**
     * Cleanup the submitted $_POST and assign to $post
     * @return void
     */
    protected function cleanup()
    {
        $cleaner = new SanitizeFields($_POST, $this->submit);
        $this->post = $cleaner->run();
    }

    /**
     * Have we got what we need?
     * @return void
     */
    protected function validate()
    {
        $validate = new ValidateFields($this->fields, $this->post);
        $this->errors = $validate->validate();
    }

    /**
     * Write it all to sessions so WP can have a sniff
     * @return void
     */
    protected function sessions()
    {
        $_SESSION['form_values'] = $this->post;
        $_SESSION['form_errors'] = $this->errors;
    }

    /**
     * Someone should know about this
     * @return void
     */
    protected function sendEmail()
    {
        if (! empty($this->errors) || empty($this->mail)) {
            return;
        }

        $mailer = new Emailer($this->mail, $this->post);
        $mailer->run();
    }

    /**
     * If the form passes redirect the user with a GET of success
     * @return boolean
     */
    protected function redirect()
    {
        // Passed validation?
        if (empty($this->errors)) {
            wp_redirect(get_permalink(url_to_postid($this->page)) . '?success');

            exit;
        }
    }
}
