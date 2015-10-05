# alerts
Alerts is a PHP library for managing and rendering alerts (such as error and success messages) within an application.  It does not enforce a particular output format (instead a Renderer interface is provided) and it provides some useful methods for easily filtering those alerts.

## Synopsis

Alerts is a simple interface for adding and outputting alerts within an application. For example, this could be alerts displayed to a user when submitting a form, or to a console application.

Although it has built in convenience methods for errors, warnings, notices and successes, it does not try to make too many assumptions and allows the user to associate additional data with messages that can be used for filtering and retrieval.

Alerts allows specifying a RendererInterface so that the output can easily be tailored to the format needed by the front-end.  For example Using the supplied Text renderer will render alerts as plain text, while another renderer might output some form of HTML.



At the top of the file there should be a short introduction and/ or overview that explains **what** the project is. This description should match descriptions added for package managers (Gemspec, package.json, etc.)

## Examples

### Basic Example

```php
use Alerts\Alerts;

// Create new alerts instance and optionally specify the default renderer
$alerts = new Alerts(new \Alerts\Renderer\Text);

// Add an error and a notice to the alerts
$alerts->error("Oh no an error occurred");
$alerts->notice("FYI - You probably shouldn't do that again...");

// Output the alerts (using the default renderer specified in the constructor)
echo $alerts->render();
```

### Different types of alerts

```php
// Add a success
$alerts->success('Successful message');

// Add a notice
$alerts->notice("This is a notice");

// Add a warning
$alerts->warning("This is a warning");

// Add an error
$alerts->error("Error message");

// Add an alert without a type
$alerts->add('This is just a generic alert with no type associated');
```

Those alerts can then be accessed via the following convenience methods which all return an `Alerts\MessageCollection` object:

```php
// Get all alerts
$messages = $alerts->all();

// Get only the success messages
$messages = $alerts->successes();

// Get only notices
$messages = $alerts->notices();

// Get all warnings
$messages = $alerts->warnings();

// Get all errors
$messages = $alerts->errors();
```

### Iterating alerts

The MessageCollection returned implements the `ArrayIterator` interface so it's simple to iterate through messages:

```php
foreach ($alerts->errors() as $message) {
    // $message is an Alerts\Message object
    echo $message->getMessage();  // Return the string message
}
```

### Changing the renderer

Although you can easily implement your own renderer by implementing the `Alerts\Renderer\RendererInterface`, there are some basic renderers provided; Bootstrap, SweetAlerts, Text.

The renderer can be specified in the constructor, by calling `setRenderer` or by passing it to the `render` method:

```php
// Initialise without a default renderer
$alerts = new Alerts();
$alerts->success("User has been updated!");

 // Output Bootstrap style HTML
echo $alerts->render(new \Alerts\Renderer\Bootstrap); 

 // Output as text
echo $alerts->render(new \Alerts\Renderer\Text);  // Output as text

// Change the default render to SweetAlerts (JS library)
$alerts->setRenderer(new \Alerts\Renderer\SweetAlerts); 
echo $alerts;  // the overloaded _toString() method automatically calls render()
```

### Adding additional data to each alerts.

Internally each alert is stored as an `Alerts\Message` object.  This object includes a `data` property which can be used to associate an array of data with an alert.  This can be used for anything such as categorising or tagging fields.

```php
// Add a success message and associate some data with it.
$alerts->success('Your form was submitted successfully!', [ 'email' => 'walter@example.com ]);

// Get the first success message and pull 
$message = $alerts->successes()->get(0);
echo $message->getMessage()."\n";
echo "Your email is: ".$message->get('email')."\n";
```

The data is simply an array which can be used to store arbitrary values which you need to retrieve later.  It can also be used to filter messages.

```php
$alerts->error("Standard error message");
$alerts->error("Please check the value you entered", [ 'overlay' => true ]);

// Get only errors that have overlay => true specified in their data
$messages = $alerts->errors([ 'overlay' => true ]);

// Render those alerts via the SweetAlerts renderer
$messages->render(\Alerts\Renderer\SweetAlerts);
```


## Installation

Use composer to include this library within your project:
```
composer require i4ucode\alerts
```

## API Reference

TODO

## Tests

More tests come soon.

## Contributors

Feel free to submit a pull-request.

## License

MIT License
