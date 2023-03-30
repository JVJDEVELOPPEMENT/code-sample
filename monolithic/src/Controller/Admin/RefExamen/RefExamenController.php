<?php

declare(strict_types=1);

namespace App\Controller\Admin\RefExamen;

use App\Entity\RefExamen;
use App\Entity\User;
use App\Form\RefExamen\RefExamenType;
use App\Repository\RefExamenRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/admin/ref-examen', name: 'admin_ref_examen_')]
class RefExamenController extends AbstractController
{
    public function __construct(private RefExamenRepository $refExamenRepository,
                                private EntityManagerInterface $em){}

    #[Route('/', name: 'index')]
    public function index(): Response
    {
        $refExamens = $this->refExamenRepository->findAll();

        return $this->render("admin/ref_examen/index.html.twig",[
            "refExamens" => $refExamens
        ]);
    }

    #[Route('/add', name: 'add')]
    public function add(Request $request): Response
    {
        /** @var User $user */
        $user = $this->getUser();

        $refExamen = new RefExamen();

        $refExamen->setCreatedBy($user);

        $form = $this->createForm(RefExamenType::class, $refExamen);

        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid())
        {
            $this->em->persist($refExamen);

            $this->em->flush();

            $this->addFlash("success","Un nouvel examen a bien été ajouté.");

            return $this->redirectToRoute("admin_ref_examen_index",["id" => $refExamen->getId()]);
        }

        return $this->render("admin/ref_examen/add.html.twig",[
            "form" => $form->createView()
        ]);
    }

    #[Route('/edit/{id}', name: 'edit')]
    public function edit(Request $request, RefExamen $refExamen): Response
    {
        /** @var User $user */
        $user = $this->getUser();

        $form = $this->createForm(RefExamenType::class,$refExamen);

        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid())
        {
            $refExamen->setUpdatedBy($user);

            $refExamen->setUpdatedAt(new \DateTimeImmutable());

            $this->em->persist($refExamen);

            $this->em->flush();

            $this->addFlash("success","L'examen a bien été modifié.");

            return $this->redirectToRoute("admin_ref_examen_edit",["id" => $refExamen->getId()]);
        }

        return $this->render("admin/ref_examen/edit.html.twig",[
            "form" => $form->createView()
        ]);
    }
}