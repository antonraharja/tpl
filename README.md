README
======

Dead simple PHP template engine

Information      | Description
---------------- | ----------------
Author           | Anton Raharja
Version          | 1.0.2
Release date     | 140421

Examples
--------

An example template file `the_page.html`:

```
<div>
	<p>This is the title: {{ title }}</p>
	<p>This is the content: {{ content }}</p>
	<p>And this is the data: {{ $data }}</p>
</div>
```

Example PHP file `show_page.php` using the template file `the_page.html`:

```
<?php

require 'vendor/autoload.php';

$data = 'THE DATA HERE';

$tpl = new Playsms\Tpl;

$tpl->setTemplate('the_page');

$tpl->setVars(array(
	'title' => 'THE TITLE HERE',
	'content' => 'THE CONTENT HERE'
	));

$tpl->setInjects(array('data'));

$tpl->compile();

echo $tpl->getCompiled();
```

After compilation the end result will be like this:
```
<div>
	<p>This is the title: THE TITLE HERE</p>
	<p>This is the content: THE CONTENT HERE</p>
	<p>And this is the data: THE DATA HERE</p>
</div>
```


For more examples please see **examples** folder.

Other documents can be found in **docs** folder.
