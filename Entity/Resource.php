<?php

declare(strict_types=1);

/*
 * This file is part of the BkstgCoreBundle package.
 * (c) Luke Bainbridge <http://www.lukebainbridge.ca/>
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Bkstg\ResourceBundle\Entity;

use Bkstg\CoreBundle\Entity\Production;
use Bkstg\CoreBundle\Model\PublishableInterface;
use Bkstg\MediaBundle\Entity\Media;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use MidnightLuke\GroupSecurityBundle\Model\GroupableInterface;
use MidnightLuke\GroupSecurityBundle\Model\GroupInterface;

class Resource implements GroupableInterface, PublishableInterface
{
    private $id;
    private $name;
    private $description;
    private $pinned;
    private $active;
    private $published;
    private $author;
    private $created;
    private $updated;
    private $media;
    private $groups;

    /**
     * Create a new resource.
     */
    public function __construct()
    {
        $this->groups = new ArrayCollection();
    }

    /**
     * Get id.
     *
     * @return int
     */
    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * Get name.
     *
     * @return string
     */
    public function getName(): ?string
    {
        return $this->name;
    }

    /**
     * Set name.
     *
     * @param string $name The name.
     *
     * @return self
     */
    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    /**
     * Get description.
     *
     * @return string
     */
    public function getDescription(): ?string
    {
        return $this->description;
    }

    /**
     * Set description.
     *
     * @param string $description The description.
     *
     * @return self
     */
    public function setDescription(string $description): self
    {
        $this->description = $description;

        return $this;
    }

    /**
     * Get pinned.
     *
     * @return bool
     */
    public function getPinned(): ?bool
    {
        return $this->pinned;
    }

    /**
     * Set pinned.
     *
     * @param bool $pinned The pinned status.
     *
     * @return self
     */
    public function setPinned(bool $pinned): self
    {
        $this->pinned = $pinned;

        return $this;
    }

    /**
     * Get active.
     *
     * @return bool
     */
    public function isActive(): bool
    {
        return true === $this->active;
    }

    /**
     * Set active.
     *
     * @param bool $active The active state.
     *
     * @return self
     */
    public function setActive(bool $active): self
    {
        $this->active = $active;

        return $this;
    }

    /**
     * Get published.
     *
     * @return bool
     */
    public function isPublished(): bool
    {
        return true === $this->published;
    }

    /**
     * Set published.
     *
     * @param bool $published The published state.
     *
     * @return self
     */
    public function setPublished(bool $published): PublishableInterface
    {
        $this->published = $published;

        return $this;
    }

    /**
     * Get author.
     *
     * @return string
     */
    public function getAuthor(): ?string
    {
        return $this->author;
    }

    /**
     * Set author.
     *
     * @param string $author The author to set.
     *
     * @return self
     */
    public function setAuthor(string $author): self
    {
        $this->author = $author;

        return $this;
    }

    /**
     * Get created.
     *
     * @return \DateTimeInterface
     */
    public function getCreated(): ?\DateTimeInterface
    {
        return $this->created;
    }

    /**
     * Set created.
     *
     * @param \DateTimeInterface $created The created time.
     *
     * @return self
     */
    public function setCreated(\DateTimeInterface $created): self
    {
        $this->created = $created;

        return $this;
    }

    /**
     * Get updated.
     *
     * @return \DateTimeInterface
     */
    public function getUpdated(): ?\DateTimeInterface
    {
        return $this->updated;
    }

    /**
     * Set updated.
     *
     * @param \DateTimeInterface $updated The updated time.
     *
     * @return self
     */
    public function setUpdated(\DateTimeInterface $updated): self
    {
        $this->updated = $updated;

        return $this;
    }

    /**
     * Get media.
     *
     * @return Media
     */
    public function getMedia(): ?Media
    {
        return $this->media;
    }

    /**
     * Set media.
     *
     * @param Media $media The media.
     *
     * @return self
     */
    public function setMedia(Media $media): self
    {
        $this->media = $media;

        return $this;
    }

    /**
     * Add group.
     *
     * @param GroupInterface $group The group to set.
     *
     * @throws \Exception If the group is not a production.
     *
     * @return self
     */
    public function addGroup(GroupInterface $group): self
    {
        if (!$group instanceof Production) {
            throw new \Exception(sprintf('The group type "%s" is not supported.', get_class($group)));
        }
        $this->groups[] = $group;

        return $this;
    }

    /**
     * Remove group.
     *
     * @param GroupInterface $group The group to remove.
     *
     * @return self
     */
    public function removeGroup(GroupInterface $group): self
    {
        $this->groups->removeElement($group);

        return $this;
    }

    /**
     * Get groups.
     *
     * @return Collection
     */
    public function getGroups(): Collection
    {
        return $this->groups;
    }

    /**
     * {@inheritdoc}
     *
     * @param GroupInterface $group The group to check for.
     *
     * @return bool
     */
    public function hasGroup(GroupInterface $group): bool
    {
        return $this->groups->contains($group);
    }

    public function __toString()
    {
        return $this->name;
    }
}
