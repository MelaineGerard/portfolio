<?php

namespace App\Twig\Components;

use App\Entity\Changelog;
use Symfony\UX\TwigComponent\Attribute\AsTwigComponent;

#[AsTwigComponent('changelog')]
final class ChangelogComponent
{
    public Changelog $changelog;

    public function __construct()
    {
    }
}
