<?php

namespace App\Service;

use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\Mailer\MailerInterface;

abstract class AbstractService
{
    public function __construct(
        protected ParameterBagInterface $parameter,
        protected MailerInterface $mailer
    ) {

        $this->parameter = $parameter;
        $this->mailer = $mailer;

    }
}