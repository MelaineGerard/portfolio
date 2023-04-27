<?php

namespace App\Controller\Admin;

use App\Entity\Project;
use App\Form\ProjectType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ProjectController extends AbstractController
{
    #[Route('/admin/project', name: 'app_admin_project')]
    public function index(EntityManagerInterface $entityManager): Response
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        $projects = $entityManager->getRepository(Project::class)->findAll();
        return $this->render('admin/project/index.html.twig', [
            'projects' => $projects
        ]);
    }

    #[Route('/admin/project/create', name: 'app_admin_project_create')]
    #[Route('/admin/project/edit/{id}', name: 'app_admin_project_edit')]
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
            return $this->redirectToRoute('app_admin_project');
        }

        return $this->render('admin/project/create.html.twig', [
            'form' => $form,
            'project' => $project
        ]);
    }

    #[Route('/admin/project/delete/{id}', name: 'app_admin_project_delete')]
    public function delete(Project $project, EntityManagerInterface $entityManager): Response
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        $entityManager->remove($project);
        $entityManager->flush();
        return $this->redirectToRoute('app_admin_project');
    }
}
