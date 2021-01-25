<?php declare(strict_types=1);

namespace App\Entity;

use App\Enum\Gender;
use App\Exception\GenderAlreadyKnownException;
use App\Exception\UnknownGenderException;
use App\Exception\UserException;
use DateTimeImmutable;
use DateTimeInterface;
use Doctrine\ORM\Mapping as ORM;
use Serializable;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Security\Core\User\EquatableInterface;
use Symfony\Component\Security\Core\User\UserInterface;

/**
 * @ORM\Entity(repositoryClass="App\Repository\UserRepository")
 * @ORM\Table(
 *     name="user",
 *     uniqueConstraints={
 *          @ORM\UniqueConstraint(name="user_unique_username", columns={"username"})
 *     }
 * )
 * @UniqueEntity("username")
 */
class User implements UserInterface, Serializable, EquatableInterface
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
     * @var string|null
     * @ORM\Column(type="string", length=254, nullable=true)
     */
    private $email;

    /**
     * @var string[]
     * @ORM\Column(type="simple_array")
     */
    private $roles;

    /**
     * @var DateTimeInterface
     * @ORM\Column(type="datetime")
     */
    private $registeredAt;

    public function __construct(
        string $username,

        ?string $email
    ) {
        $this->roles = [self::ROLE_DEFAULT];
        $this->username = $username;
        $this->displayName = $displayName;
        $this->avatarPath = $avatarPath;
        $this->email = $email;
        $this->gender = Gender::UNKNOWN;
        $this->registeredAt =  new DateTimeImmutable('now');
        $this->isActive = true;
    }

    public function getId()
    {
        return $this->id;
    }

    public function getRoles()
    {
        return $this->roles;
    }

    public function getPassword()
    {
        //Do nothing coz no password exist
    }

    public function getSalt()
    {
        //Do nothing coz no salt exist
    }

    public function getUsername()
    {
        return $this->username;
    }

    public function eraseCredentials()
    {
        //Do nothing coz no credentials exist
    }

    public function serialize()
    {
        return serialize([
            $this->id,
            $this->username,
            $this->isActive,
        ]);
    }

    public function unserialize($serialized)
    {
        $data = unserialize($serialized);

        list(
            $this->id,
            $this->username,
            $this->isActive,
        ) = $data;
    }

    /**
     * @see Core/Authentication/Token/AbstractToken.php hasUserChanged()
     */
    public function isEqualTo(UserInterface $user)
    {
        if (!$user instanceof self) {
            return false;
        }

        if ($this->getId() !== $user->getId()) {
            return false;
        }

        if ($this->getUsername() !== $user->getUsername()) {
            return false;
        }

        if ($this->isActive() !== $user->isActive()) {
            return false;
        }

        return true;
    }

    public function getDisplayName(): string
    {
        return $this->displayName;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function getGender(): string
    {
        return $this->gender;
    }

    public function getRegisteredAt(): DateTimeInterface
    {
        return $this->registeredAt;
    }

    public function hasKnownGender(): bool
    {
        return $this->gender !== Gender::UNKNOWN;
    }

    public function getAvatarPath(): string
    {
        return $this->avatarPath;
    }

    public function isActive(): bool
    {
        return $this->isActive;
    }

    public function determineGender(string $gender): void
    {
        if ($this->hasKnownGender()) {
            throw new GenderAlreadyKnownException("User's gender is already known");
        }

        if ($gender !== Gender::MALE && $gender !== Gender::FEMALE) {
            throw new UnknownGenderException("Unknown gender '{$gender}'"); //TODO: UserException?
        }

        $this->gender = $gender;
    }

    public function block(): void
    {
        if ($this->isActive === false) {
            throw new UserException("User is already blocked"); //TODO: replace with Inherited
        }

        $this->isActive = false;
    }

    public function unblock(): void
    {
        if ($this->isActive === true) {
            throw new UserException("User is not blocked"); //TODO: replace with Inherited
        }

        $this->isActive = true;
    }

    public function hashCode(): string
    {
        return md5(implode(';', [
           implode(',', $this->roles),
           $this->username,
           $this->displayName,
           $this->avatarPath,
           $this->email,
           $this->gender,
           $this->isActive,
       ]));
    }
}
