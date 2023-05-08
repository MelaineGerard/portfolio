<?php

namespace App\Controller\Admin;

use App\Entity\Project;
use App\Form\ProjectType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/admin/project', name: 'app_admin_project_')]
class ProjectController extends AbstractController
{
    #[Route('/', name: 'index')]
    public function index(EntityManagerInterface $entityManager): Response
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        $projects = $entityManager->getRepository(Project::class)->findAll();
        return $this->render('admin/project/index.html.twig', [
            'projects' => $projects
        ]);
    }

    #[Route('/create', name: 'create')]
    #[Route('/edit/{id}', name: 'edit')]
    public function create(?Project $project, Request $request, EntityManagerInterface $entityManager): Response
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        if (!$project instanceof Project) {
            $project = new Project();
        }

        $form = $this->createForm(ProjectType::class, $project);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $project = $form->getData();
            $entityManager->persist($project);
            $entityManager->flush();
            return $this->redirectToRoute('app_admin_project_index');
        }

        return $this->render('admin/project/create.html.twig', [
            'form' => $form,
            'project' => $project
        ]);
    }

    #[Route('/delete/{id}', name: 'delete')]
    public function delete(Project $project, EntityManagerInterface $entityManager): Response
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        $entityManager->remove($project);
        $entityManager->flush();
        return $this->redirectToRoute('app_admin_project_index');
    }
}
