<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use App\Repository\UserRepository;
use Doctrine\Common\Collections\Collection;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;

/**
 * @ORM\Entity(repositoryClass=UserRepository::class)
 */
class User implements UserInterface, PasswordAuthenticatedUserInterface
{
    /**
     * @Groups({"user_browse", "user_read", "event_read", "favorite_browse", "event_browse"})
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @Groups({"user_browse", "user_read", "favorite_browse"})
     * @ORM\Column(type="string", length=180, unique=true)
     */
    private $email;

    /**
     * @Groups({"user_read"})
     * @ORM\Column(type="json")
     */
    private $roles = [];

    /**
     * @var string The hashed password
     * @ORM\Column(type="string")
     */
    private $password;

    /**
     * @Groups({"user_browse", "user_read"})
     * @ORM\Column(type="string", length=255)
     */
    private $firstname;

    /**
     * @Groups({"user_browse", "user_read"})
     * @ORM\Column(type="string", length=255)
     */
    private $lastname;

    /**
     * @Groups({"user_read"})
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $phoneNumber;

    /**
     * @Groups({"user_browse", "user_read", "event_read"})
     * @ORM\Column(type="string", length=255, options={"default"="user_placeholder.png"})
     */
    private $avatar;

    /**
     * @Groups({"user_read"})
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $address;

    /**
     * @Groups({"user_read"})
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $zipcode;

    /**
     * @Groups({"user_read"})
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $city;

    /**
     * @Groups({"user_read"})
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $country;

    /**
     * @Groups({"user_read"})
     * @ORM\Column(type="decimal", precision=8, scale=6, nullable=true)
     */
    private $latitude;

    /**
     * @Groups({"user_read"})
     * @ORM\Column(type="decimal", precision=9, scale=6, nullable=true)
     */
    private $longitude;

    /**
     * @Groups({"user_read"})
     * @ORM\Column(type="datetime_immutable")
     */
    private $createdAt;

    /**
     * @Groups({"user_read"})
     * @ORM\Column(type="datetime_immutable", nullable=true)
     */
    private $updatedAt;
    
    /**
     * @Groups({"user_read"})
     * @ORM\Column(type="boolean", options={"default"=true})
     */
    protected $isActivate;


    /**
     * @Groups({"user_read"})
     * @ORM\OneToMany(targetEntity=Event::class, mappedBy="author")
     */
    private $createdEvents;

    /**
     * @Groups({"user_read"})
     * @ORM\ManyToMany(targetEntity=Event::class, inversedBy="members")
     */
    private $joinedEvents;

    /**
     * @Groups({"user_read"})
     * @ORM\ManyToMany(targetEntity=Favorite::class, mappedBy="user")
     */
    private $favoriteEvents;

    public function __construct()
    {
        $this->createdEvents = new ArrayCollection();
        $this->joinedEvents = new ArrayCollection();
        $this->favoriteEvents = new ArrayCollection();
        $this->createdAt = new \DateTimeImmutable;
        $this->avatar = 'user_placeholder.png';
        $this->isActivate = true;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): self
    {
        $this->email = $email;

        return $this;
    }

    /**
     * A visual identifier that represents this user.
     *
     * @see UserInterface
     */
    public function getUserIdentifier(): string
    {
        return (string) $this->email;
    }

    /**
     * @deprecated since Symfony 5.3, use getUserIdentifier instead
     */
    public function getUsername(): string
    {
        return (string) $this->email;
    }

    /**
     * @see UserInterface
     */
    public function getRoles(): array
    {
        $roles = $this->roles;
        // guarantee every user at least has ROLE_USER
        $roles[] = 'ROLE_USER';

        return array_unique($roles);
    }

    public function setRoles(array $roles): self
    {
        $this->roles = $roles;

        return $this;
    }

    /**
     * @see PasswordAuthenticatedUserInterface
     */
    public function getPassword(): string
    {
        return $this->password;
    }

    public function setPassword(string $password): self
    {
        $this->password = $password;

        return $this;
    }

    /**
     * Returning a salt is only needed, if you are not using a modern
     * hashing algorithm (e.g. bcrypt or sodium) in your security.yaml.
     *
     * @see UserInterface
     */
    public function getSalt(): ?string
    {
        return null;
    }

    /**
     * @see UserInterface
     */
    public function eraseCredentials()
    {
        // If you store any temporary, sensitive data on the user, clear it here
        // $this->plainPassword = null;
    }

    public function getFirstname(): ?string
    {
        return $this->firstname;
    }

    public function setFirstname(string $firstname): self
    {
        $this->firstname = $firstname;

        return $this;
    }

