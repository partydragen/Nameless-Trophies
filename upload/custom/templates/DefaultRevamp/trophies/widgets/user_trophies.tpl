<div class="ui segments">
    <div class="ui segment header">{$TROPHIES_TITLE}</div>
    <div class="ui segment">
        {if isset($TROPHIES) && count($TROPHIES)}
            {foreach from=$TROPHIES item=trophy}
                <img class="ui avatar image" data-toggle="tooltip" data-title="{$trophy.title}" data-content="{$trophy.description} ({$trophy.awarded_date})" data-footer="test" style="max-height:30px; max-width:30px;" src="{$trophy.image}" />
            {/foreach}
        {else}
            <p>{$NONE_TROPHIES}</p>
        {/if}
    </div>
</div>