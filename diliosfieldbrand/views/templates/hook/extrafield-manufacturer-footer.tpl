<div class="dfc-extra-field-footer">
    {if isset($imagelink) && $imagelink}
        <img style="max-width: 100%" class="dfc-image" src="{$imagelink}"/>
    {/if}
    {if isset($title) && $title}
        <p class="dfc-title">{$title}</p>
    {/if}
    {if isset($description) && $description}
        <div class="extra-description">{$description|cleanHtml nofilter}</div>
    {/if}
</div>