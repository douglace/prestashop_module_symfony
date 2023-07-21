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

namespace Yateo\Yathomecategories\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Table()
 * @ORM\Entity(repositoryClass="Yateo\Yathomecategories\Repository\CategoryRepository")
 */
class YateoHomeCategories
{
    /**
     * @var int
     *
     * @ORM\Id
     * @ORM\Column(name="id_yateo_home_categories", type="integer")
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
     * @var string
     *
     * @ORM\Column(name="imagetimes", type="string", length=20)
     */
    private $imagetimes;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="date_upd", type="datetime")
     */
    private $dateUpd;

    /**
     * @ORM\OneToMany(targetEntity="Yateo\Yathomecategories\Entity\YateoHomeCategoriesLang", cascade={"persist", "remove"}, mappedBy="category")
     */
    private $categoryLangs;

    /**
     * Constructor.
     */
    public function __construct()
    {
        $this->categoryLangs = new ArrayCollection();
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
     * Set dateUpd.
     *
     * @param \DateTime $dateUpd
     *
     * @return self
     */
    public function setDateUpd($dateUpd)
    {
        $this->dateUpd = $dateUpd;

        return $this;
    }

    /**
     * Get dateUpd.
     *
     * @return \DateTime
     */
    public function getDateUpd()
    {
        return $this->dateUpd;
    }

    /**
     * Get the value of categoryLangs
     */
    public function getCategoryLangs()
    {
        return $this->categoryLangs;
    }

    /**
     * Set the value of categoryLangs
     */
    public function addCategoryLangs(YateoHomeCategoriesLang $categoryLangs): self
    {
        $this->categoryLangs[] = $categoryLangs;
        $categoryLangs->setCategory($this);

        return $this;
    }

    public function removeAttributeLang(YateoHomeCategoriesLang $attributeLang)
    {
        $this->categoryLangs->removeElement($attributeLang);
    }

    /**
     * Get the value of imagetimes
     */
    public function getImagetimes()
    {
        return $this->imagetimes;
    }

    /**
     * Set the value of imagetimes
     */
    public function setImagetimes(string $imagetimes): self
    {
        $this->imagetimes = $imagetimes;

        return $this;
    }
}