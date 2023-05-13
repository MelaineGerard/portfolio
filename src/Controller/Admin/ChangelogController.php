<?php

namespace App\Controller\Admin;

use App\Entity\Changelog;
use App\Form\ChangelogType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
#[Route('/admin/changelog', name: 'app_admin_changelog_')]
class ChangelogController extends AbstractController
{
    #[Route('/', name: 'index')]
    public function index(EntityManagerInterface $entityManager): Response
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');

        $changelogs = $entityManager->getRepository(Changelog::class)->findAll();
        return $this->render('admin/changelog/index.html.twig', [
            'changelogs' => $changelogs
        ]);
    }

    #[Route('/create', name: 'create')]
    #[Route('/edit/{id}', name: 'edit')]
    public function create(?Changelog $changelog, Request $request, EntityManagerInterface $entityManager): Response
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        if (!$changelog instanceof Changelog) {
            $changelog = new Changelog();
        }

        $form = $this->createForm(ChangelogType::class, $changelog);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $changelog = $form->getData();
            $entityManager->persist($changelog);
            $entityManager->flush();
            return $this->redirectToRoute('app_admin_changelog_index');
        }

        return $this->render('admin/changelog/create.html.twig', [
            'form' => $form,
            'changelog' => $changelog
        ]);
    }

    #[Route('/delete/{id}', name: 'delete')]
    public function delete(Changelog $changelog, EntityManagerInterface $entityManager): Response
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        $entityManager->remove($changelog);
        $entityManager->flush();
        return $this->redirectToRoute('app_admin_changelog_index');
    }
}
