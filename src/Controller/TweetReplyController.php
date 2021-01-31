<?php

namespace App\Controller;

use App\DTO\TweetReplyDTO;
use App\Entity\Tweet;
use App\Entity\TweetReply;
use App\Exception\TweetReplyException;
use FOS\RestBundle\Controller\AbstractFOSRestController;
use FOS\RestBundle\View\View;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\UnprocessableEntityHttpException;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Validator\ConstraintViolationListInterface;

class TweetReplyController extends AbstractFOSRestController
{
    /**
     * @Route(
     *     "/api/tweets/{tweetId}/replies",
     *     name="tweet_reply.create",
     *     methods={"POST"},
     *     requirements={"tweetId": "\d+"}
     * )
     * @ParamConverter(
     *     "tweetReplyDTO",
     *     converter="fos_rest.request_body",
     *     options={"validator"={"groups"={"create"}}}
     * )
     * @IsGranted("ROLE_USER")
     */
    public function create(
        int $tweetId,
        TweetReplyDTO $tweetReplyDTO,
        ConstraintViolationListInterface $validationErrors
    ): View {
        if (count($validationErrors)) {
            return $this->view($validationErrors, Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        $tweet = $this->getDoctrine()->getRepository(Tweet::class)->find($tweetId);

        if (!$tweet) {
            throw $this->createNotFoundException('Tweet not found');
        }

        try {
            $tweetReply = new TweetReply($tweetReplyDTO->message, $this->getUser());
            $tweet->addReply($tweetReply);
        } catch (TweetReplyException $e) {
            throw new UnprocessableEntityHttpException($e->getMessage());
        }

        $em = $this->getDoctrine()->getManager();
        $em->flush();

        return $this->view($tweetReply->getId(), Response::HTTP_CREATED);
    }
}
