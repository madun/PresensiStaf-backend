@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row">

        @include('layouts.menu')

        <div class="col-md-9">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <div>Data Schedule Staf</div>
                    <a href="{{ route('schedule.create') }}" class="btn btn-success btn-sm">Tambah baru</a>
                </div>

                <div class="card-body">

                    @if (session('success'))
                        <div class="alert alert-success" role="alert">
                            {{ session('success') }}
                        </div>
                    @endif

                    <table id="schedule-table" class="table table-bordered table-striped">
                        <thead>
                        <tr>
                            <th width="30">No.</th>
                            <th>Name</th>
                            <th>Total Jam</th>
                            <th width="190">Action</th>
                        </tr>
                        </thead>
                        <tbody></tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('js')
<script src="https://cdn.datatables.net/1.10.21/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.10.21/js/dataTables.bootstrap4.min.js"></script>

<script>
    $(document).ready(function() {
        $('#schedule-table_wrapper').removeClass('form-inline');
    });
    var table = $('#schedule-table').dataTable({
                    processing: true,
                    serverSide: true,
                    ajax : "{{ route('api.schedule') }}",
                    'columnDefs': [
                    {
                        "targets": 3,
                        "className": "text-center",
                    }],
                    columns: [
                        {data:'DT_RowIndex', name:'DT_RowIndex'},
                        {data:'name', name:'name'},
                        {data:'total_jam', name:'total_jam'},
                        {data:'action', name:'action', orderable: false, searchable: false}
                    ]
                });

    
</script>
@endsection