{if isset($items) && !empty($items)}
    <section class="category">
        <h3>{l s='Nos catégories' d="Module.Yathomecategories.home"}</h3>
        <div class="row">
            {foreach from=$items item=item key=key name=name}
                <div class="col-4 col-md-4">
                    <img width="200px" src="{$item.image}">
                    <a href="{$item.link}" class="btn btn-primary">{$item.title}</a>
                </div>
            {/foreach}
        </div>
    </section>
{/if}
