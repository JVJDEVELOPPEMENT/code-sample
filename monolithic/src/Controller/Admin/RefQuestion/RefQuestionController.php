<?php

declare(strict_types=1);

namespace App\Controller\Admin\RefQuestion;

use App\Entity\RefAnswer;
use App\Entity\RefQuestion;
use App\Entity\User;
use App\Form\RefAnswer\RefAnswerType;
use App\Form\RefQuestion\RefQuestionType;
use App\Repository\RefQuestionRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/admin/ref-question', name: 'admin_ref_question_')]
class RefQuestionController extends AbstractController
{
    public function __construct(private RefQuestionRepository $refQuestionRepository,
                                private EntityManagerInterface $em){}

    #[Route('/', name: 'index')]
    public function index(): Response
    {
        $refQuestions = $this->refQuestionRepository->findAll();

        return $this->render("admin/ref_question/index.html.twig",[
            "refQuestions" => $refQuestions
        ]);
    }

    #[Route('/add', name: 'add')]
    public function add(Request $request): Response
    {
        /** @var User $user */
        $user = $this->getUser();

        $refQuestion = new RefQuestion();

        $refQuestion->setCreatedBy($user);

        $form = $this->createForm(RefQuestionType::class,$refQuestion);

        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid())
        {
            $this->em->persist($refQuestion);

            $this->em->flush();

            $this->addFlash("success","Une nouvelle question a bien été ajoutée.");

            return $this->redirectToRoute("admin_ref_question_edit",["id" => $refQuestion->getId()]);
        }

        return $this->render("admin/ref_question/add.html.twig",[
            "formRefQuestion" => $form->createView()
        ]);
    }

    #[Route('/edit/{id}', name: 'edit')]
    public function edit(Request $request, RefQuestion $refQuestion): Response
    {
        /** @var User $user */
        $user = $this->getUser();

        $formRefQuestion = $this->createForm(RefQuestionType::class,$refQuestion);

        $formRefQuestion->handleRequest($request);

        if($formRefQuestion->isSubmitted() && $formRefQuestion->isValid())
        {
            $refQuestion->setUpdatedBy($user);

            $refQuestion->setUpdatedAt(new \DateTimeImmutable());

            $this->em->persist($refQuestion);

            $this->em->flush();

            $this->addFlash("success","La question a bien été modifiée.");

            return $this->redirectToRoute("admin_ref_question_edit",["id" => $refQuestion->getId()]);
        }

        $refAnswer = new RefAnswer();

        $refAnswer->setCreatedBy($user);

        $refAnswer->setQuestion($refQuestion);

        $formRefAnswer = $this->createForm(RefAnswerType::class,$refAnswer);

        $formRefAnswer->handleRequest($request);

        if($formRefAnswer->isSubmitted() && $formRefAnswer->isValid())
        {
            if(true === $refAnswer->getIsCorrect() && $refQuestion->getInputType() === RefQuestion::QUESTION_UNIQUE_CHOICE && $refQuestion->numberOfCorrectAnswer() > 0)
            {
                $errorMessage = "La question est de type " . RefQuestion::QUESTION_UNIQUE_CHOICE . ", vous avez déjà enregistrée une réponse correcte. Vous ne pouvez pas ajouter une autre réponse correcte.";

                $this->addFlash("danger", $errorMessage);

                return $this->redirectToRoute("admin_ref_question_edit",["id" => $refQuestion->getId()]);
            }

            $this->em->persist($refAnswer);

            $this->em->flush();

            $titleRefQuestion = $refAnswer->getTitle();

            $this->addFlash("success","La réponse : $titleRefQuestion, a bien été ajoutée.");

            return $this->redirectToRoute("admin_ref_question_edit",["id" => $refQuestion->getId()]);
        }

        return $this->render("admin/ref_question/edit.html.twig",[
            "formRefQuestion" => $formRefQuestion->createView(),
            "formRefAnswer" => $formRefAnswer->createView(),
            "refQuestion" => $refQuestion
        ]);
    }

    #[Route('/delete/ref-answer/{id}', name: 'delete_ref_answer')]
    public function deleteRefAnswer(RefAnswer $refAnswer): RedirectResponse
    {
        $refQuestion = $refAnswer->getQuestion();

        $titleRefQuestion = $refAnswer->getTitle();

        $this->em->remove($refAnswer);

        $this->em->flush();

        $this->addFlash("success","La réponse : $titleRefQuestion, a bien été supprimée.");

        return $this->redirectToRoute("admin_ref_question_edit",["id" => $refQuestion->getId()]);
    }
}