<?php

namespace App\Controller\Admin;

use App\Entity\ProfessionalExperience;
use App\Entity\Project;
use App\Form\ProfessionalExperienceType;
use App\Form\ProjectType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/admin/professionalexperience', name: 'app_admin_professional_experience_')]
class ProfessionalExperienceController extends AbstractController
{
    #[Route('/', name: 'index')]
    public function index(EntityManagerInterface $entityManager): Response
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');

        $professionalExperiences = $entityManager->getRepository(ProfessionalExperience::class)->findAll();
        return $this->render('admin/professional_experience/index.html.twig', [
            'professionalExperiences' => $professionalExperiences
        ]);
    }

    #[Route('/create', name: 'create')]
    #[Route('/edit/{id}', name: 'edit')]
    public function create(?ProfessionalExperience $professionalExperience, Request $request, EntityManagerInterface $entityManager): Response
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        if (!$professionalExperience instanceof ProfessionalExperience) {
            $professionalExperience = new ProfessionalExperience();
        }

        $form = $this->createForm(ProfessionalExperienceType::class, $professionalExperience);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $professionalExperience = $form->getData();
            $entityManager->persist($professionalExperience);
            $entityManager->flush();
            return $this->redirectToRoute('app_admin_professional_experience_index');
        }

        return $this->render('admin/professional_experience/create.html.twig', [
            'form' => $form,
            'project' => $professionalExperience
        ]);
    }

    #[Route('/delete/{id}', name: 'delete')]
    public function delete(ProfessionalExperience $professionalExperience, EntityManagerInterface $entityManager): Response
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        $entityManager->remove($professionalExperience);
        $entityManager->flush();
        return $this->redirectToRoute('app_admin_professional_experience_index');
    }
}
