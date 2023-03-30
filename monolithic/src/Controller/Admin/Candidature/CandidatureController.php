<?php

declare(strict_types=1);

namespace App\Controller\Admin\Candidature;

use App\Entity\Candidate;
use App\Entity\Candidature;
use App\Entity\Quiz;
use App\Entity\QuizAnswer;
use App\Entity\QuizQuestion;
use App\Entity\RefAnswer;
use App\Entity\RefExamen;
use App\Entity\RefQuestion;
use App\Entity\User;
use App\Form\Candidature\CandidatureType;
use App\Form\Candidature\MultiCandidatureType;
use App\Repository\RefQuestionRepository;
use App\Service\Mailer\Mailer;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Csrf\TokenGenerator\TokenGeneratorInterface;
use Symfony\UX\Chartjs\Builder\ChartBuilderInterface;
use Symfony\UX\Chartjs\Model\Chart;

#[Route('/admin/candidature', name: 'admin_candidature_')]
class CandidatureController extends AbstractController
{
    public function __construct(private RefQuestionRepository $refQuestionRepository,
                                private EntityManagerInterface $em,
                                private TokenGeneratorInterface $tokenGenerator,
                                private ChartBuilderInterface $chartBuilder,
                                private Mailer $mailer){}

    #[Route('/show/{id}', name: 'show')]
    public function show(Candidature $candidature): Response
    {
        $donutChart =  $this->getDonutChart($candidature->getQuiz());

        $quizQuestions = $candidature->getQuiz()->getQuizQuestions();

        return $this->render("admin/candidate/candidature/show.html.twig", [
            "candidature" => $candidature,
            "donutChart" => $donutChart,
            "quizQuestions" => $quizQuestions
        ]);
    }

    #[Route('/add/{id}', name: 'add')]
    public function add(Request $request, Candidate $candidate): Response
    {
        /** @var User $user */
        $user = $this->getUser();

        $candidature = new Candidature();

        $candidature->setCreatedBy($user);

        $candidature->setCandidate($candidate);

        $form = $this->createForm(CandidatureType::class, $candidature);

        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()) {

            $quiz = $this->computeQuizCreation($candidature, $user);

            $this->em->persist($candidature);

            $this->em->flush();

            $this->mailer->sendEmailToCandidate($candidate, $quiz->getTokenLink());

            $this->addFlash("success","Une nouvelle candidature a bien été envoyé.");

            return $this->redirectToRoute("admin_candidate_profile",["id" => $candidate->getId()]);
        }

