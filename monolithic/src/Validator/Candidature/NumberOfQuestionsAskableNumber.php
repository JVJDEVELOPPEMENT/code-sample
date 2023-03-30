<?php

namespace App\Validator\Candidature;

use Symfony\Component\Validator\Constraint;

/**
 * @Annotation
 */
#[\Attribute] class NumberOfQuestionsAskableNumber extends Constraint
{
    public string $message = 'Erreur custom';

    public function getTargets(): string
    {
        return self::PROPERTY_CONSTRAINT;
    }
}
