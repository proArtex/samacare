<?php

declare(strict_types=1);

namespace App\Controller;

use App\Entity\User;
use App\Exception\UserException;
use FOS\RestBundle\Controller\AbstractFOSRestController;
use FOS\RestBundle\View\View;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\UnprocessableEntityHttpException;
use Symfony\Component\Routing\Annotation\Route;

class UserController extends AbstractFOSRestController
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
    public function createFollower(int $authorId): View
    {
        $currentUser = $this->getUser();
        $author = $this->getDoctrine()->getRepository(User::class)->find($authorId);

        if (!$author) {
            throw $this->createNotFoundException('Author not found');
        }

        try {
            $currentUser->follow($author);
            $this->getDoctrine()->getManager()->flush();
        } catch (UserException $e) {
            throw new UnprocessableEntityHttpException($e->getMessage(), $e);
        }

        return $this->view(null, Response::HTTP_NO_CONTENT);
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
    public function removeFollower(int $followerId): View
    {
        $currentUser = $this->getUser();
        $follower = $this->getDoctrine()->getRepository(User::class)->find($followerId);

        if (!$follower) {
            throw $this->createNotFoundException('Follower not found');
        }

        try {
            $currentUser->blockFollower($follower);
            $this->getDoctrine()->getManager()->flush();
        } catch (UserException $e) {
            throw new UnprocessableEntityHttpException($e->getMessage(), $e);
        }

        return $this->view(null, Response::HTTP_NO_CONTENT);
    }
}