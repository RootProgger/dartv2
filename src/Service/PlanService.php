<?php

namespace App\Service;

use Doctrine\ORM\EntityManagerInterface;

class PlanService
{
    public function __construct(private EntityManagerInterface $entityManager){}
}
