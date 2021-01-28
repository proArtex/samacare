<?php declare(strict_types=1);

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\TweetRepository")
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
     * @var ArrayCollection<TweetReply>
     * @ORM\ManyToMany(targetEntity="TweetReply", cascade={"persist"})
     * @ORM\JoinTable(name="tweet_replies",
     *      joinColumns={@ORM\JoinColumn(name="tweet_id", referencedColumnName="id")},
     *      inverseJoinColumns={@ORM\JoinColumn(name="reply_id", referencedColumnName="id")}
     * )
     */
    private $replies;

    /**
     * @var bool
     * @ORM\Column(type="boolean")
     */
    private $isPrivate;

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
        $this->replies = new ArrayCollection();
        $this->isPrivate = false;
    }

    public function makePrivate(): void
    {
        $this->isPrivate = true;
    }

    public function makePublic(): void
    {
        $this->isPrivate = false;
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

    public function getReplies(): array
    {
        return $this->replies->toArray();
    }

    public function getTimestamp(): int
    {
        return $this->timestamp;
    }

    public function addReply(TweetReply $tweetReply): void
    {
        //TODO: can an author reply to himself?
        $this->replies->add($tweetReply);
    }
}
