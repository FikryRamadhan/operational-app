@extends('layouts.template')

@section('content')
<div class="row">

    <div class="col-lg-9">
        <div class="card">
            <div class="card-header">
                <h4 class="card-title">
                    <span class="d-inline-block">
                        {{ $title ?? 'judul' }}
                    </span>
                </h4>
            </div>

            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-bordered" id="dataTable">

                        <thead>
                            <tr>
                                <th> Gudang </th>
                                <th> Deskripsi </th>
                                <th width="100"> Aksi </th>
                            </tr>
                        </thead>

                        <tfoot>
                            <tr>
                                <th> Gudang </th>
                                <th> Deskripsi </th>
                                <th> Aksi </th>
                            </tr>
                        </tfoot>

                    </table>
                </div>
            </div>
        </div>
    </div>

</div>
@endsection

@section('scripts')
<script>
    $(function(){
        $('#dataTable').DataTable({
            processing: true,
            serverSide: true,
            autoWidth: false,
            ajax : {
				url : "{{ route('warehouse_stock') }}"
			},
            columns: [{
                data: 'warehouse_name',
                name: 'warehouse_name'
            },{
                data: 'description',
                name: 'description',
            },{
                data: 'action',
                name: 'action',
                orderable: false,
                searchable: false
            }]
        })
    })
</script>
@endsection
