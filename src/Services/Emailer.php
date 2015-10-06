<?php

namespace FormEdible\Services;

class Emailer
{
    protected $mail;

    protected $post;

    public function __construct($mail, $post)
    {
        $this->mail = $mail;
        $this->post = $post;
    }

    public function run()
    {
        $this->headers();
        $this->message();
        $this->send();
    }

    protected function headers()
    {
        if ($this->mail['html'] == true) {
            $this->mail['headers'][] = 'Content-Type: text/html; charset=UTF-8';
        }

        // From - check for field name
        if (isset($this->post[$this->mail['from']['name']])) {
            $from = 'From: ' . $this->post[$this->mail['from']['name']];
        } else {
            $from = 'From: ' . $this->mail['from']['name'];
        }

        if (isset($this->post[$this->mail['from']['email']])) {
            $from .= ' <' . $this->post[$this->mail['from']['email']] . '>';
        } else {
            $from .= ' <' . $this->mail['from']['email'] . '>';
        }
        $this->mail['headers'][] = $from;

        // Reply-to
        if (isset($this->post[$this->mail['reply-to']['name']])) {
            $replyto = 'Reply-To: ' . $this->post[$this->mail['reply-to']['name']];
        } else {
            $replyto = 'Reply-To: ' . $this->mail['reply-to']['name'];
        }

        if (isset($this->post[$this->mail['reply-to']['email']])) {
            $replyto .= ' <' . $this->post[$this->mail['reply-to']['email']] . '>';
        } else {
            $replyto = ' <' . $this->mail['reply-to']['email'] . '>';
        }
        $this->mail['headers'][] = $replyto;

        // Cc
        if (isset($this->mail['cc'])) {
            foreach ($this->mail['cc'] as $cc) {
                $this->mail['headers'][] = 'Cc: ' . $cc;
            }
        }

        // Bcc
        if (isset($this->mail['bcc'])) {
            foreach ($this->mail['bcc'] as $bcc) {
                $this->mail['headers'][] = 'Bcc: ' . $bcc;
            }
        }
    }

    protected function message()
    {
        $message = '';

        foreach ($this->post as $k => $v) {
            if ($v === '') {
                continue;
            }

            if (isset($this->mail['html']) && $this->mail['html'] == true) {
                $message .= '<strong>' . $k . '</strong>'
                    . '<br>'
                    . $v
                    . '<br><br>';
            } else {
                $message .= $k
                    . '\n'
                    . $v
                    . '\n\n';
            }
        }

        $this->mail['message'] = $message;
    }

    protected function send()
    {
        return wp_mail(
            $this->mail['to'],
            $this->mail['subject'],
            $this->mail['message'],
            $this->mail['headers']
            // $this->attachments
        );
    }
}
