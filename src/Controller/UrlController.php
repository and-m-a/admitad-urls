<?php


namespace App\Controller;


use App\Entity\URL;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Validator\Constraints\Choice;
use Symfony\Component\Validator\Constraints\Url as UrlValidator;
use App\Services\UrlService;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Validator\Constraints\Collection;
use Symfony\Component\Validator\Constraints\Required;
use Symfony\Component\Validator\Constraints\Type;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class UrlController extends BaseController
{
    /**
     * @param Request $request
     * @param ValidatorInterface $validator
     * @param UrlService $service
     * @return JsonResponse
     */
    public function store(Request $request, ValidatorInterface $validator, UrlService $service): JsonResponse
    {
        // Better place than controller should be decided to keep validations
        $violations = $validator->validate($request->toArray(), new Collection([
            'base' => [new Required, new Type('string'), new UrlValidator], // @TODO unique validation
            'short' => [new Required, new Type('string')], // @TODO unique validation
        ]));

        if ($this->hasViolations($violations)) {
            return $this->jsonValidationsResponse($violations);
        }

        return $this->json(
            $this->saveEntity(
                $service->getNewURL($request->toArray(), $this->getUser()) // UserInterface doesnt have getId method, this issue should be solved
            )->toArray()
        );
    }

    /**
     * @param Request $request
     * @param ValidatorInterface $validator
     * @return JsonResponse
     */
    public function statistics(Request $request, ValidatorInterface $validator): JsonResponse
    {
        // @TODO change validation to boolean
        $violations = $validator->validate($request->query->all(), new Collection([
            'group_by_user' => [new Type('string'), new Choice(["0", "1"])],
            'group_by_date' => [new Type('string'), new Choice(["0", "1"])],
        ]));

        if ($this->hasViolations($violations)) {
            return $this->jsonValidationsResponse($violations);
        }

        return $this->json(
            $this->getDoctrine()
                ->getRepository(URL::class)
                ->getUserStatistics($request->query->all())
        );
    }

    /**
     * @param Request $request
     * @param UrlService $service
     *
     * @TODO this should be changed to route binding
     * @param string $shortUrl
     *
     * @return Response
     */
    public function useShort(Request $request, UrlService $service, string $shortUrl): Response
    {
        $url = $this->getDoctrine()
            ->getRepository(URL::class)
            ->getBaseUrl($shortUrl);

        if (!$url) {
            throw new NotFoundHttpException('Short url does not exits');
        }

        return $this->redirect($url);
    }
}