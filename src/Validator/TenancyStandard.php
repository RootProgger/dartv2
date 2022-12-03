<?php

namespace App\Validator;

use Symfony\Component\Validator\Constraint;

/**
 * @Annotation
 * @Target({"PROPERTY", "METHOD", "ANNOTATION"})
 */
#[\Attribute(\Attribute::TARGET_PROPERTY | \Attribute::TARGET_METHOD | \Attribute::IS_REPEATABLE)]
class TenancyStandard extends Constraint
{
    public $message = 'Es existiert bereits eine Seite welche als Default gesetzt ist. Es kann nur eine Standard-Seite geben';
}
