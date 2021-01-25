<?php declare(strict_types=1);

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity()
 * @ORM\Table(
 *     name="tweet_reply"
 * )
 */
class TweetRely
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
     * @var int
     * @ORM\Column(type="integer")
     */
    private $timestamp;

    public function __construct(string $message, User $author)
    {
        $this->message = $message;
        $this->author = $author;
        $this->timestamp = time();
    }

    public function getId()
    {
        return $this->id;
    }
}
