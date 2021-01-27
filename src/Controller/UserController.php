<?php

declare(strict_types=1);

namespace App\Controller;

use App\Entity\User;
use App\Exception\UserException;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\UnprocessableEntityHttpException;
use Symfony\Component\Routing\Annotation\Route;

class UserController extends AbstractController
{
    /**
     * @Route(
     *     "/api/authors/{authorId}/followers",
     *     name="follower.create",
     *     methods={"POST"},
     *     requirements={"id": "\d+"}
     * )
     * @IsGranted("ROLE_USER")
     */
    public function createFollower(int $authorId): Response
    {
        $currentUser = $this->getUser();
        $author = $this->getDoctrine()->getRepository(User::class)->find($authorId);

        if (!$author) {
            throw $this->createNotFoundException('Author not found');
        }

        if ($author->getId() === $currentUser->getId()) {
            throw new UnprocessableEntityHttpException('You cannot follow yourself');
        }

        try {
            $currentUser->follow($author);
            $this->getDoctrine()->getManager()->flush();
        } catch (UserException $e) {
            throw new UnprocessableEntityHttpException($e->getMessage(), $e);
        }

        return new JsonResponse(null, Response::HTTP_NO_CONTENT);
    }
}