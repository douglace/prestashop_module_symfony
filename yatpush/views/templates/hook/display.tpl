{if isset($items) && !empty($items)}
    <section class="push">
        <div class="row">
            {foreach from=$items item=item key=key name=name}
                <div class="col-4 col-md-4">
                    <img width="200px" src="{$item.image}">
                    <h3 class="item-text-top">{$item.textTop}</h3>
                    <span class="item-text-bottom">{$item.textBottom}</span>
                    <a href="{$item.link}" class="btn btn-primary">{$item.textButton}</a>
                </div>
            {/foreach}
        </div>
    </section>
{/if}
