<?php

namespace App\Twig;

use App\Entity\LikeNotification;
use Twig\Extension\AbstractExtension;
use Twig\Extension\GlobalsInterface;
use Twig\TwigFilter;

class TwigExtension extends AbstractExtension implements GlobalsInterface
{
    private $hello_message;

    public function __construct(string $hello_message)
    {
        $this->hello_message = $hello_message;
    }

    public function getFilters()
    {
        return [
            new TwigFilter('price', [$this, 'priceFilter'])
        ];
    }

    public function getGlobals()
    {
        return [
            'hello_message' => $this->hello_message
        ];
    }

    public function priceFilter($number)
    {
        return '$' . number_format($number, 2, '.', ',');
    }

    public function getTests()
    {
        return [
            new \Twig_SimpleTest('like', function ($obj) {
                return $obj instanceof LikeNotification;
            })
        ];
    }
}