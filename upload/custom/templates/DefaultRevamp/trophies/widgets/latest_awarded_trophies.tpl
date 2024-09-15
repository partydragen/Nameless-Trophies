<div class="ui fluid card" id="widget-latest-posts">
    <div class="content">
        <h4 class="ui header">{$LATEST_REWARDED_TROPHIES}</h4>
        <div class="description">
            {foreach from=$LATEST_REWARDED_TROPHIES_LIST item=trophy}
                <div class="ui relaxed list">
                    <div class="item">
                        <img class="ui mini image" src="{$trophy.trophy_image}" style="aspect-ratio: 1 / 1;"
                             alt="{$trophy.trophy_title}">
                        <div class="content">
                            <a class="header" data-toggle="popup"
                               data-position="top left">{$trophy.trophy_title}</a>
                            <div class="ui wide popup">
                                <h4 class="ui header">{$trophy.trophy_title}</h4>
                                {$trophy.trophy_description}
                            </div>
                            <a href="{$trophy.user_profile}" style="{$trophy.user_style}"
                               data-poload="{$USER_INFO_URL}{$trophy.user_id}">{$trophy.username}</a>
                            &middot; <span data-toggle="tooltip"
                                           data-content="{$trophy.trophy_received_full}">{$trophy.trophy_received_friendly}</span>
                        </div>
                    </div>
                </div>
                {foreachelse}
                {$NO_TROPHIES_REWARDED}
            {/foreach}
        </div>
    </div>
</div>