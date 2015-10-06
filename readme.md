# FormEdible
Sometimes you want a contact form in WordPress that the client doesn't edit, it's just part of the site you deliver. This allows you to define a form, various field requirements, specific error messages, and all right in your theme. No plugins here.

## Field Types
* `honeypot` – A honeypot is a spam trap, i.e. the value must be blank.
* `text` – does no validation.
* `email` – validates for an email address.
* `url` - validates a URL.

## Examples

### Define the Form
Include this in your `functions.php` file. Don’t forget to include `vendor/autoload.php`.

```php
$form = new FormEdible\Setup('contact-us'); // pass in page name
$form->setSubmit('submit-form'); // set submit button name
$form->setFields([
    'spam' => [
        'type' => 'honeypot',
    ],
    'fullname' => [ // corresponds to form field name.
        'type' => 'text', // type of field, a textarea is just text, this is just to state the type of validation.
        'required' => true, // state if the field it required.
    ],
    'email' => [
        'type' => 'email',
        'required' => true,
    ],
    'website' => [
        'type' => 'url',
        'required' => true,
    ],
    'message' => [
        'type' => 'text',
        'required' => true,
    ],
]);

// State where the
$form->setMail([
    'to' => 'tom@deadlyhifi.com', // (required) string or array
    'subject' => 'Test Form', // (required)
    'from' => ['name' => 'fullname', 'email' => 'email'], // (optional) may end up in spam as you're spoofing the from field, leave it out to send from wordpress@yoursite.com.
    'reply-to' => ['name' => 'fullname', 'email' => 'email'], // (optional)checks field names and populates if they exists, else it'll use the string you passed in.
    'cc' => ['another@example.com', 'andanother@example.com'], // (optional)
    'bcc' => ['another@example.com', 'andanother@example.com'], // (optional)
    'html' => true, // (optional) if you want HTML email to be sent out.
]);
```

### Put the Form Into Your Theme

You now have access to:
* `FormEdible\Form::value('website');`
  * where the paramater is the field name. Use this to echo the value back in if validation failed.
* `FormEdible\Form::error('website', ['required' => 'A URL is required', 'url' => 'This must be a valid URL'], '<div class="c-form--error">', '</div>');`
  * Again, the field name is passed in, followed by an array of the error messages, their keys being the name of the error type.
  * Followed by before and after strings (to wrap error message in html).

```php
if (FormEdible\Form::success()) {
    echo "<h1>Successfully Submitted</h1>";
} else {
?>
<form method="POST" action="">
  <input type="hidden" name="spam" value="<?php echo FormEdible\Form::value('spam'); ?>">

  <input type="text" name="fullname" placeholder="Your name" value="<?php echo FormEdible\Form::value('fullname'); ?>">
  <?php echo FormEdible\Form::error('fullname', ['required' => 'Your name is required'], '<div class="c-form--error">', '</div>'); ?>

  <input type="text" name="email" placeholder="Email Address" value="<?php echo FormEdible\Form::value('email'); ?>">
  <?php echo FormEdible\Form::error('email', ['required' => 'Your email address is required', 'email' => 'This must be a valid email address'], '<div class="c-form--error">', '</div>'); ?>

  <input type="text" name="website" placeholder="Your Website" value="<?php echo FormEdible\Form::value('website'); ?>">
  <?php echo FormEdible\Form::error('website', ['required' => 'A URL is required', 'url' => 'This must be a valid URL'], '<div class="c-form--error">', '</div>'); ?>

  <textarea name="message" placeholder="Enter a message"><?php echo FormEdible\Form::value('message'); ?></textarea>
  <?php echo FormEdible\Form::error('message', ['required' => 'A message is required'], '<div class="c-form--error">', '</div>'); ?>

  <input type="submit" name="submit-form" value="Submit">
</form>
<?php } ?>
```

A successful form submition redirects to the current page appended by `?success` which is checked with `FormEdible\Form::success()`.

## Be Aware – Reserved Form Field Names
WordPress has a load of reserved field names, and if you're scratching your head as to why you get a 404 when submitting a form it's likely because you named one of your form elements as such. `name` is a common one.

`w`, `name`, `date`, `year`, `month`, `day`, `hour`, `minute`.
beware, there may be more.

* [contactform7 FAQ](http://contactform7.com/faq/my-contact-form-always-redirects-to-404-error-page-after-submission/)
* [Ninnypants/Certain Form Elements Cause 404](https://ninnypants.com/blog/2011/02/07/certain-form-element-names-cause-404-in-wordpress/)

## Releases
* 0.1 – 06/10/15

## Todo:
* set success redirect URL
* minlength validation
* maxlength validation
* int - minvalue validation
* int - maxvalue validation
* File uploads?
* Save data to database
* Admin page to show respondents
* Some kind of captcha?
* AJAX?
