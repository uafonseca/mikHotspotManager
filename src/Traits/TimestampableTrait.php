<?php

namespace App\Traits;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;

/**
 * Adds created at and updated at timestamps to entities.
 * Entities using this must have HasLifecycleCallbacks annotation.
 *
 * @ORM\HasLifecycleCallbacks
 */
trait TimestampableTrait
{
	/**
	 * @var \DateTime
	 *
	 * @ORM\Column(name="created_at", type="datetime", nullable = true)
	 * @Gedmo\Timestampable(on="create")
	 */
	private $createdAt;

	/**
	 * @var \DateTime
	 *
	 * @ORM\Column(name="updated_at", type="datetime", nullable = true)
	 * @Gedmo\Timestampable(on="update")
	 */
	private $updatedAt;

	/**
	 * Gets triggered only on insert
	 *
	 * @ORM\PrePersist
	 */
	public function onPrePersist()
	{
		$this->createdAt = new \DateTime();
		$this->updatedAt = new \DateTime();
	}

	/**
	 * Gets triggered every time on update
	 *
	 * @ORM\PreUpdate
	 */
	public function onPreUpdate()
	{
		$this->updatedAt = new \DateTime();
	}

	/**
	 * Set createdAt
	 *
	 * @param \DateTime $createdAt
	 */
	public function setCreatedAt($createdAt)
	{
		$this->createdAt = $createdAt;

		return $this;
	}

	/**
	 * Get createdAt
	 *
	 * @return \DateTime
	 */
	public function getCreatedAt()
	{
		return $this->createdAt;
	}

	/**
	 * Set updatedAt
	 *
	 * @param \DateTime $updatedAt
	 */
	public function setUpdatedAt($updatedAt)
	{
		$this->updatedAt = $updatedAt;

		return $this;
	}

	/**
	 * Get updatedAt
	 *
	 * @return \DateTime
	 */
	public function getUpdatedAt()
	{
		return $this->updatedAt;
	}
}
