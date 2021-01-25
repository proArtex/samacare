<?php

namespace App\Controller;

use App\Entity\Tweet;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class TweetController extends AbstractController
{
    /**
     * @Route("/api/tweet", name="tweet.create", methods={"POST"})
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
     * @Route("/api/tweets", name="tweet.all", methods={"GET"})
     */
    public function all(Request $request): Response
    {
        $tweets = $this->getDoctrine()->getRepository(Tweet::class)->findAll();

        return new JsonResponse(
            array_map(
                function (Tweet $tweet) {
                    return [
                        'id' => $tweet->getId(),
                        'user' => $tweet->getAuthor()->getId(),
                        'message' => $tweet->getMessage()
                    ];
                },
                $tweets
            )
        );
    }
}
