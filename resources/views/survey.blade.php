@extends('master')

@section('content')

<div class="page-heading">
    <div class="flexbox mb-4">
        <div class="flexbox">
            <h1 class="page-title">Survey</h1>
        </div>
        <a href="{{url('/rating')}}" class="btn btn-primary btn-air mt-5 mr-5">Ratings</a>
    </div>
</div>
<div class="page-content fade-in-up">
<div class="ibox">
    <div class="ibox-body">
        <h5 class="font-strong mb-4">SURVEY LIST</h5>
        <div class="flexbox mb-4">
            <div class="flexbox">
                <label class="mb-0 mr-2">Country:</label>
                <select class="form-control" id="type-filter" title="Please select" data-style="btn-solid" data-width="150px">
                    <option value="">All</option>
                    @foreach ($countries as $item)
                        <option value="{{$item->country_name}}">{{$item->country_name}}</option>
                    @endforeach
                </select>
            </div>
            <div class="input-group-icon input-group-icon-left mr-3">
                <span class="input-icon input-icon-right font-16"><i class="fa fa-search"></i></span>
                <input class="form-control form-control-rounded form-control-solid" id="key-search" type="text" placeholder="Search ...">
            </div>
        </div>
        <div class="table-responsive row">
            <table class="table table-hover" id="table">
                <thead class="thead-default thead-lg">
                    <tr>
                        <th class="no-sort">Job ID</th>
                        <th class="no-sort">Customer name</th>
                        <th class="no-sort">Customer phone</th>
                        <th class="no-sort">Agent name</th>
                        <th class="no-sort">Country</th>
                        <th class="no-sort">Address</th>
                        <th class="no-sort">Location</th>
                        <th class="no-sort">Is replied</th>
                        <th class="no-sort">Is received</th>
                        <th class="no-sort">Value</th>
                        <th class="no-sort">Is getton Coupon</th>
                        <th class="no-sort">Service rating</th>
                        <th class="no-sort">Agent rating</th>

                        <th class="no-sort">Acknowledged</th>
                        <th class="no-sort">Started</th>
                        <th class="no-sort">Arrived</th>
                        <th class="no-sort">Completed</th>
                        <th class="no-sort">Total distance</th>
                    </tr>
                </thead>
            </table>
        </div>
    </div>
</div>
</div>
@endsection

@section('js')


<script>

    $(function() {
        
            
        var table = $('#table').DataTable({
            pageLength: 20,
            responsive: true,
            fixedHeader: true,
            processing: true,
            serverSide: true,
            ajax: {
                    "url": '/api/survey-table',
                    "type": "POST",
                },
            dom: 'rtip',
            columnDefs: [{
                targets: 'no-sort',
                orderable: false,
            }],

            columns: [
                {data: 'job_id'},
                {data : 'job_pickup_name'},
                {data : 'job_pickup_phone'},
                {data : 'fleet_name'},
                {data : 'country'},
                {data : 'job_pickup_address'},
                {data : 'location', 
                    render: function (data) {
                        return '<a target="_blank" href="' + data + '">' + data+ '</a>';
                    }
                },
                {data : 'is_reply', 
                    render: function (data) {
                        return data == 1 ? '<i class="fa fa-check-circle text-success font-20"></i>' : '<i class="fa fa-times text-danger font-20"></i>';
                    }
                },
                {data : 'is_receipt', 
                    render: function (data) {
                        return data == 1 ? '<i class="fa fa-check-circle text-success font-20"></i>' : (data == 0 ? '<i class="fa fa-times text-danger font-20"></i>' : '-');
                    }
                },
                {data : 'price'},
                {data : 'is_coupon', 
                    render: function (data) {
                        return data == 1 ? '<i class="fa fa-check-circle text-success font-20"></i>' : (data == 0 ? '<i class="fa fa-times text-danger font-20"></i>' : '-');
                    }
                },
                {data : 'service_rating', 
                    render: function (data) {
                       return '<span>' + (data == 0 ?  '-' : Math.round(data * 100) / 100) + ' <i class="fa fa-star text-warning"></i></span>';
                    }
                },
                {data : 'fleet_rating', 
                    render: function (data) {
                       return '<span>' + (data == 0 ?  '-' : Math.round(data * 100) / 100) + ' <i class="fa fa-star text-warning"></i></span>';
                    }
                },
                {data : 'acknowledged_datetime'},
                {data : 'started_datetime'},
                {data : 'arrived_datetime'},
                {data : 'completed_datetime'},
                {data : 'total_distance_travelled'},

            ],

            select: true,
        });
        
        $('#key-search').on('keyup', function() {
            table.search(this.value).draw();
        });
        $('#type-filter').on('change', function() {
            table.column(4).search($(this).val()).draw();
        });
        
        
    });
  
    </script>

@endsection