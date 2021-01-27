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
     *     requirements={"authorId": "\d+"}
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

        if ($author->hasBlocked($currentUser)) {
            throw new UnprocessableEntityHttpException(
                'You have been blocked by the author and cannot follow anymore'
            );
        }

        try {
            $currentUser->follow($author);
            $this->getDoctrine()->getManager()->flush();
        } catch (UserException $e) {
            throw new UnprocessableEntityHttpException($e->getMessage(), $e);
        }

        return new JsonResponse(null, Response::HTTP_NO_CONTENT);
    }

    /**
     * @Route(
     *     "/api/self/followers/{followerId}",
     *     name="follower.delete",
     *     methods={"DELETE"},
     *     requirements={"followerId": "\d+"}
     * )
     * @IsGranted("ROLE_USER")
     */
    public function removeFollower(int $followerId): Response
    {
        $currentUser = $this->getUser();
        $follower = $this->getDoctrine()->getRepository(User::class)->find($followerId);

        if (!$follower) {
            throw $this->createNotFoundException('Follower not found');
        }

        if ($follower->getId() === $currentUser->getId()) {
            throw new UnprocessableEntityHttpException('You cannot remove yourself from your followers');
        }

        try {
            $follower->unfollow($currentUser);
            $currentUser->blockFollower($follower);
            $this->getDoctrine()->getManager()->flush();
        } catch (UserException $e) {
            throw new UnprocessableEntityHttpException($e->getMessage(), $e);
        }

        return new JsonResponse(null, Response::HTTP_NO_CONTENT);
    }
}