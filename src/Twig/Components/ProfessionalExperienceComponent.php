<?php

namespace App\Twig\Components;

use Symfony\UX\TwigComponent\Attribute\AsTwigComponent;

#[AsTwigComponent('professional_experience')]
final class ProfessionalExperienceComponent
{
    public string $companyName;
    public string $jobType;
    public string $description;
    public string $startDate;
    public string $endDate;
    public string $companyWebsite;

    public function __construct()
    {
    }
}
