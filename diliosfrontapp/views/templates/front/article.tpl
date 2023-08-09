{*
* 2007-2022 PrestaShop
*
* NOTICE OF LICENSE
*
* This source file is subject to the Academic Free License (AFL 3.0)
* that is bundled with this package in the file LICENSE.txt.
* It is also available through the world-wide-web at this URL:
* http://opensource.org/licenses/afl-3.0.php
* If you did not receive a copy of the license and are unable to
* obtain it through the world-wide-web, please send an email
* to license@prestashop.com so we can send you a copy immediately.
*
* DISCLAIMER
*
* Do not edit or add to this file if you wish to upgrade PrestaShop to newer
* versions in the future. If you wish to customize PrestaShop for your
* needs please refer to http://www.prestashop.com for more information.
*
*  @author    PrestaShop SA <contact@prestashop.com>
*  @copyright 2007-2022 PrestaShop SA
*  @license   http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
*  International Registered Trademark & Property of PrestaShop SA
*}

{extends file='page.tpl'}

{block name="page_title"}
    {l s='Mon article' mod='Modules.Diliosfrontapp.Articles'}
{/block}

{block name="page_content_container"}
    <div class="dilios-article">
        <div class="img">
            <img src="{$article_image}" alt="{$article->title}" />
        </div>
        <div class="article-content">
            <h1 class="article-title">
                {$article->title}
            </h1>
            <div class="article-description">
                {$article->description nofilter}
            </div>
        </div>
    </div>
{/block}