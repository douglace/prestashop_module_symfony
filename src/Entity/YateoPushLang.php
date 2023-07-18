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

use Doctrine\ORM\Mapping as ORM;
use PrestaShopBundle\Entity\Lang;

/**
 * @ORM\Table()
 * @ORM\Entity(repositoryClass="Yateo\Yatpush\Repository\PushRepository")
 */
class YateoPushLang
{
    /**
     * @ORM\Id
     * @ORM\ManyToOne(targetEntity="Yateo\Yatpush\Entity\YateoPush", inversedBy="pushLangs")
     * @ORM\JoinColumn(name="id_yateo_push", referencedColumnName="id_yateo_push", nullable=false)
     */
    private $push;

    /**
     * @ORM\Id
     * @ORM\ManyToOne(targetEntity="PrestaShopBundle\Entity\Lang")
     * @ORM\JoinColumn(name="id_lang", referencedColumnName="id_lang", nullable=false, onDelete="CASCADE")
     */
    private $lang;

    /**
     * @var string
     *
     * @ORM\Column(name="link", type="string", length=255)
     */
    private $link;

    /**
     * @var string
     *
     * @ORM\Column(name="text_top", type="string", length=200)
     */
    private $textTop;

    /**
     * @var string
     *
     * @ORM\Column(name="text_bottom", type="string", length=250)
     */
    private $textBottom;



    /**
     * @var string
     *
     * @ORM\Column(name="text_button", type="string", length=255)
     */
    private $textButton;


    /**
     * @return int
     */
    public function getId()
    {
        return $this->push;
    }

    /**
     * Set lang.
     *
     * @param \PrestaShopBundle\Entity\Lang $lang
     *
     * @return self
     */
    public function setLang(Lang $lang)
    {
        $this->lang = $lang;

        return $this;
    }

    /**
     * Get lang.
     *
     * @return \PrestaShopBundle\Entity\Lang
     */
    public function getLang()
    {
        return $this->lang;
    }

    /**
     * Get the value of textButton
     */
    public function getTextButton()
    {
        return $this->textButton;
    }

    /**
     * Set the value of textButton
     */
    public function setTextButton($textButton): self
    {
        $this->textButton = $textButton;

        return $this;
    }

    /**
     * Get the value of textBottom
     */
    public function getTextBottom()
    {
        return $this->textBottom;
    }

    /**
     * Set the value of textBottom
     */
    public function setTextBottom($textBottom): self
    {
        $this->textBottom = $textBottom;

        return $this;
    }

    /**
     * Get the value of textTop
     */
    public function getTextTop()
    {
        return $this->textTop;
    }

    /**
     * Set the value of textTop
     */
    public function setTextTop($textTop): self
    {
        $this->textTop = $textTop;

        return $this;
    }

    /**
     * Get the value of link
     */
    public function getLink()
    {
        return $this->link;
    }

    /**
     * Set the value of link
     */
    public function setLink($link): self
    {
        $this->link = $link;

        return $this;
    }

    /**
     * Get the value of push
     */
    public function getPush()
    {
        return $this->push;
    }

    /**
     * Set the value of push
     */
    public function setPush(YateoPush $push): self
    {
        $this->push = $push;

        return $this;
    }
}