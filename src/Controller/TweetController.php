<?php

namespace App\Controller;

use App\DTO\TweetDTO;
use App\Entity\Tweet;
use App\Entity\TweetReply;
use App\Exception\TweetException;
use FOS\RestBundle\Controller\AbstractFOSRestController;
use FOS\RestBundle\View\View;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\HttpKernel\Exception\UnprocessableEntityHttpException;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Validator\ConstraintViolationListInterface;

class TweetController extends AbstractFOSRestController
{
    /**
     * @Route(
     *     "/api/tweets",
     *     name="tweet.create",
     *     methods={"POST"}
     * )
     * @ParamConverter(
     *     "tweetDTO",
     *     converter="fos_rest.request_body",
     *     options={"validator"={"groups"={"create"}}}
     * )
     * @IsGranted("ROLE_USER")
     */
    public function create(TweetDTO $tweetDTO, ConstraintViolationListInterface $validationErrors): View
    {
        if (count($validationErrors)) {
            return $this->view($validationErrors, Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        try {
            $tweet = new Tweet($tweetDTO->message, $this->getUser());
        } catch (TweetException $e) {
            throw new UnprocessableEntityHttpException($e->getMessage());
        }

        $em = $this->getDoctrine()->getManager();
        $em->persist($tweet);
        $em->flush();

        return $this->view($tweet->getId(), Response::HTTP_CREATED);
    }

    /**
     * @Route(
     *     "/api/tweets",
     *     name="tweet.list",
     *     methods={"GET"}
     * )
     */
    public function list(Request $request): View
    {
        //TODO: validation
        $filter = $request->query->get('filter', []);

        $tweets = $this
            ->getDoctrine()
            ->getRepository(Tweet::class)
            ->findAllByFilterForUser($filter, $this->getUser());

        return $this->view(
            array_map(
                function (Tweet $tweet) {
                    return [
                        'id' => $tweet->getId(),
                        'authorId' => $tweet->getAuthor()->getId(),
                        'message' => $tweet->getMessage(),
                        'timestamp' => $tweet->getTimestamp()
                    ];
                },
                $tweets
            )
        );
    }

    /**
     * @Route(
     *     "/api/tweets/{tweetId}",
     *     name="tweet.fetch",
     *     methods={"GET"},
     *     requirements={"tweetId": "\d+"}
     * )
     */
    public function fetch(int $tweetId): View
    {
        $tweet = $this
            ->getDoctrine()
            ->getRepository(Tweet::class)
            ->findForUser($tweetId, $this->getUser());

        if (!$tweet) {
            throw $this->createNotFoundException('Tweet not found');
        }

        return $this->view(
            [
                'id' => $tweet->getId(),
                'authorId' => $tweet->getAuthor()->getId(),
                'message' => $tweet->getMessage(),
                'timestamp' => $tweet->getTimestamp(),
                'replies' => array_map(
                    function (TweetReply $reply) {
                        return [
                            'id' => $reply->getId(),
                            'message' => $reply->getMessage(),
                            'authorId' => $reply->getAuthor()->getId(),
                            'timestamp' => $reply->getTimestamp(),
                        ];
                    },
                    $tweet->getReplies()
                )
            ]
        );
    }

    /**
     * @Route(
     *     "/api/tweets/{tweetId}",
     *     name="tweet.patch",
     *     methods={"PATCH"},
     *     requirements={"tweetId": "\d+"}
     * )
     * @ParamConverter(
     *     "tweetDTO",
     *     converter="fos_rest.request_body",
     *     options={"validator"={"groups"={"patch"}}}
     * )
     * @IsGranted("ROLE_USER")
     */
    public function patch(
        int $tweetId,
        TweetDTO $tweetDTO,
        ConstraintViolationListInterface $validationErrors
    ): View {
        $tweet = $this->getDoctrine()->getRepository(Tweet::class)->find($tweetId);

        if (!$tweet) {
            throw $this->createNotFoundException('Tweet not found');
        }

        if ($tweet->getAuthor()->getId() !== $this->getUser()->getId()) {
            throw new AccessDeniedHttpException("You cannot modify someone's tweet");
        }

        if (count($validationErrors)) {
            return $this->view($validationErrors, Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        if ($tweetDTO->isPrivate) {
            $tweet->makePrivate();
        } else {
            $tweet->makePublic();
        }

        $this->getDoctrine()->getManager()->flush();

        return $this->view(null, Response::HTTP_NO_CONTENT);
    }
}
