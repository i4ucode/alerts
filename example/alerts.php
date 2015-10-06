<?php
require '../vendor/autoload.php';
use Alerts\Alerts;

$alerts = new Alerts(
    new \Alerts\Renderer\Text(),        // Default renderer (optional)
    new \Alerts\Storage\Session()       // Default storage (optional)
);

// Add a plain old  error
$alerts->error('You got beef?');

// Output the error (currently using the text renderer)
echo $alerts;

// Add a success message with some extra data. We can use data for filtering or to pass to the renderer
// as you can see below.
$alerts->success('Congratulations, you win!', ['overlay' => true, 'title' => 'Winner!', 'buttonText' => 'OH YEAH!']);

// You can pass any data you might need to access or filter by
$alerts->warning('Example warning', [ 'somekey' => 'anything' ]);

// error(), warning(), success() and notice() are just convenience methods that set an additional _type key in the data
// The example below is the same as calling $alerts->error()
$alerts->add('So many things going wrong today', [ '_type' => \Alerts\AlertType::ERROR ]);

// _type isn't anything special ...
$alerts->add('Oh no - not another problem?', [ '_type' => 'My Type' ]);

// Change the default renderer to Bootstrap style HTML
$alerts->setRenderer(new \Alerts\Renderer\Bootstrap());


// Return messages collection containing messages that do not contain 'overlay' => true within their data.
$messages = $alerts->filterWithout(['overlay' => true]);

// Output the messages, this is the same as calling $messages->render()
echo $messages;

// Return those messages that ARE an overlay
$messages = $alerts->filter(['overlay' => true]);

// First output them as sweet alerts
echo $messages->render(new \Alerts\Renderer\SweetAlerts);

// Then output them as text
echo $messages->render(new \Alerts\Renderer\Text);

// Get alerts as an array (eg. for encoding to JSON)
print_r($messages->toArray());

// Persist the alerts to storage
// Or simply unset($alerts) or lose scope and the destructor will call automatically if there is a storage engine
$alerts->store();