        return $this->render("admin/candidate/candidature/add.html.twig", [
            "form" => $form->createView(),
            "candidate" => $candidate
        ]);
    }

    #[Route('/multi', name: 'multi')]
    public function multi(Request $request): Response
    {
        /** @var User $user */
        $user = $this->getUser();

        $form = $this->createForm(MultiCandidatureType::class);

        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()) {

            $refExamen = $form->get('refExamen')->getData();

            $candidates = $form->get('candidate')->getData();

            if(count($candidates) > 0 === false)
            {
                $this->addFlash("danger","Vous devez sélectionner des candidats.");
                return $this->redirectToRoute("admin_candidature_multi");
            }

            $this->createCandidatureForMulti($candidates, $refExamen, $user);

            $this->em->flush();

            $this->addFlash("success","Les candidats ont bien reçu les candidatures.");

            return $this->redirectToRoute("admin_candidature_multi");

        }

        return $this->render("admin/candidate/multi_candidature/index.html.twig", [
            "form" => $form->createView(),
        ]);

    }

    private function computeQuizCreation(Candidature $candidature, User $user): Quiz
    {
        $randomRefQuestions = $this->getRandomRefQuestions($candidature->getRefExamen());

        $quiz = $this->createQuiz($candidature, $user);

        $quizQuestions = $this->createQuizQuestions($randomRefQuestions, $quiz, $user);

        $this->createQuizAnswers($quizQuestions ,$user);

        return $quiz;
    }

    private function getRandomRefQuestions(RefExamen $refExamen): array
    {
        $refQuestions = $this->refQuestionRepository->findBy([
            'deleted' => false
        ]);

        $refQuestionsAskable = [];

        foreach ($refQuestions as $refQuestion)
        {
            if(true === $refQuestion->isAskable())
            {
                $refQuestionsAskable[] = $refQuestion;
            }
        }

        $selectedRefQuestionsKey = array_rand($refQuestionsAskable, $refExamen->getNumberOfQuestions());

        $quizQuestions = [];

        foreach ($refQuestionsAskable as $key => $question)
        {
            if(true === in_array($key, $selectedRefQuestionsKey))
            {
                $quizQuestions[] = $question;
            }
        }

        return $quizQuestions;
    }

    private function createQuiz(Candidature $candidature, User $user): Quiz
    {
        $quiz = new Quiz();

        $quiz->setCreatedBy($user);

        $quiz->setTokenLink(uniqid().$this->tokenGenerator->generateToken());

        $quiz->setCandidature($candidature);

        $quiz->setNumberOfQuestions($candidature->getRefExamen()->getNumberOfQuestions());

        $quiz->setNumberOfMinutesToAnswer($candidature->getRefExamen()->getNumberOfMinutesToAnswer());

        $this->em->persist($quiz);

        return $quiz;
    }

    private function createQuizQuestions(array $randomRefQuestions, Quiz $quiz, User $user): array
    {
        $quizQuestions = [];

        /** @var RefQuestion $randomRefQuestion */
        foreach ($randomRefQuestions as $randomRefQuestion)
        {
            $quizQuestion = new QuizQuestion();
            $quizQuestion->setCreatedBy($user);
            $quizQuestion->setRefQuestion($randomRefQuestion);
            $quizQuestion->setQuiz($quiz);
            $quizQuestion->setTitle($randomRefQuestion->getTitle());
            $quizQuestion->setInputType($randomRefQuestion->getInputType());
            $quizQuestions[] = $quizQuestion;

            $this->em->persist($quizQuestion);
        }

        return $quizQuestions;
    }

    private function createQuizAnswers(array $quizQuestions, User $user): void
    {
        /** @var QuizQuestion $quizQuestion */
        foreach ($quizQuestions as $quizQuestion)
        {
            $refQuestion = $quizQuestion->getRefQuestion();

            $refAnswers = $refQuestion->getRefAnswers();

            /** @var RefAnswer $refAnswer */
            foreach ($refAnswers as $refAnswer)
            {
                $quizAnswer = new QuizAnswer();
                $quizAnswer->setCreatedBy($user);
                $quizAnswer->setRefAnswer($refAnswer);
                $quizAnswer->setQuizQuestion($quizQuestion);
                $quizAnswer->setTitle($refAnswer->getTitle());
                $quizAnswer->setIsCorrect($refAnswer->getIsCorrect());

                $this->em->persist($quizAnswer);
            }
        }
    }

    private function getDonutChart(Quiz $quiz): Chart
    {
        $chart = $this->chartBuilder->createChart(Chart::TYPE_DOUGHNUT);

        $goodAnswers = $quiz->getNumberOfCorrectAnswers();

        $badAnswers = $quiz->getNumberOfBadAnswers();

        $chart->setData([
            'datasets' => [
                [
                    'backgroundColor' => ['#0acf97','#fa5c7c'],
                    'borderColor' => 'white',
                    'data' => [$goodAnswers,$badAnswers],
                ],
            ],
        ]);

        $chart->setOptions([]);

        return $chart;
    }

    private function createCandidatureForMulti(Collection $candidates,RefExamen $refExamen, User $user)
    {
        $randomRefQuestions = $this->getRandomRefQuestions($refExamen);

        foreach ($candidates as $candidate)
        {
            $candidature = new Candidature();
            $candidature->setCreatedBy($user);
            $candidature->setCandidate($candidate);
            $candidature->setRefExamen($refExamen);
            $quiz = $this->createQuiz($candidature, $user);
            $quizQuestions = $this->createQuizQuestions($randomRefQuestions, $quiz, $user);
            $this->createQuizAnswers($quizQuestions ,$user);
            $this->em->persist($candidature);
            $this->mailer->sendEmailToCandidate($candidate, $quiz->getTokenLink());
        }
    }
}