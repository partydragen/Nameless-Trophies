{include file='header.tpl'}

<body id="page-top">

<!-- Wrapper -->
<div id="wrapper">

    <!-- Sidebar -->
    {include file='sidebar.tpl'}

    <!-- Content Wrapper -->
    <div id="content-wrapper" class="d-flex flex-column">

        <!-- Main content -->
        <div id="content">

            <!-- Topbar -->
            {include file='navbar.tpl'}

            <!-- Begin Page Content -->
            <div class="container-fluid">

                <!-- Page Heading -->
                <div class="d-sm-flex align-items-center justify-content-between mb-4">
                    <h1 class="h3 mb-0 text-gray-800">{$TROPHIES}</h1>
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="{$PANEL_INDEX}">{$DASHBOARD}</a></li>
                        <li class="breadcrumb-item active">{$TROPHIES}</li>
                    </ol>
                </div>

                <!-- Update Notification -->
                {include file='includes/update.tpl'}

                <div class="card shadow mb-4">
                    <div class="card-body">
                        <h5 style="display:inline">{$TROPHY_TITLE}</h5>
                        <div class="float-md-right">
                            <a href="{$BACK_LINK}" class="btn btn-primary">{$BACK}</a>
                        </div>

                        <hr />

                        <!-- Success and Error Alerts -->
                        {include file='includes/alerts.tpl'}

                        <form action="" method="post">

                            {assign var=counter value=1}
                            {foreach $FIELDS as $field_key => $field}
                                <div class="form-group">
                                    <label for="input{$field_key}">{$field.name}</label>
                                    {if $field.type eq 1}
                                        <input type="text" name="{$field_key}" class="form-control" id="input{$field_key}" value="{$field.value}" placeholder="{$field.placeholder}" tabindex="{$counter++}"{if $field.required} required{/if}>
                                    {else if $field.type eq 2}
                                        <textarea name="{$field_key}" class="form-control" id="{$field_key}" placeholder="{$field.placeholder}" tabindex="{$counter++}"></textarea>
                                    {else if $field.type eq 3}
                                        <input type="date" name="{$field_key}" class="form-control" id="{$field_key}" value="{$field.value}" tabindex="{$counter++}">
                                    {else if $field.type eq 4}
                                        <input type="password" name="{$field_key}" class="form-control" id="{$field_key}" value="{$field.value}" placeholder="{$field.placeholder}" tabindex="{$counter++}"{if $field.required} required{/if}>
                                    {else if $field.type eq 5}
                                        <select class="form-control" name="{$field_key}" id="{$field_key}" {if $field.required}required{/if}>
                                            {foreach from=$field.options item=option}
                                                <option value="{$option.value}" {if $option.value eq $field.value} selected{/if}>{$option.option}</option>
                                            {/foreach}
                                        </select>
                                    {else if $field.type eq 6}
                                        <input type="number" name="{$field_key}" class="form-control" id="{$field_key}" value="{$field.value}" placeholder="{$field.name}" tabindex="{$counter++}"{if $field.required} required{/if}>
                                    {else if $field.type eq 7}
                                        <input type="email" name="{$field_key}" class="form-control" id="{$field_key}" value="{$field.value}" placeholder="{$field.placeholder}" tabindex="{$counter++}"{if $field.required} required{/if}>
                                    {/if}
                                </div>
                            {/foreach}

                            <div class="form-group">
                                <label for="inputAddCredits">Reward any store credits for completion?</label>
                                <input type="number" class="form-control" id="inputAddCredits" name="add_credits" value="{$ADD_CREDITS_VALUE}" step="0.01" min="0.00" value="0.00">
                            </div>

                            <div class="form-group">
                                <label for="inputAddGroups">Reward any groups for completion?</label>
                                <select name="add_groups[]" id="inputAddGroups" class="form-control" multiple>
                                    {foreach from=$ALL_GROUPS item=item}
                                        <option value="{$item->id}"{if in_array($item->id, $ADD_GROUPS_VALUE)} selected{/if}>{$item->name|escape}</option>
                                    {/foreach}
                                </select>
                            </div>

                            <div class="form-group">
                                <input type="hidden" name="token" value="{$TOKEN}">
                                <input type="hidden" name="type" value="settings">
                                <input type="submit" class="btn btn-primary" value="{$SUBMIT}">
                            </div>
                        </form>
                        </br>

                        <center>
                            <p>Trophies Module by <a href="https://partydragen.com/" target="_blank">Partydragen</a> and my <a href="https://partydragen.com/supporters/" target="_blank">Sponsors</a></br>
                                <a class="ml-1" href="https://partydragen.com/suggestions/" target="_blank" data-toggle="tooltip"
                                   data-placement="top" title="You can submit suggestions here"><i class="fa-solid fa-thumbs-up text-warning"></i></a>
                                <a class="ml-1" href="https://discord.gg/TtH6tpp" target="_blank" data-toggle="tooltip"
                                   data-placement="top" title="Discord"><i class="fab fa-discord fa-fw text-discord"></i></a>
                                <a class="ml-1" href="https://partydragen.com/" target="_blank" data-toggle="tooltip"
                                   data-placement="top" title="Website"><i class="fas fa-globe fa-fw text-primary"></i></a>
                                <a class="ml-1" href="https://www.patreon.com/partydragen" target="_blank" data-toggle="tooltip"
                                   data-placement="top" title="Support the development on Patreon"><i class="fas fa-heart fa-fw text-danger"></i></a>
                            </p>
                        </center>

                    </div>
                </div>

                <!-- Spacing -->
                <div style="height:1rem;"></div>

                <!-- End Page Content -->
            </div>

            <!-- End Main Content -->
        </div>

        {include file='footer.tpl'}

        <!-- End Content Wrapper -->
    </div>

    <!-- End Wrapper -->
</div>

{include file='scripts.tpl'}

</body>

</html>