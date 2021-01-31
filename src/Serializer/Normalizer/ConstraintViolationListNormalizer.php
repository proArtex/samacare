<?php

declare(strict_types=1);

namespace App\Serializer\Normalizer;

use Symfony\Component\Serializer\Normalizer\NormalizerInterface;
use Symfony\Component\Validator\ConstraintViolation;
use Symfony\Component\Validator\ConstraintViolationListInterface;

class ConstraintViolationListNormalizer implements NormalizerInterface
{
    public function normalize($object, $format = null, array $context = [])
    {
        $errors = [];

        /** @var ConstraintViolation $violation */
        foreach ($object as $violation) {
            $propertyPath = $violation->getPropertyPath();
            $errors[] = ($propertyPath ? "{$propertyPath}: " : '') . $violation->getMessage();
        }
        return ['errors' => $errors];
    }

    public function supportsNormalization($data, $format = null)
    {
        return $data instanceof ConstraintViolationListInterface;
    }
}