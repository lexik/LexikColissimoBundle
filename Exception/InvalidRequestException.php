<?php

namespace Lexik\Bundle\ColissimoBundle\Exception;

use Symfony\Component\Validator\ConstraintViolationList;

/**
 * InvalidRequestException thrown if request does not pass validation
 *
 * @author Nicolas Cabot <n.cabot@lexik.fr>
 */
class InvalidRequestException extends \Exception
{
    /**
     * @var ConstraintViolationList
     */
    protected $violations;

    /**
     * @param ConstraintViolationList $violations
     */
    public function setViolations($violations)
    {
        $this->violations = $violations;
    }

    /**
     * @return ConstraintViolationList
     */
    public function getViolations()
    {
        return $this->violations;
    }
}
