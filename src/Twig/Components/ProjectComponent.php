<?php

namespace App\Twig\Components;

use Symfony\UX\TwigComponent\Attribute\AsTwigComponent;

#[AsTwigComponent('project')]
final class ProjectComponent
{
    public string $projectName;
    public string $projectDescription;
    public string $projectLink;
}
