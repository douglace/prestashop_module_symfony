<?php
/**
 * Copyright since 2007 PrestaShop SA and Contributors
 * PrestaShop is an International Registered Trademark & Property of PrestaShop SA
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Academic Free License version 3.0
 * that is bundled with this package in the file LICENSE.md.
 * It is also available through the world-wide-web at this URL:
 * https://opensource.org/licenses/AFL-3.0
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@prestashop.com so we can send you a copy immediately.
 *
 * @author    PrestaShop SA and Contributors <contact@prestashop.com>
 * @copyright Since 2007 PrestaShop SA and Contributors
 * @license   https://opensource.org/licenses/AFL-3.0 Academic Free License version 3.0
 */

declare(strict_types=1);

namespace Yateo\Yatpush\Command;

final class AddPushCommand
{

    /**
     * @var int
     */
    private $position;

    /**
     * @var boolean
     */
    private $active;

    /**
     * @var string
     */
    private $type;

    /**
     * @var string[]
     */
    private $link;

    /**
     * @var string[]
     */
    private $textTop;

    /**
     * @var string[]
     */
    private $textBottom;

    /**
     * @var string[]
     */
    private $textButton;

    /**
     * Get the value of position
     */
    public function getPosition()
    {
        return $this->position;
    }

    /**
     * Set the value of position
     */
    public function setPosition($position): self
    {
        $this->position = $position;

        return $this;
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
}