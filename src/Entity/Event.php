<?php

namespace App\Entity;

use App\Repository\EventRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\Serializer\Annotation\Groups;
use Vich\UploaderBundle\Mapping\Annotation as Vich;

/**
 * @ORM\Entity(repositoryClass=EventRepository::class)
 * @Vich\Uploadable
 */
class Event
{
    /**
     * @Groups({"event_browse", "user_read", "event_read", "favorite_browse", "favorite_read", "user_read"})
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @Groups({"event_browse", "user_read", "event_read", "favorite_browse", "favorite_read", "user_read"})
     * @ORM\Column(type="string", length=255)
     */
    private $title;

    /**
     * @Groups({"event_browse", "event_read", "favorite_browse"})
     * @ORM\Column(type="text")
     */
    private $description;

    /**
     * @Vich\UploadableField(mapping="event_image", fileNameProperty="picture")
     * @var File|null
     */
    private $pictureFile;

    /**
     * @return File|null
     */
    public function getPictureFile(): ?File
    {
        return $this->pictureFile;
    }

    /**
     * @param File|null $pictureFile
     */
    public function setPictureFile(?File $pictureFile): void
    {
        $this->pictureFile = $pictureFile;

        if (null !== $pictureFile) {
            // It is required that at least one field changes if you are using doctrine
            // otherwise the event listeners won't be called and the file is lost
            $this->title = $this->title;
        }
    }

    /**
     * @Groups({"event_browse", "event_read", "favorite_browse"})
     * @ORM\Column(type="string", length=255, options={"default"="event_placeholder.jpg"})
     */
    private $picture;

    /**
     * @Groups({"event_browse", "event_read", "favorite_browse", "favorite_read", "user_read"})
     * @ORM\Column(type="datetime_immutable")
     */
    private $date;

    /**
     * @Groups({"event_read"})
     * @ORM\Column(type="string", length=255, options={"default"="empty_address"})
     */
    private $address;

    /**
     * @Groups({"event_read"})
     * @ORM\Column(type="integer", options={"default"=0})
     */
    private $zipcode;

    /**
     * @Groups({"event_read", "event_browse", "favorite_browse"})
     * @ORM\Column(type="string", length=255, options={"default"="empty_city"})
     */
    private $city;

    /**
     * @Groups({"event_read"})
     * @ORM\Column(type="string", length=255, options={"default"="empty_country"})
     */
    private $country;

    /**
     * @Groups({"event_read"})
     * @ORM\Column(type="decimal", precision=8, scale=6, nullable=true)
     */
    private $latitude;

    /**
     * @Groups({"event_read"})
     * @ORM\Column(type="decimal", precision=9, scale=6, nullable=true)
     */
    private $longitude;

    /**
     * @Groups({"event_read"})
     * @ORM\Column(type="integer")
     */
    private $maxMembers;

    /**
     * @Groups({"event_browse", "event_read", "favorite_browse"})
     * @ORM\Column(type="boolean", options={"default"=false})
     */
    private $isArchived;

    /**
     * @Groups({"event_browse", "event_read", "favorite_browse"})
     * @ORM\Column(type="boolean")
     */
    private $isOnline;

    /**
     * @ORM\Column(type="datetime_immutable")
     */
    private $createdAt;

    /**
     * @ORM\Column(type="datetime_immutable", nullable=true)
     */
    private $updatedAt;

    /**
     * @Groups({"event_read", "event_browse", "favorite_browse"})
     * @ORM\ManyToOne(targetEntity=Category::class, inversedBy="events")
     * @ORM\JoinColumn(nullable=false)
     */
    private $category;

    /**
     * @Groups({"event_browse", "event_read"})
     * @ORM\ManyToOne(targetEntity=User::class, inversedBy="createdEvents")
     * @ORM\JoinColumn(nullable=false)
     */
    private $author;

    /**
     * @Groups({"event_read"})
     * @ORM\ManyToMany(targetEntity=User::class, mappedBy="joinedEvents")
     */
    private $members;

    public function __construct()
    {
        $this->members = new ArrayCollection();
        $this->toFavoriteUsers = new ArrayCollection();
        $this->date = new \DateTimeImmutable();
        $this->createdAt = new \DateTimeImmutable();
        $this->isArchived = false;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function setTitle(string $title): self
    {
        $this->title = $title;

        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(string $description): self
    {
        $this->description = $description;

        return $this;
    }

    public function getPicture(): ?string
    {
        return $this->picture;
    }

    public function setPicture(string $picture): self
    {
        $this->picture = $picture;

        return $this;
    }

    public function getDate(): ?\DateTimeImmutable
    {
        return $this->date;
    }

    public function setDate(\DateTimeImmutable $date): self
    {
        $this->date = $date;

        return $this;
    }

    public function getAddress(): ?string
    {
        return $this->address;
    }

    public function setAddress(string $address): self
    {
        $this->address = $address;

        return $this;
    }

    public function getZipcode(): ?string
    {
        return $this->zipcode;
    }

    public function setZipcode(string $zipcode): self
    {
        $this->zipcode = $zipcode;

        return $this;
    }

    public function getCity(): ?string
    {
        return $this->city;
    }

    public function setCity(string $city): self
    {
        $this->city = $city;

        return $this;
    }

    public function getCountry(): ?string
    {
        return $this->country;
    }

    public function setCountry(string $country): self
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

    public function getMaxMembers(): ?int
    {
        return $this->maxMembers;
    }

    public function setMaxMembers(int $maxMembers): self
    {
        $this->maxMembers = $maxMembers;

        return $this;
    }

    public function getIsArchived(): ?bool
    {
        return $this->isArchived;
    }

    public function setIsArchived(bool $isArchived): self
    {
        $this->isArchived = $isArchived;

        return $this;
    }

    public function getIsOnline(): ?bool
    {
        return $this->isOnline;
    }

    public function setIsOnline(bool $isOnline): self
    {
        $this->isOnline = $isOnline;

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

    public function getCategory(): ?Category
    {
        return $this->category;
    }

    public function setCategory(?Category $category): self
    {
        $this->category = $category;

        return $this;
    }

    public function getAuthor(): ?User
    {
        return $this->author;
    }

    public function setAuthor(?User $author): self
    {
        $this->author = $author;

        return $this;
    }

    /**
     * @return Collection|User[]
     */
    public function getMembers(): Collection
    {
        return $this->members;
    }

    public function addMember(User $member): self
    {
        if (!$this->members->contains($member)) {
            $this->members[] = $member;
            $member->addJoinedEvent($this);
        }

        return $this;
    }

    public function removeMember(User $member): self
    {
        if ($this->members->removeElement($member)) {
            $member->removeJoinedEvent($this);
        }

        return $this;
    }

    /**
     * @Groups({"event_browse", "event_read", "favorite_browse"})
     */
    public function getMembersCount()
    {
        return $this->members->count();
    }
}
