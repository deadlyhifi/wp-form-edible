<?php

namespace FormEdible\Services;

class SanitizeFields
{
    protected $post;

    protected $submit;

    public function __construct($post, $submit)
    {
        $this->post = $post;
        $this->submit = $submit;
    }

    public function run()
    {
        unset($this->post[$this->submit]); // remove submit button from post
        array_walk_recursive($this->post, [$this, 'sanitize']);

        return $this->post;
    }

    /**
     * Strip anything disingenuous
     * @param  string &$item
     * @param  string $key
     * @return void
     */
    protected function sanitize(&$item, $key)
    {
        $item = trim($item);
        $item = wp_strip_all_tags($item);
    }
}
