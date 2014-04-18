<?php

include "../src/Antonraharja/Tpl.php";

$tpl = new Antonraharja\Tpl;

$tpl->name = 'test1';

$tpl->vars = array(
	'title' => 'This is test 1',
	'content' => 'This is sample content',
);

$tpl->apply();

echo "<p>Original content:\n";
echo $tpl->getContent();

echo "<br />\n";

echo "<p>Manipulated content:\n";
echo $tpl->getResult();

echo "<br />\n";

echo "<p>Compiled content:\n";
echo $tpl->getCompiled();
