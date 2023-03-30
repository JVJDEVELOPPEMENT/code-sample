<?php

namespace App\Validator\Candidature;

use App\Entity\RefExamen;
use App\Repository\RefQuestionRepository;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Component\Validator\Exception\UnexpectedValueException;

class NumberOfQuestionsAskableNumberValidator extends ConstraintValidator
{
    public function __construct(private RefQuestionRepository $refQuestionRepository){}

    public function validate($refExamen, Constraint $constraint)
    {
        if (null === $refExamen) {
            return;
        }

        if (!$refExamen instanceof RefExamen) {
            throw new UnexpectedValueException($refExamen, RefExamen::class);
        }

        if (!$constraint instanceof NumberOfQuestionsAskableNumber) {
            throw new UnexpectedValueException($constraint, NumberOfQuestionsAskableNumber::class);
        }

        $numberQuestions = $refExamen->getNumberOfQuestions();

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

        if($numberQuestions > count($refQuestionsAskable))
        {
            $this->context->buildViolation('Vous n\'avez pas assez de questions posables en base de données pour créer une candidature à partir de ce modèle. Vous devez avoir un minimum de ' . $numberQuestions . ' posables. Cependant vous n\'en avez que ' . count($refQuestionsAskable) . '.')
                ->addViolation();
        }
    }
}