    public function getLastname(): ?string
    {
        return $this->lastname;
    }

    public function setLastname(string $lastname): self
    {
        $this->lastname = $lastname;

        return $this;
    }

    public function getPhoneNumber(): ?string
    {
        return $this->phoneNumber;
    }

    public function setPhoneNumber(?string $phoneNumber): self
    {
        $this->phoneNumber = $phoneNumber;

        return $this;
    }

    public function getAvatar(): ?string
    {
        return $this->avatar;
    }

    public function setAvatar(string $avatar): self
    {
        $this->avatar = $avatar;

        return $this;
    }

    public function getAddress(): ?string
    {
        return $this->address;
    }

    public function setAddress(?string $address): self
    {
        $this->address = $address;

        return $this;
    }

    public function getZipcode(): ?string
    {
        return $this->zipcode;
    }

    public function setZipcode(?string $zipcode): self
    {
        $this->zipcode = $zipcode;

        return $this;
    }

    public function getCity(): ?string
    {
        return $this->city;
    }

    public function setCity(?string $city): self
    {
        $this->city = $city;

        return $this;
    }

    public function getCountry(): ?string
    {
        return $this->country;
    }

    public function setCountry(?string $country): self
    {
        $this->country = $country;

        return $this;
    }

    public function getLatitude(): ?string
    {
        return $this->latitude;
    }

    public function setLatitude(?string $latitude): self
    {
        $this->latitude = $latitude;

        return $this;
    }

    public function getLongitude(): ?string
    {
        return $this->longitude;
    }

    public function setLongitude(?string $longitude): self
    {
        $this->longitude = $longitude;

        return $this;
    }

    public function getCreatedAt(): ?\DateTimeImmutable
    {
        return $this->createdAt;
    }

    public function setCreatedAt(\DateTimeImmutable $createdAt): self
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    public function getUpdatedAt(): ?\DateTimeImmutable
    {
        return $this->updatedAt;
    }

    public function setUpdatedAt(?\DateTimeImmutable $updatedAt): self
    {
        $this->updatedAt = $updatedAt;

        return $this;
    }

    public function getIsActivate(): ?bool
    {
        return $this->isActivate;
    }

    public function setIsActivate(bool $isActivate): self
    {
        $this->isActivate = $isActivate;

        return $this;
    }

    /**
     * @return Collection|Event[]
     */
    public function getCreatedEvents(): Collection
    {
        return $this->createdEvents;
    }

    public function addCreatedEvent(Event $createdEvent): self
    {
        if (!$this->createdEvents->contains($createdEvent)) {
            $this->createdEvents[] = $createdEvent;
            $createdEvent->setAuthor($this);
        }

        return $this;
    }

    public function removeCreatedEvent(Event $createdEvent): self
    {
        if ($this->createdEvents->removeElement($createdEvent)) {
            // set the owning side to null (unless already changed)
            if ($createdEvent->getAuthor() === $this) {
                $createdEvent->setAuthor(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|Event[]
     */
    public function getJoinedEvents(): Collection
    {
        return $this->joinedEvents;
    }

    public function addJoinedEvent(Event $joinedEvent): self
    {
        if (!$this->joinedEvents->contains($joinedEvent)) {
            $this->joinedEvents[] = $joinedEvent;
        }

        return $this;
    }

    public function removeJoinedEvent(Event $joinedEvent): self
    {
        $this->joinedEvents->removeElement($joinedEvent);

        return $this;
    }

    /**
     * @return Collection|Favorite[]
     */
    public function getFavoriteEvents(): Collection
    {
        return $this->favoriteEvents;
    }

    public function addFavoriteEvent(Favorite $favoriteEvent): self
    {
        if (!$this->favoriteEvents->contains($favoriteEvent)) {
            $this->favoriteEvents[] = $favoriteEvent;
            $favoriteEvent->addUser($this);
        }

        return $this;
    }

    public function removeFavoriteEvent(Favorite $favoriteEvent): self
    {
        if ($this->favoriteEvents->removeElement($favoriteEvent)) {
            $favoriteEvent->removeUser($this);
        }

        return $this;
    }

    /**
     * @Groups({"user_browse"})
     */
    public function getJoinedEventsCount()
    {
        return $this->joinedEvents->count();
    }

    /**
     * @Groups({"user_browse"})
     */
    public function getCreatedEventsCount()
    {
        return $this->createdEvents->count();
    }

    public function getFavoriteEventsCount()
    {
        return $this->favoriteEvents->count();
    }

    /**
     * @Groups({"event_read", "favorite_browse", "event_browse"})
     */
    public function getFullName()
    {
        return $this->firstname . ' ' . $this->lastname;
    }
}
