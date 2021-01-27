<?php declare(strict_types=1);

namespace App\Entity;

use App\Exception\UserException;
use DateTimeImmutable;
use DateTimeInterface;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Security\Core\User\UserInterface;

/**
 * @ORM\Entity()
 * @ORM\Table(
 *     name="user",
 *     uniqueConstraints={
 *          @ORM\UniqueConstraint(name="user_unique_username", columns={"username"})
 *     }
 * )
 * @UniqueEntity("username")
 */
class User implements UserInterface
{
    const ROLE_DEFAULT = 'ROLE_USER';

    /**
     * @var int|null
     * @ORM\Id()
     * @ORM\GeneratedValue(strategy="AUTO")
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @var string
     * @ORM\Column(type="string", length=180)
     */
    private $username;

    /**
     * @var string
     * @ORM\Column(type="string", length=24)
     */
    private $token;

    /**
     * @var string[]
     * @ORM\Column(type="simple_array")
     */
    private $roles;

    /**
     * @var ArrayCollection<User>
     * @ORM\ManyToMany(targetEntity="User")
     * @ORM\JoinTable(name="followers",
     *      joinColumns={@ORM\JoinColumn(name="user_id", referencedColumnName="id")},
     *      inverseJoinColumns={@ORM\JoinColumn(name="follower_user_id", referencedColumnName="id")}
     * )
     */
    private $followers;

    /**
     * @var ArrayCollection<User>
     * @ORM\ManyToMany(targetEntity="User")
     * @ORM\JoinTable(name="blocked_followers",
     *      joinColumns={@ORM\JoinColumn(name="author_id", referencedColumnName="id")},
     *      inverseJoinColumns={@ORM\JoinColumn(name="blocked_follower_id", referencedColumnName="id")}
     * )
     */
    private $blockedFollowers;

    /**
     * @var DateTimeInterface
     * @ORM\Column(type="datetime")
     */
    private $registeredAt;

    public function __construct(string $username, string $token)
    {
        $this->roles = [self::ROLE_DEFAULT];
        $this->username = $username;
        $this->token = $token;
        $this->followers = new ArrayCollection();
        $this->blockedFollowers = new ArrayCollection();
        $this->registeredAt = new DateTimeImmutable('now');
    }

    public function follow(User $user): void
    {
        if ($user->followers->contains($this)) {
            throw new UserException('A user have already been following the author');
        }

        $user->followers->add($this);
    }

    public function unfollow(User $user): void
    {
        if (!$user->followers->contains($this)) {
            throw new UserException("A user haven't been following the author yet");
        }

        $user->followers->removeElement($this);
    }

    public function blockFollower(User $user): void
    {
        if ($this->blockedFollowers->contains($user)) {
            throw new UserException("A user has already been blocked");
        }

        $this->blockedFollowers->add($user);
    }

    public function hasBlocked(User $user): bool
    {
        return $this->blockedFollowers->contains($user);
    }

    public function getId()
    {
        return $this->id;
    }

    public function getRoles()
    {
        return $this->roles;
    }

    public function getUsername()
    {
        return $this->username;
    }

    public function getRegisteredAt(): DateTimeInterface
    {
        return $this->registeredAt;
    }

    public function getPassword()
    {
        // NOOP
    }

    public function getSalt()
    {
        // NOOP
    }

    public function eraseCredentials()
    {
        // NOOP
    }
}
