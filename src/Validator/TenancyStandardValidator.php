<?php

namespace App\Validator;

use App\Entity\Tenancy;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;

class TenancyStandardValidator extends ConstraintValidator
{
    public function __construct(private EntityManagerInterface $entityManager){}

    public function validate($value, Constraint $constraint)
    {
        /* @var TenancyStandard $constraint */

        if (null === $value || '' === $value) {
            return;
        }

        $tenancy = $this->entityManager->getRepository(Tenancy::class)->findOneBy([
            'default' => true
        ]);
        if(null !== $tenancy && true === $value && $tenancy->getId() !== $this->context->getObject()->getId()) {
            $this->context->buildViolation($constraint->message)
                ->setParameter('{{ value }}', true)
                ->addViolation();
        }
    }
}
