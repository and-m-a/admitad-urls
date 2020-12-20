<?php


namespace App\Controller;


use App\Entity\BaseEntity;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Validator\ConstraintViolationListInterface;

class BaseController extends AbstractController
{
    /**
     * @param ConstraintViolationListInterface $violationList
     * @return bool
     */
    public function hasViolations(ConstraintViolationListInterface $violationList): bool
    {
        return $violationList->count();
    }

    /**
     * @param ConstraintViolationListInterface $violationList
     * @return JsonResponse
     */
    public function jsonValidationsResponse(ConstraintViolationListInterface $violationList): JsonResponse
    {
        foreach ($violationList as $violation) {
            $errors[$violation->getPropertyPath()][] = $violation->getMessage();
        }

        return $this->json([
            'errors' => $errors ?? []
        ]);
    }

    /**
     * @param BaseEntity $entity
     * @return BaseEntity
     */
    public function saveEntity(BaseEntity $entity): BaseEntity
    {
        $entityManager = $this->getDoctrine()->getManager();
        $entityManager->persist($entity);
        $entityManager->flush();

        return $entity;
    }
}