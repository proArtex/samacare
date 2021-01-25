<?php

namespace App\Controller;

use App\Entity\Tweet;
use App\Entity\TweetRely;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class TweetReplyController extends AbstractController
{
    /**
     * @Route("/api/tweet-reply", name="tweet_reply.create", methods={"POST"})
     * @IsGranted("ROLE_USER")
     */
    public function create(Request $request): Response
    {
        //TODO: data validation
        $data = json_decode($request->getContent());
        $tweetId = $data->tweetId;

        $tweet = $this->getDoctrine()->getRepository(Tweet::class)->find($tweetId);

        if (!$tweet) {
            throw $this->createNotFoundException('Tweet not found');
        }

        $tweetReply = new TweetRely($data->message, $this->getUser());
        $tweet->addReply($tweetReply);

        $em = $this->getDoctrine()->getManager();
        $em->persist($tweet);
        $em->flush();

        return new JsonResponse($tweetReply->getId(), Response::HTTP_CREATED);
    }
}