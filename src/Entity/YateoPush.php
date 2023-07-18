<?php
/**
 * 2007-2020 PrestaShop
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Academic Free License 3.0 (AFL-3.0).
 * It is also available through the world-wide-web at this URL: https://opensource.org/licenses/AFL-3.0
 */

declare(strict_types=1);

namespace Yateo\Yatpush\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Table()
 * @ORM\Entity(repositoryClass="Yateo\Yatpush\Repository\PushRepository")
 */
class YateoPush
{
    /**
     * @var int
     *
     * @ORM\Id
     * @ORM\Column(name="id_yateo_push", type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;


    /**
     * @ORM\Column(name="position", type="integer")
     */
    private $position;


    /**
     * @ORM\Column(name="active", type="integer")
     */
    private $active;

    /**
     * @var string
     *
     * @ORM\Column(type="string", columnDefinition="ENUM('MAN', 'WOMAN')")
     */
    private $type;

    /**
     * @ORM\OneToMany(targetEntity="Yateo\Yatpush\Entity\YateoPushLang", cascade={"persist", "remove"}, mappedBy="push")
     */
    private $pushLangs;

    /**
     * Constructor.
     */
    public function __construct()
    {
        $this->pushLangs = new ArrayCollection();
    }

    /**
     * @return int
     */
    public function getId(): int
    {
        return $this->id;
    }

    /**
     * @param int $id
     */
    public function setId(int $id): void
    {
        $this->id = $id;
    }

    /**
     * @return int
     */
    public function getPosition(): int
    {
        return $this->position;
    }

    /**
     * @param int $position
     */
    public function setPosition(int $position): void
    {
        $this->position = $position;
    }

    /**
     * Get the value of active
     */
    public function getActive()
    {
        return $this->active;
    }

    /**
     * Set the value of active
     */
    public function setActive($active): self
    {
        $this->active = $active;

        return $this;
    }

    /**
     * Get the value of type
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * Set the value of type
     */
    public function setType($type): self
    {
        $this->type = $type;

        return $this;
    }

    /**
     * Get the value of pushLangs
     */
    public function getPushLangs()
    {
        return $this->pushLangs;
    }

    /**
     * Set the value of pushLangs
     */
    public function addPushLangs(YateoPushLang $pushLangs): self
    {
        $this->pushLangs[] = $pushLangs;
        $pushLangs->setPush($this);

        return $this;
    }

    public function removeAttributeLang(YateoPushLang $attributeLang)
    {
        $this->pushLangs->removeElement($attributeLang);
    }
}