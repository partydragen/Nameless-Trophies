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
                        <a href="{$NEW_TROPHY_LINK}" class="btn btn-primary"><i class="fa fa-plus-circle"></i> {$NEW_TROPHY}</a> <a onclick="showRewardModal()" class="btn btn-primary"><i class="fa fa-plus-circle"></i> Reward Trophy</a>
                        <hr />

                        <!-- Success and Error Alerts -->
                        {include file='includes/alerts.tpl'}

                        {if count($TROPHIES_LIST)}
                            <div class="table-responsive">
                                <table class="table table-borderless table-striped">
                                    <tbody>
                                    {foreach from=$TROPHIES_LIST item=trophy}
                                        <tr>
                                            <td style="width:45px"><img src="{$trophy.image}" style="height:45px; width:45px;"></td>
                                            <td>
                                                <a href="{$trophy.edit_link}">{$trophy.title}</a>
                                                <br />{$trophy.description}
                                            </td>
                                            <td>
                                                <div class="float-md-right">
                                                    <a href="{$trophy.edit_link}" class="btn btn-warning btn-sm"><i
                                                                class="fas fa-edit fa-fw"></i></a>
                                                    <button class="btn btn-danger btn-sm" type="button"
                                                            onclick="showDeleteModal('{$trophy.id}')"><i
                                                                class="fas fa-trash fa-fw"></i></button>
                                                </div>
                                            </td>
                                        </tr>
                                    {/foreach}
                                    </tbody>
                                </table>
                            </div>
                        {else}
                            {$NO_HOOKS}
                        {/if}

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

    <div class="modal fade" id="deleteModal" tabindex="-1" role="dialog">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">{$ARE_YOU_SURE}</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    {$CONFIRM_DELETE_TROPHY}
                </div>
                <div class="modal-footer">
                    <input type="hidden" id="deleteId" value="">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">{$NO}</button>
                    <button type="button" onclick="deleteTrophy()" class="btn btn-primary">{$YES}</button>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="rewardModal" tabindex="-1" role="dialog">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Reward Trophy to user</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <form action="" method="post" id="reward-trophy">
                        <div class="form-group">
                            <label for="inputUsername">Username</label>
                            <input type="text" name="username" class="form-control" id="inputUsername" placeholder="Username">
                        </div>
                        <div class="form-group">
                            <label for="inputTrophy">Trophy</label>
                            <select name="trophy" id="inputTrophy" class="form-control">
                                <option value="0">Select Trophy</option>
                                {foreach from=$TROPHIES_LIST item=trophy}
                                    <option value="{$trophy.id}">{$trophy.title}</option>
                                {/foreach}
                            </select>
                        </div>
                        <input type="hidden" name="token" value="{$TOKEN}">
                        <input type="hidden" name="type" value="reward_trophy">
                    </form>
                </div>
                <div class="modal-footer">
                    <input type="hidden" id="deleteId" value="">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                    <button type="button" onclick="$('#reward-trophy').submit();" class="btn btn-primary">Give Trophy</button>
                </div>
            </div>
        </div>
    </div>

    <!-- End Wrapper -->
</div>

{include file='scripts.tpl'}

<script type="text/javascript">
    function showDeleteModal(id) {
        $('#deleteId').attr('value', id);
        $('#deleteModal').modal().show();
    }

    function showRewardModal() {
        $('#rewardModal').modal().show();
    }

    function deleteTrophy() {
        const id = $('#deleteId').attr('value');
        if (id) {
            const response = $.post("{$DELETE_LINK}", { id, action: 'delete', token: "{$TOKEN}" });
            response.done(function() { window.location.reload(); });
        }
    }
</script>

</body>

</html>