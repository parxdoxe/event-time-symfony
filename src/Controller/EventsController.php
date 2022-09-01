<?php

namespace App\Controller;

use App\Entity\Event;
use App\Form\EventType;
use App\Repository\EventRepository;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\String\Slugger\SluggerInterface;

class EventsController extends AbstractController
{
    #[Route('/', name: 'home')]
    public function home(): Response
    {
        return $this->render('home.html.twig');
    }

    #[Route('/events', name: 'app_events')]
    public function index(ManagerRegistry $doctrine, EventRepository $repository): Response
    {
        $doctrine ->getRepository(Event::class)->findAll();
        $events = $repository->findAll();

        return $this->render('events/index.html.twig', [
            'events' => $events,
        ]);
    }
    #[Route('/events/create', name: 'app_events_new')]
    public function create(Request $request, EntityManagerInterface $manager, SluggerInterface $slugger): Response
    {
        $event = new Event();
        $form = $this->createForm(EventType::class, $event);
        $form->handleRequest($request);


        if ($form->isSubmitted() && $form->isValid()) {
            
            $image= $form->get('image')->getData();

            if ($image) {
                $originalFilename = pathinfo($image->getClientOriginalName(), PATHINFO_FILENAME);
                
                $safeFilename = $slugger->slug($originalFilename);
                $newFilename = $safeFilename.'-'.uniqid().'.'.$image->guessExtension();
                try {
                    $image->move(
                        $this->getParameter('images_directory'),
                        $newFilename
                    );
                } catch (FileException $e) {
                    $e;
                }
                $event->setImage($newFilename);
            }
            $manager->persist($event);
            $manager->flush();

            $this->addFlash('success', $event->getName().' a été crée.');

            return $this->redirectToRoute('app_events');
        }

        return $this->render('events/create.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    #[Route('/events/{id}', name: 'app_events_show')]
    public function show(Event $event): Response
    {
        return $this->render('events/show.html.twig', [
            'event' => $event,
        ]);
    }
}
