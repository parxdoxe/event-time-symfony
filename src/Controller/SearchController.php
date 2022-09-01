<?php

namespace App\Controller;

use App\Entity\Event;
use App\Repository\EventRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class SearchController extends AbstractController
{
    #[Route('/search', name: 'search')]
    public function index(Request $request, EventRepository $repository): Response
    {
        $query = $request->request->all('form')['query'];
        if ($query) {
            $events = $repository->findEventByName($query);
        }
        return $this->render('search/index.html.twig', [
            'events' => $events,
        ]);
    }

    public function searchBar(): Response
    {
        $form = $this->createFormBuilder()
            ->setAction($this->generateUrl('search'))
            ->add('query', TextType::class, [
                'label' => false,
                'attr' => [
                    'class' => 'border-2 border-gray-300 bg-white h-10 px-5 pr-16 rounded-lg text-sm focus:outline-none',
                    'placeholder' => 'Entrez un mot clé'
                ]
            ])
            ->add('recherche', SubmitType::class, [
                'attr' => [
                    'class' => 'text-center p-2 bg-[#22c55e] rounded-lg'
                ]
            ])
            ->getForm();
        return $this->render('search/searchBar.html.twig', [
            'form' => $form->createView(),
        ]);
    }


}
