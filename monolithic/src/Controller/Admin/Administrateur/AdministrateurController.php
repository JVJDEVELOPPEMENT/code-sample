<?php

declare(strict_types=1);

namespace App\Controller\Admin\Administrateur;

use App\Entity\User;
use App\Form\Administrateur\AdministrateurType;
use App\Repository\UserRepository;
use App\Service\Mailer\Mailer;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Csrf\TokenGenerator\TokenGeneratorInterface;

#[Route('/super-admin/administrateur', name: 'super_admin_administrateur_')]
class AdministrateurController extends AbstractController
{
    public function __construct(private UserRepository $userRepository,
                                private EntityManagerInterface $em,
                                private TokenGeneratorInterface $tokenGenerator,
                                private UserPasswordHasherInterface $userPasswordHasher,
                                private Mailer $mailer){}

    #[Route('/', name: 'index')]
    public function index(): Response
    {
        $users = $this->userRepository->findAll();

        return $this->render("admin/administrateur/index.html.twig",[
            "users" => $users
        ]);
    }

    #[Route('/add', name: 'add')]
    public function add(Request $request): Response
    {
        /** @var User $user */
        $user = $this->getUser();

        $newUser = new User();

        $newUser->setCreatedBy($user);

        $form = $this->createForm(AdministrateurType::class, $newUser);

        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid())
        {
            $token = $this->tokenGenerator->generateToken();

            $password = mb_strcut($token,5,6);

            $newUser->setPassword(
                $this->userPasswordHasher->hashPassword(
                    $newUser,
                    $password
                )
            );

            $newUser->setRoles(["ROLE_ADMIN"]);

            $this->em->persist($newUser);

            $this->em->flush();

            $this->mailer->sendEmailToNewAdministrateur($newUser,$password);

            $this->addFlash("success","Un nouvel administrateur a bien été ajouté.");

            return $this->redirectToRoute("super_admin_administrateur_index");
        }

        return $this->render("admin/administrateur/add.html.twig",[
            "form" => $form->createView()
        ]);
    }

    #[Route('/edit/{id}', name: 'edit')]
    public function edit(Request $request, User $userToEdit): Response
    {
        /** @var User $user */
        $user = $this->getUser();

        $form = $this->createForm(AdministrateurType::class, $userToEdit);

        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid())
        {
            $userToEdit->setUpdatedBy($user);
            $userToEdit->setUpdatedAt(new \DateTimeImmutable());

            $this->em->persist($userToEdit);

            $this->em->flush();

            $this->addFlash("success","Les informations de l'administrateur ont bien été mis à jour.");

            return $this->redirectToRoute("super_admin_administrateur_index");
        }

        return $this->render("admin/administrateur/edit.html.twig",[
            "form" => $form->createView()
        ]);
    }
}