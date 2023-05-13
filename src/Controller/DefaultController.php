<?php

namespace App\Controller;

use App\Entity\Changelog;
use App\Entity\ProfessionalExperience;
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
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

class DefaultController extends AbstractController
{
    #[Route('/', name: 'app_index')]
    public function index(EntityManagerInterface $entityManager): Response
    {
        $projects = $entityManager->getRepository(Project::class)->findFourLastProjects();
        $professionalExperiences = $entityManager->getRepository(ProfessionalExperience::class)->findFourLastProfessionalExperience();

        return $this->render('index.html.twig', [
            'projects' => $projects,
            'professionalExperiences' => $professionalExperiences,
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

    #[Route('/professionalexperience', name: 'app_professional_experiences')]
    public function professionalExperience(EntityManagerInterface $entityManager): Response
    {
        $professionalExperiences = $entityManager->getRepository(ProfessionalExperience::class)->findAll();
        return $this->render('professionalExperiences.html.twig', [
            'professionalExperiences' => $professionalExperiences,
        ]);
    }
    #[Route('/changelog', name: 'app_changelog')]
    public function changelog(EntityManagerInterface $entityManager): Response
    {
        $changelogs = $entityManager->getRepository(Changelog::class)->findBy([], ['releasedAt' => 'DESC']);
        return $this->render('changelog.html.twig', [
            'changelogs' => $changelogs,
        ]);
    }

    #[Route('/sitemap.xml', name: 'app_sitemap')]
    public function sitemap(UrlGeneratorInterface $urlGenerator): Response
    {
        $urls = [];
        $urls[] = ['loc' => $urlGenerator->generate('app_index', [], UrlGeneratorInterface::ABSOLUTE_URL), 'changefreq' => 'weekly', 'priority' => '1.0'];
        $urls[] = ['loc' => $urlGenerator->generate('app_projects', [], UrlGeneratorInterface::ABSOLUTE_URL), 'changefreq' => 'weekly', 'priority' => '0.8'];
        $urls[] = ['loc' => $urlGenerator->generate('app_professional_experiences', [], UrlGeneratorInterface::ABSOLUTE_URL), 'changefreq' => 'weekly', 'priority' => '0.8'];
        $urls[] = ['loc' => $urlGenerator->generate('app_changelog', [], UrlGeneratorInterface::ABSOLUTE_URL), 'changefreq' => 'weekly', 'priority' => '0.8'];
        $urls[] = ['loc' => $urlGenerator->generate('app_contact', [], UrlGeneratorInterface::ABSOLUTE_URL), 'changefreq' => 'weekly', 'priority' => '0.8'];

        $response = new Response(
            $this->renderView('sitemap.xml.twig', [
                'urls' => $urls,
            ]),
            200
        );

        $response->headers->set('Content-Type', 'text/xml');

        return $response;
    }
}
