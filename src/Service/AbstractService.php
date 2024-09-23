<?php

namespace App\Service;

use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\Mailer\MailerInterface;

abstract class AbstractService
{
    public function __construct(
        protected ParameterBagInterface $parameterBag,
        protected MailerInterface $mailer
    ) {}
}