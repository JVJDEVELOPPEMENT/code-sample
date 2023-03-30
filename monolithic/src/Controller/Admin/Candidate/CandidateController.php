<?php

declare(strict_types=1);

namespace App\Controller\Admin\Candidate;

use App\Entity\Candidate;
use App\Entity\User;
use App\Form\Candidate\CandidateType;
use App\Repository\CandidateRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/admin/candidate', name: 'admin_candidate_')]
class CandidateController extends AbstractController
{
    public function __construct(private CandidateRepository $candidateRepository,
                                private EntityManagerInterface $em){}

    #[Route('/', name: 'index')]
    public function index(): Response
    {
        $candidates = $this->candidateRepository->findAll();

        return $this->render("admin/candidate/index.html.twig",[
            "candidates" => $candidates
        ]);
    }

    #[Route('/add', name: 'add')]
    public function add(Request $request): Response
    {
        /** @var User $user */
        $user = $this->getUser();

        $candidate = new Candidate();

        $candidate->setCreatedBy($user);

        $form = $this->createForm(CandidateType::class, $candidate);

        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid())
        {
            $this->em->persist($candidate);

            $this->em->flush();

            $this->addFlash("success","Un nouveau candidat a bien été ajouté.");

            return $this->redirectToRoute("admin_candidate_profile",["id" => $candidate->getId()]);
        }

        return $this->render("admin/candidate/add.html.twig",[
            "form" => $form->createView()
        ]);
    }

    #[Route('/edit/{id}', name: 'edit')]
    public function edit(Request $request, Candidate $candidate): Response
    {
        /** @var User $user */
        $user = $this->getUser();

        $form = $this->createForm(CandidateType::class, $candidate);

        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid())
        {
            $candidate->setUpdatedBy($user);

            $this->em->persist($candidate);

            $this->em->flush();

            $this->addFlash("success","Les coordonnées du candidat ont bien été modifiées.");

            return $this->redirectToRoute("admin_candidate_profile",["id" => $candidate->getId()]);
        }

        return $this->render("admin/candidate/edit.html.twig",[
            "candidate" => $candidate,
            "form" => $form->createView()
        ]);
    }

    #[Route('/profile/{id}', name: 'profile')]
    public function profile(Candidate $candidate): Response
    {
        $candidatures = $candidate->getCandidatures();

        return $this->render("admin/candidate/profile/profile.html.twig",[
            "candidate" => $candidate,
            "candidatures" => $candidatures
        ]);
    }
}