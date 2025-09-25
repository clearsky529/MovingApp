@extends('theme.company-admin.layouts.main')
@section('title', 'Moves')
@section('page-style')
    <link rel="stylesheet"
          href="{{asset('backend/assets/bower_components/datatables.net-bs/css/dataTables.bootstrap.min.css')}}">
    <link rel="stylesheet" href="https://lipis.github.io/bootstrap-sweetalert/dist/sweetalert.css"/>
@stop
@section('content')

    <section class="content-header">
        <h1>Manage Moves </h1>
        <ol class="breadcrumb">
            <li><a href="{{url('company-admin/home')}}"><i class="fa fa-dashboard"></i>@lang('auth.home')</a></li>
            <li class="active">Manage Moves</li>
        </ol>
    </section>
    @if(Session::has('flash_message_success'))
        <div class="col-md-12" style="margin-top: 10px;">
            <div class="alert alert-success alert-block" style="text-align: center">
                <button type="button" class="close" data-dismiss="alert">×</button>
                <strong>{!! session('flash_message_success') !!}</strong>
            </div>
        </div>
    @endif
    @if(Session::has('flash_message_error'))
        <div class="col-md-12" style="margin-top: 10px;">
            <div class="alert alert-danger alert-block">
                <button type="button" class="close" data-dismiss="alert">×</button>
                <strong>{!! session('flash_message_success') !!}</strong>
            </div>
        </div>
    @endif
    <section class="content">
        <div class="row">
            <div class="col-lg-12">
                <div class="box box-primary">
                    <div class="box-header with-border">
                        <a type="button" style="float: right; width: 150px;"
                           href="{{ route('company-admin.move.create-uplift') }}"
                           class="btn btn-success btn-sm pull-right">Create A Move</a>
                    </div>

                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-lg-12">
                <div class="nav-tabs-custom">
                    <ul class="nav nav-tabs move_nav_tabs_ul" id="myTab">
                        <li class="reloadTable active">
                            <a href="#uplift" data-toggle="tab" onclick="activeMoveTab('uplift')"> <b> Uplift </b></a>
                        </li>
                        <!-- <li class=reloadTable>
                         <a href="#tab_2" data-toggle="tab"> <b> Transit </b> </a>
                        </li> -->
                        <li class="reloadTable">
                            <a href="#delivery" data-toggle="tab" onclick="activeMoveTab('delivery')"> <b> Delivery </b></a>
                        </li>
                        <!-- start code by ss_24_aug -->
                        @if($userId->kika_direct == 0)
                            <li class="reloadTable">
                                <a href="#transload" data-toggle="tab" hidden="true"
                                   onclick="activeMoveTab('transload')"> <b>
                                        Tranship </b></a>
                            </li>
                            <li class="reloadTable">
                                <a href="#screening" data-toggle="tab" onclick="activeMoveTab('screening')"> <b>
                                        Screen </b></a>
                            </li>
                        @endif
                        <!-- end code by ss_24_aug -->
                        <li class="pull-right hide-completed">
                            <div class="form-group">
                                <label>
                                    <input type="checkbox" name="r1" id="toggleComplete" class="minimal v-align-top">
                                    Hide Completed
                                </label>
                            </div>
                        </li>
                    </ul>
                    <div class="tab-content" style="margin-bottom: 10rem !important">
                        <div class="table-responsive">
                            <table class="table table-bordered data-table" style="width: 100%">
                                <thead>
                                <tr>
                                    <th>Status</th>
                                    <th>Date</th>
                                    <th>Move Number</th>
                                    <th>Customer</th>
                                    <th>Agent</th>
                                    <th>Controlling Agent</th>
                                    <th>Volume</th>
                                    <th>Action</th>
                                </tr>
                                </thead>
                                <tbody>
                                </tbody>
                            </table>

                        </div>

                    </div>
                </div>
            </div>
        </div>
    </section>

    <div class="modal fade" id="modal-default">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <input type="hidden" name="move_id" class="hidden-move-id">
                    <input type="hidden" name="move_type" class="move-type">
                    <div class="form-group">
                        <label for="name" class="control-label">Status</label>
                        <select name="status" id="status" class="form-control">
                            <option value="0">Pending</option>
                            <option value="1">In Progress</option>
                            <option value="2">Completed</option>
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default pull-left" data-dismiss="modal">Close</button>
                    <button type="button" class="btn btn-primary submit-status">Save</button>
                </div>
            </div>
        </div>
    </div>

    {{-- model in_prg_move --}}
    <div class="modal fade delete-modal" id="inprg_delete_move">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-body">
                    <input type="hidden" id="delete_inprg_move_id" name="delete_inprg_move_id" class="hidden-move-id">
                    <div class="form-group">
                        <label for="name" class="control-label delete-title">To continue with the deletion of this job
                            the status of all sections need to be set at Completed</label>
                    </div>
                    <div class="delete-btns">
                        <button type="button" class="btn btn-success btn-sm" data-dismiss="modal">OK</button>
                    </div>
                </div>

            </div>
        </div>
    </div>
    {{-- close model --}}

    {{-- model completed_move --}}
    <div class="modal fade delete-modal" id="delete_move">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-body">
                    <input type="hidden" id="delete_move_id" name="delete_move_id" class="hidden-move-id">
                    <div class="form-group">
                        <label for="name" class="control-label delete-title">Do you want to delete this move ?</label>
                    </div>
                    <div class="delete-btns">
                        <button type="button" class="btn btn-success btn-sm submit-id" id="delete">Delete Move</button>
                        <button type="button" class="btn btn-danger btn-sm" data-dismiss="modal">Cancel</button>
                    </div>
                </div>

            </div>
        </div>
    </div>
    {{-- close model --}}

    {{-- model completed_move --}}
    <div class="modal fade delete-modal" id="delivery_icr">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-body modal-body-space">
                    <form id="frm-email-delivery-icr" name="frm-email-delivery-icr" method="post">
                        @csrf
                        <input type="hidden" id="delivery_icr_move_id" name="delivery_icr_move_id"
                               class="delivery_icr_move_id">
                        <div class="form-group">
                            <label for="name" class="control-label delete-title">Email Delivery ICR To Agent</label>
                            <input type="email" name="delivery_icr_mail" placeholder="Agent's Email Address"
                                   class="email_icr_input" required>
                        </div>
                        <div class="cancal-btns" style="display: flex;">
                            <button type="button" class="btn btn-danger btn-sm" data-dismiss="modal">Cancel</button>
                            <button type="submit" class="btn btn-success btn-sm send-icr-id" style="width: 48%;"
                                    id="send_icr">Send ICR
                            </button>
                        </div>
                    </form>
                </div>

            </div>
        </div>
    </div>
    {{-- close model --}}


    {{-- delete model completed_move --}}
    <div class="modal fade delete-modal" id="delete_moveId">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-body">
                    <input type="hidden" id="delete_inprg_move_id" name="delete_inprg_move_id" class="hidden-move-id">
                    <div class="form-group">
                        <label for="name" class="control-label delete-title">Are you sure you want to delete this move ?
                            This action cannot be undone.</label>
                    </div>
                    <div class="delete-btns">
                        <button type="button" class="btn btn-success btn-sm submit-id" id="delete_id">Delete Move
                        </button>
                        <a href="#" class="btn btn-danger btn-sm" data-dismiss="modal">Cancel</a>
                    </div>
                </div>

            </div>
        </div>
    </div>
    {{-- close model --}}

    {{-- model unarchive-move --}}
    <div class="modal fade delete-modal" id="unarchive_move">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-body">
                    <input type="hidden" id="unarchive_inprg_move_id" name="unarchive_inprg_move_id"
                           class="hidden-move-id">
                    <div class="form-group">
                        <label for="name" class="control-label delete-title">Please ensure all tasks related to this job
                            are either 'Completed' or 'Pending'.</label>
                    </div>
                    <div class="delete-btns">
                        <button type="button" class="btn btn-success btn-sm" data-dismiss="modal">OK</button>
                    </div>
                </div>

            </div>
        </div>
    </div>
    {{-- close model --}}

