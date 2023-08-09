{if isset($title) && isset($key)}
    <div class="dfc-{$key}">

        <div class="m-b-1 m-t-1">
            <h2>{$title}</h2>
            
            <fieldset class="form-group">
                <div class="col-lg-12 col-xl-4">
                    {*Champ Standard *}
                    <label class="form-control-label">{l s='DFC FIELD TEXT' mod='Module.Diliosfieldprod.Adminproduct'}</label>
                    <input type="text" name="dfc_field_{$key}_text" class="form-control" {if isset($field_simple_text) && $field_simple_text != ''}value="{$field_simple_text}"{/if}/>
                
                    {* Champ langue avec une structure particulière *}
                    <label class="form-control-label">{l s='DFC FIELD TEXT LANG' mod='Module.Diliosfieldprod.Adminproduct'}</label>
                    <div class="translations tabbable">
                        <div class="translationsFields tab-content">
                            {foreach from=$languages item=language }
                                <div class="tab-pane translation-label-{$language.iso_code} {if $default_language == $language.id_lang}active{/if}">
                                    <input type="text" name="dfc_field_{$key}_text_lang_{$language.id_lang}" class="form-control" {if isset($field_simple_text_lang) && isset({$field_simple_text_lang[$language.id_lang]}) && {$field_simple_text_lang[$language.id_lang]} != ''}value="{$field_simple_text_lang[$language.id_lang]}"{/if}/>
                                </div>
                            {/foreach}
                        </div>
                    </div>
                </div>
            
                {* Champ wysiwyg avec TinyMce *}
                <div class="col-lg-12 col-xl-12">
                    <label class="form-control-label">{l s='DFC FIELD TEXT LANG WYSIWYG' mod='Module.Diliosfieldprod.Adminproduct'}</label>
                    <div class="translations tabbable">
                        <div class="translationsFields tab-content bordered">
                            {foreach from=$languages item=language }
                            <div class="tab-pane translation-label-{$language.iso_code} {if $default_language == $language.id_lang}active{/if}">
                                <textarea name="dfc_field_{$key}_text_lang_wysiwyg_{$language.id_lang}" class="autoload_rte">{if isset($custom_field_lang_wysiwyg) && isset({$custom_field_lang_wysiwyg[$language.id_lang]}) && {$custom_field_lang_wysiwyg[$language.id_lang]} != ''}{$custom_field_lang_wysiwyg[$language.id_lang]}{/if}</textarea>
                            </div>
                            {/foreach}
                        </div>
                    </div>
                </div>
            </fieldset>
            
            <div class="clearfix"></div>
        </div>

    </div>
{/if}
