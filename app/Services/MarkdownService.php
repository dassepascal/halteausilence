<?php

namespace App\Services;

use League\CommonMark\CommonMarkConverter;
use League\CommonMark\Environment\Environment;
use League\CommonMark\Extension\CommonMark\CommonMarkCoreExtension;
use League\CommonMark\MarkdownConverter;


$config = [
    'html_input' => 'strip',
    'allow_unsafe_links' => false,
];

$environment = new Environment($config);
$environment->addExtension(new CommonMarkCoreExtension());

$converter = new MarkdownConverter($environment);

class MarkdownService
{
    protected $converter;

    public function __construct()
    {
        $this->converter = new CommonMarkConverter();
    }

    public function parse($content)
    {
        return $this->converter->convert($content)->getContent();
    }
}
