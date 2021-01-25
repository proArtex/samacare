<?php declare(strict_types=1);

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity()
 * @ORM\Table(
 *     name="tweet"
 * )
 */
class Tweet
{
    /**
     * @var int|null
     * @ORM\Id()
     * @ORM\GeneratedValue(strategy="AUTO")
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @var string
     * @ORM\Column(type="string", length=140)
     */
    private $message;

    /**
     * @var User
     * @ORM\ManyToOne(targetEntity="App\Entity\User")
     * @ORM\JoinColumn(name="user_id", referencedColumnName="id")
     */
    private $author;

    /**
     * @var Tweet[]
     * @ORM\OneToMany(targetEntity="App\Entity\TweetRely", mappedBy="tweet", cascade={"persist"})
     * @ORM\JoinTable(name="tweet_replies",
     *      joinColumns={@ORM\JoinColumn(name="tweet_id", referencedColumnName="id")},
     *      inverseJoinColumns={@ORM\JoinColumn(name="reply_id", referencedColumnName="id")}
     * )
     */
    private $replies;

    /**
     * @var int
     * @ORM\Column(type="integer")
     */
    private $timestamp;

    public function __construct(string $message, User $author)
    {
        $this->message = $message;
        $this->author = $author;
        $this->timestamp = time();
        $this->replies =  new ArrayCollection();
    }

    public function getId()
    {
        return $this->id;
    }

    public function getAuthor(): User
    {
        return $this->author;
    }

    public function getMessage(): string
    {
        return $this->message;
    }

    public function addReply(TweetRely $tweetReply): void
    {
        $this->replies->add($tweetReply);
    }
}