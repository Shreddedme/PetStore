<?php

namespace App\Model\ErrorHandling;

use OpenApi\Annotations as OA;

/**
 * @OA\Schema(
 *     title="ErrorResponse",
 *     description="Ответ об ошибке",
 *     @OA\Property(
 *         property="errors",
 *         type="array",
 *         @OA\Items(
 *             type="object",
 *             @OA\Property(property="field", type="string", default="name"),
 *             @OA\Property(property="message", type="string", default="Forbidden characters cant be entered")
 *         )
 *     )
 * )
 */
class ErrorResponse
{
    /**
     * @var string[]|null
     */
    private ?array $errors = null;

    public function addError(array $errors): void
    {
        $this->errors[] = $errors;
    }

    /**
     * @return string[]|null
     */
    public function getErrors(): ?array
    {
        return $this->errors;
    }
}
