<?php

include "../src/Playsms/Tpl.php";

$all_fruits = 'Apple, Banana and Orange';

$tpl = new Playsms\Tpl;

$tpl->vars = array(
	'title' => 'This is test 3',
	'content' => 'This is sample content',
);

$tpl->injects = array('all_fruits');

$tpl->setTemplate('./templates/test4.html');

$tpl->compile();

echo $tpl->getCompiled();
