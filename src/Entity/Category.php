<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use App\Repository\CategoryRepository;
use Doctrine\Common\Collections\Collection;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\Serializer\Annotation\Groups;
use Vich\UploaderBundle\Mapping\Annotation as Vich;

/**
 * @ORM\Entity(repositoryClass=CategoryRepository::class)
 * @Vich\Uploadable
 */
class Category
{
    /**
     * @Groups({"category_browse", "event_read", "event_browse", "category_read", "favorite_browse"})
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @Groups({"category_browse", "event_read", "event_browse", "category_read", "favorite_browse"})
     * @ORM\Column(type="string", length=255)
     */
    private $name;

    /**
     * @Vich\UploadableField(mapping="category_image", fileNameProperty="picture")
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
           
            $this->updatedAt = new \DateTimeImmutable();
        }
    }

    /**
     * @Groups({"category_browse", "event_read", "category_read"})
     * @ORM\Column(type="string", length=255, options={"default"="category_placeholder.jpg"})
     */
    private $picture;

    /**
     * @ORM\OneToMany(targetEntity=Event::class, mappedBy="category")
     */
    private $events;

    /**
     * @ORM\Column(type="datetime_immutable", nullable=true)
     */
    private $updatedAt;

    public function __construct()
    {
        $this->events = new ArrayCollection();
    }

    public function __toString()
    {
        return $this->getName();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;

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

    /**
     * @return Collection|Event[]
     */
    public function getEvents(): Collection
    {
        return $this->events;
    }

    public function addEvent(Event $event): self
    {
        if (!$this->events->contains($event)) {
            $this->events[] = $event;
            $event->setCategory($this);
        }

        return $this;
    }

    public function removeEvent(Event $event): self
    {
        if ($this->events->removeElement($event)) {
            // set the owning side to null (unless already changed)
            if ($event->getCategory() === $this) {
                $event->setCategory(null);
            }
        }

        return $this;
    }

    /**
     * @Groups({"category_browse", "category_read"})
     */
    public function getEventsCount()
    {
        return $this->events->count();
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
}
