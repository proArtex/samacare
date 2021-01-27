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
     * Many Users have Many Users.
     * @ORM\ManyToMany(targetEntity="User", mappedBy="followers")
     */
    private $follows;

    /**
     * @ORM\ManyToMany(targetEntity="User", inversedBy="follows")
     * @ORM\JoinTable(name="followers",
     *      joinColumns={@ORM\JoinColumn(name="user_id", referencedColumnName="id")},
     *      inverseJoinColumns={@ORM\JoinColumn(name="follower_user_id", referencedColumnName="id")}
     * )
     */
    private $followers;

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
        $this->follows = new ArrayCollection();
        $this->followers = new ArrayCollection();
        $this->registeredAt =  new DateTimeImmutable('now');
    }

    public function follow(User $user): void
    {
        if ($this->follows->contains($user)) {
            throw new UserException('You have already been following this author');
        }

        $this->follows->add($user);
        $user->followers->add($this);
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
