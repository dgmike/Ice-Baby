<?php
include_once 'config.php';

$suite = new TestSuite('Todos os arquivos de teste');

foreach (glob('*.test.php') as $item) {
    $suite->addTestFile(dirname(__FILE__).DIRECTORY_SEPARATOR.$item);
}

$reporter = $_SERVER['argv'] ? 'TextReporter' : 'HtmlReporter';

$suite->run(new $reporter);

