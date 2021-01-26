<?php

namespace App\Controller;

use App\Entity\Tweet;
use App\Entity\TweetReply;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class TweetController extends AbstractController
{
    /**
     * @Route("/api/tweets", name="tweet.create", methods={"POST"})
     * @IsGranted("ROLE_USER")
     */
    public function create(Request $request): Response
    {
        //TODO: data validation
        $data = json_decode($request->getContent());

        $tweet = new Tweet($data->message, $this->getUser());

        $em = $this->getDoctrine()->getManager();
        $em->persist($tweet);
        $em->flush();

        return new JsonResponse($tweet->getId(), Response::HTTP_CREATED);
    }

    /**
     * @Route("/api/tweets", name="tweet.list", methods={"GET"})
     */
    public function list(Request $request): Response
    {
        $tweets = $this->getDoctrine()->getRepository(Tweet::class)->findAll();

        return new JsonResponse(
            array_map(
                function (Tweet $tweet) {
                    return [
                        'id' => $tweet->getId(),
                        'author_id' => $tweet->getAuthor()->getId(),
                        'message' => $tweet->getMessage(),
                        'timestamp' => $tweet->getTimestamp()
                    ];
                },
                $tweets
            )
        );
    }

    /**
     * @Route("/api/tweets/{id}", name="tweet.fetch", methods={"GET"}, requirements={"id": "\d+"})
     */
    public function fetch(int $id): Response
    {
        $tweet = $this->getDoctrine()->getRepository(Tweet::class)->find($id);

        if (!$tweet) {
            throw $this->createNotFoundException('Tweet not found');
        }

        return new JsonResponse(
            [
                'id' => $tweet->getId(),
                'author_id' => $tweet->getAuthor()->getId(),
                'message' => $tweet->getMessage(),
                'timestamp' => $tweet->getTimestamp(),
                'replies' => array_map(
                    function (TweetReply $reply) {
                        return [
                            'id' => $reply->getId(),
                            'message' => $reply->getMessage(),
                            'author_id' => $reply->getAuthor()->getId(),
                            'timestamp' => $reply->getTimestamp(),
                        ];
                    },
                    $tweet->getReplies()
                )
            ]
        );
    }
}
