<?php declare(strict_types=1);

namespace App\Entity;

use App\Exception\TweetReplyException;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity()
 * @ORM\Table(
 *     name="tweet_reply"
 * )
 */
class TweetReply
{
    private const MAX_LENGTH = 140;

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
     * @var int
     * @ORM\Column(type="integer")
     */
    private $timestamp;

    public function __construct(string $message, User $author)
    {
        if (mb_strlen($message) > self::MAX_LENGTH) {
            throw new TweetReplyException("A tweet reply's message must not be longer than " . self::MAX_LENGTH . ' symbols');
        }

        $this->message = $message;
        $this->author = $author;
        $this->timestamp = time();
    }

    public function getId()
    {
        return $this->id;
    }

    public function getMessage(): string
    {
        return $this->message;
    }

    public function getAuthor(): User
    {
        return $this->author;
    }

    public function getTimestamp(): int
    {
        return $this->timestamp;
    }
}