@endsection
@section('page-script')
    <script src="{{asset('backend/assets/bower_components/datatables.net/js/jquery.dataTables.min.js') }}"></script>
    <script
        src="{{asset('backend/assets/bower_components/datatables.net-bs/js/dataTables.bootstrap.min.js') }}"></script>
    <script src="https://lipis.github.io/bootstrap-sweetalert/dist/sweetalert.js"></script>

    <script>

        $('.table-responsive').on('show.bs.dropdown', function () {
            $('.table-responsive').css("overflow", "inherit");
        });

        $(function () {

            window.activeMoveTab = function (tab) {
                console.log("Tab changed to: " + tab);
                sessionStorage.setItem("activeMoveTab", tab);
                table.ajax.reload();
            };

            var table = $('.data-table').DataTable({
                processing: true,
                serverSide: true,
                "paging": true,
                "pageLength": 10,
                "lengthMenu": [10, 25, 50, 100],
                "order": [[1, 'desc']],
                language: {
                    infoFiltered: ""
                },
                ajax: {
                    url: "{{ route('company-admin.move') }}",
                    type: 'GET',
                    data: function (d) {

                        let status = sessionStorage.getItem("is_completed");
                        if (!status) {
                            status = $('#toggleComplete').is(':checked') ? 'completed' : false;
                        }

                        let activeTabFilter = sessionStorage.getItem("activeMoveTab");
                        if (!activeTabFilter) {
                            activeTabFilter = 'uplift';
                        }
                        d.activeTabFilter = activeTabFilter;
                        d.statusFilter = status;
                        console.log("data", d);
                    }
                },
                columns: [
                    {data: 'status', name: 'status'},
                    {data: 'move_date', name: 'move_date'},
                    {data: 'move_number', name: 'move_number'},
                    {data: 'contact', name: 'contact'},
                    {data: 'origin_agent', name: 'origin_agent'},
                    {data: 'controlling_agent', name: 'controlling_agent'},
                    {data: 'volume', name: 'volume'},
                    {data: 'action', name: 'action', orderable: false, searchable: false},
                ]
            });


            $('input[type="checkbox"]').click(function () {
                if ($(this).prop("checked") == true) {
                    sessionStorage.setItem("is_completed", 'completed');
                    table.ajax.reload();
                    console.log(sessionStorage.getItem("is_completed"));
                } else if ($(this).prop("checked") == false) {
                    sessionStorage.removeItem("is_completed");
                    table.ajax.reload();
                    console.log(sessionStorage.getItem("is_completed"));
                }
            });


            $('.submit-status').on("click", function () {
                var status = $('#status').val();
                var type = $('.move-type').val();
                var move_id = $('.hidden-move-id').val();
                $.ajax({
                    type: 'POST',
                    url: '{{ route("company-admin.move.change-status") }}',
                    data: {
                        'move_id': move_id,
                        'status': status,
                        'type': type,
                        '_token': "{{ csrf_token() }}"
                    },
                    success: function (redirect_tab) {
                        console.log(redirect_tab);
                        $("#modal-default").modal("hide");
                        {{--window.location.replace("{{ route('company-admin.move') }}/" + redirect_tab)--}}
                        table.ajax.reload();
                    }
                });
            });


            $('#delete_id').on("click", function () {
                var spinner = '<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span><span style="margin-left: 10px;">Loading...</span>';
                $('#delete_moveId .submit-id').html(spinner).attr('disabled', true);
                var id = $('#delete_move_id').val();

                // $('#delete_moveId').modal('show');
                $.ajax({
                    type: 'get',
                    url: "{{ url('company-admin/move/delete/') }}" + '/' + id,

                    success: function () {
                        $('#delete_moveId').modal('hide');
                        swal("Your move deleted successfully!");
                        // window.location.reload();
                        table.ajax.reload();
                    }
                });
            });

            function openArchiveMoveModel(id) {
                var move_id = id;
                // alert(id);
                $.ajax({
                    type: 'post',
                    dataType: "json",
                    url: "{{ url('company-admin/move/archive-move/') }}",
                    data: {
                        'move_id': move_id,
                        '_token': "{{ csrf_token() }}"
                    },
                    success: function (data) {
                        if (data == 1) {
                            swal("Move Archive Sucessfully!");
                            // window.location.reload();
                            table.ajax.reload();
                        } else {
                            swal('Opps! Something went wrong.');
                        }

                    }
                });

            }

            // Submit popup send mail delivery icr
            $('#frm-email-delivery-icr').on("submit", function (e) {
                e.preventDefault();
                var form_data = $(this).serialize();
                console.log("form")
                // $('#delete_moveId').modal('show');
                $.ajax({
                    type: 'post',
                    url: "{{ url('company-admin/move/send-email-delivery-icr') }}",
                    data: form_data,
                    beforeSend: function () {
                        console.log("beforeSend","1111");
                        $('#delivery_icr').modal('hide');
                        swal({
                            title: 'Creating and sending the Delivery ICR',
                            html: true,
                            showConfirmButton: false,
                            text: '<i class="fa fa-spinner fa-spin" style="font-size: 40px;margin-top: 15px;"></i>',
                            allowOutsideClick: false,
                            allowEscapeKey: false,
                            allowEnterKey: false,
                        });
                    },
                    success: function (response) {
                        var obj = JSON.parse(response);
                        console.log("success", obj);
                        if (obj['status'] == 1) {
                            $('#delivery_icr').modal('hide');
                            swal(obj['message']);
                            table.ajax.reload();
                            // window.location.reload();
                        } else {
                            swal(obj['message']);
                        }
                    }
                });
            });


            $('.select2').select2()
            $('.status').change(function () {
                const status = $(this).prop('checked') == true ? 1 : 0;
                const agents_id = $(this).data('id');

                const url = '{{ url("company-admin/agents/change-status") }}';
                $.ajax({
                    type: "POST",
                    // dataType: "json",
                    url: url,
                    data: {'status': status, 'company_id': agents_id, '_token': "{{ csrf_token() }}"},
                    success: function (data) {
                        setTimeout(function () {
                            // location.reload(1);
                            table.ajax.reload();
                        }, 1000);
                    }
                });
            });

        });

        $(function () {
            let activeTab = sessionStorage.getItem("activeMoveTab");
            if (!activeTab) {
                activeTab = 'uplift';
            }

            $('#myTab li').removeClass('active');  // Remove active class from all li elements
            $('#myTab a[href="#' + activeTab + '"]').parent().addClass('active');

            var status = sessionStorage.getItem("is_completed");

            if (status) {
                $('#toggleComplete').prop('checked', true).trigger("change");
            } else {
                $('#toggleComplete').prop('checked', false).trigger("change");
            }


        });


        $(document).on("click", ".change-status", function () {
            var status = $(this).data('status');
            var move_id = $(this).data('id');
            var type = $(this).data('type');
            $("#status").val(status);
            $(".hidden-move-id").val(move_id);
            $(".move-type").val(type);
        });

        function openDeleteInprgModel(id) {
            $('#delete_inprg_move_id').val(id);
            $('#inprg_delete_move').modal('show');
        }

        function openDeleteModel(id) {
            $('#delete_move_id').val(id);
            $('#delete_move').modal('show');
        }

        $('#delete').on("click", function () {
            $('#delete_move').modal('hide');
            $('#delete_moveId').modal('show');
        });

        function openUnarchiveInprgMoveModel(id) {
            $('#unarchive_inprg_move_id').val(id);
            $('#unarchive_move').modal('show');
        }

        // open popup send mail delivery icr
        function openEamilDeliveryIcrModel(id) {
            // alert("Test: "+ id);
            $('input[name="delivery_icr_mail"]').val('');
            $('#delivery_icr_move_id').val(id);
            $('#delivery_icr').modal('show');
        }


    </script>
@stop
