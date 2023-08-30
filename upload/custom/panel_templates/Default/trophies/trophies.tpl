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
                        <a href="{$NEW_TROPHY_LINK}" class="btn btn-primary"><i class="fa fa-plus-circle"></i> {$NEW_TROPHY}</a>
                        <hr />

                        <!-- Success and Error Alerts -->
                        {include file='includes/alerts.tpl'}

                        {if count($TROPHIES_LIST)}
                            <div class="table-responsive">
                                <table class="table table-borderless table-striped">
                                    <tbody>
                                    {foreach from=$TROPHIES_LIST item=trophy}
                                        <tr>
                                            <td width="64px"><img src="https://cdn.discordapp.com/attachments/772216990181621800/1118604055133040720/winner-trophy-in-flat-style-free-png.png" height="50px"></td>
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

    <!-- End Wrapper -->
</div>

{include file='scripts.tpl'}

<script type="text/javascript">
    function showDeleteModal(id) {
        $('#deleteId').attr('value', id);
        $('#deleteModal').modal().show();
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