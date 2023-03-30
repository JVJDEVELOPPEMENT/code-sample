<?php

declare(strict_types=1);

namespace App\Service\Mailer;

use App\Entity\Candidate;
use App\Entity\User;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;

class Mailer
{
    public function __construct(private MailerInterface $mailer){}

    public function sendEmailToNewAdministrateur(User $user,string $password): void
    {
        $email = (new TemplatedEmail())
            ->from('no-reply@jvj-developpement.com')
            ->to($user->getEmail())
            ->subject('Nouvel Administrateur')
            ->text('Nouvel Administrateur')
            ->htmlTemplate("email/administrateur/add.html")
            ->context([
                'lastName' => $user->getLastName(),
                'firstName' => $user->getFirstName(),
                'emailAddress' => $user->getEmail(),
                'password' => $password,
            ]);

        $this->mailer->send($email);
    }

    public function sendEmailToCandidate(Candidate $candidate, string $tokenLink): void
    {
        $email = (new TemplatedEmail())
            ->from('no-reply@jvj-developpement.com')
            ->to($candidate->getEmail())
            ->subject('Nouveau test')
            ->text('Nouveau test')
            ->htmlTemplate("email/candidate/quiz_link.html.twig")
            ->context([
                'lastName' => $candidate->getLastName(),
                'firstName' => $candidate->getFirstName(),
                'emailAddress' => $candidate->getEmail(),
                'tokenLink' => $tokenLink,
            ]);

        $this->mailer->send($email);
    }
}