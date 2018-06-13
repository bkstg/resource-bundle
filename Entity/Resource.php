<?php

namespace Bkstg\ResourceBundle\Entity;

use Bkstg\CoreBundle\Entity\Production;
use Bkstg\MediaBundle\Entity\Media;
use Doctrine\Common\Collections\ArrayCollection;
use MidnightLuke\GroupSecurityBundle\Model\GroupInterface;
use MidnightLuke\GroupSecurityBundle\Model\GroupableInterface;

class Resource implements GroupableInterface
{
    private $id;
    private $name;
    private $description;
    private $pinned;
    private $status;
    private $author;
    private $created;
    private $updated;
    private $media;
    private $groups;

    public function __construct()
    {
        $this->groups = new ArrayCollection();
    }

    /**
     * Get id
     *
     * @return integer
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Get name
     * @return
     */
    public function getName(): ?string
    {
        return $this->name;
    }

    /**
     * Set name
     * @return $this
     */
    public function setName(string $name): self
    {
        $this->name = $name;
        return $this;
    }

    /**
     * Get description
     * @return
     */
    public function getDescription(): ?string
    {
        return $this->description;
    }

    /**
     * Set description
     * @return $this
     */
    public function setDescription(string $description): self
    {
        $this->description = $description;
        return $this;
    }

    /**
     * Get pinned
     * @return
     */
    public function getPinned(): ?bool
    {
        return $this->pinned;
    }

    /**
     * Set pinned
     * @return $this
     */
    public function setPinned(bool $pinned): self
    {
        $this->pinned = $pinned;
        return $this;
    }

    /**
     * Get status
     * @return
     */
    public function getStatus(): ?bool
    {
        return $this->status;
    }

    /**
     * Set status
     * @return $this
     */
    public function setStatus(bool $status): self
    {
        $this->status = $status;
        return $this;
    }

    /**
     * Get author
     * @return
     */
    public function getAuthor(): ?string
    {
        return $this->author;
    }

    /**
     * Set author
     * @return $this
     */
    public function setAuthor(string $author): self
    {
        $this->author = $author;
        return $this;
    }

    /**
     * Get created
     * @return
     */
    public function getCreated(): ?\DateTimeInterface
    {
        return $this->created;
    }

    /**
     * Set created
     * @return $this
     */
    public function setCreated(\DateTimeInterface $created): self
    {
        $this->created = $created;
        return $this;
    }

    /**
     * Get updated
     * @return
     */
    public function getUpdated(): ?\DateTimeInterface
    {
        return $this->updated;
    }

    /**
     * Set updated
     * @return $this
     */
    public function setUpdated(\DateTimeInterface $updated): self
    {
        $this->updated = $updated;
        return $this;
    }

    /**
     * Get media
     * @return
     */
    public function getMedia(): ?Media
    {
        return $this->media;
    }

    /**
     * Set media
     * @return $this
     */
    public function setMedia(Media $media): self
    {
        $this->media = $media;
        return $this;
    }

    /**
     * Add group
     *
     * @param Production $group
     *
     * @return Post
     */
    public function addGroup(GroupInterface $group): self
    {
        if (!$group instanceof Production) {
            throw new \Exception('Group type not supported.');
        }
        $this->groups[] = $group;

        return $this;
    }

    /**
     * Remove group
     *
     * @param Production $group
     */
    public function removeGroup(GroupInterface $group): self
    {
        if (!$group instanceof Production) {
            throw new \Exception('Group type not supported.');
        }
        $this->groups->removeElement($group);
    }

    /**
     * Get groups
     *
     * @return Collection
     */
    public function getGroups()
    {
        return $this->groups;
    }

    /**
     * {@inheritdoc}
     */
    public function hasGroup(GroupInterface $group): bool
    {
        foreach ($this->groups as $my_group) {
            if ($group->isEqualTo($my_group)) {
                return true;
            }
        }
        return false;
    }
}
