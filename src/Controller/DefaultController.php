<?php

namespace App\Controller;

use App\Entity\Project;
use App\Form\ContactType;
use Aws\S3\S3Client;
use Doctrine\ORM\EntityManagerInterface;
use League\Flysystem\FilesystemOperator;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Mailer\Exception\TransportExceptionInterface;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;
use Symfony\Component\Routing\Annotation\Route;

class DefaultController extends AbstractController
{
    #[Route('/', name: 'app_index')]
    public function index(EntityManagerInterface $entityManager): Response
    {
        $projects = $entityManager->getRepository(Project::class)->findFourLastProjects();
        return $this->render('index.html.twig', [
            'projects' => $projects,
        ]);
    }

    /**
     * @throws TransportExceptionInterface
     */
    #[Route('/contact', name: 'app_contact')]
    public function contact(Request $request, MailerInterface $mailer): Response
    {
        $contact = [];
        $form = $this->createForm(ContactType::class, $contact);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $contactFormData = $form->getData();

            $email = (new Email())
                ->from($contactFormData['email'])
                ->to($this->getParameter('app.contact_email'))
                ->subject('Nouvelle demande de contact')
                ->text(
                    'Envoyeur : ' . $contactFormData['prenom'] . ' ' . $contactFormData['nom'] . "\n" .
                    'Email : ' . $contactFormData['email'] . "\n" .
                    'Message : ' . $contactFormData['message']
                );

            $mailer->send($email);

            $this->addFlash('success', 'Message envoyé ! Merci de nous avoir contacté.');
            return $this->redirectToRoute('app_contact');
        }

        return $this->render('contact.html.twig', [
            'form' => $form,
            'email' => $this->getParameter('app.contact_email')
        ]);
    }

    #[Route('/projects', name: 'app_projects')]
    public function projects(EntityManagerInterface $entityManager): Response
    {
        $projects = $entityManager->getRepository(Project::class)->findAll();
        return $this->render('projects.html.twig', [
            'projects' => $projects,
        ]);
    }
}
