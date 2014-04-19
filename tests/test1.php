<?php

include "../src/Antonraharja/Tpl.php";

$tpl = new Antonraharja\Tpl;

$tpl->name = 'test1';

$tpl->vars = array(
	'title' => 'This is test 1',
	'content' => 'This is sample content',
);

$tpl->ifs = array(
	'valid' => TRUE,
	'something' => FALSE,
);

$tpl->apply();

echo "<p>Original content:</p>\n";
echo $tpl->getContent();

echo "<br /><br />\n\n";

echo "<p>Manipulated content:</p>\n";
echo $tpl->getResult();

echo "<br /><br />\n\n";

echo "<p>Compiled content:</p>\n";
echo $tpl->getCompiled();
