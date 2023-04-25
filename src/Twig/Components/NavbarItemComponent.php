<?php

namespace App\Twig\Components;

use Symfony\UX\TwigComponent\Attribute\AsTwigComponent;

#[AsTwigComponent('navbar_item')]
final class NavbarItemComponent
{
    public string $navItemLink;
    public string $navItemText;


    public function __construct()
    {
    }
}
