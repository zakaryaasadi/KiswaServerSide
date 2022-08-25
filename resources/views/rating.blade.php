@extends('master')

@section('content')

<div class="page-heading">
    <div class="flexbox mb-4">
        <div class="flexbox">
            <h1 class="page-title">RATINGs</h1>
        </div>
        <a href="{{url('/survey')}}" class="btn btn-primary btn-air mt-5 mr-5">Survey</a>
    </div>
</div>
<div class="page-content fade-in-up">

    <div class="row mb-4">
        @foreach ($countriesRating as $item)
        <div class="col-lg-4 col-md-6">
            <div class="card mb-4">
                <div class="card-body flexbox-b">
                    <div class="easypie mr-4" data-percent="{{round($item->service_rating / 5.0 * 100)}} " data-bar-color="{{$item->service_rating >= 4 ? '#18C5A9' : ($item->service_rating >= 3 ? '#5c6bc0' : ($item->service_rating >= 2 ? '#6c757d' : ($item->service_rating >= 1 ? '#f75a5f' : '#5c6bc0')))}}" data-size="80" data-line-width="8">
                        <span class="easypie-data {{$item->service_rating >= 4 ? 'text-success' : ($item->service_rating >= 3 ? 'text-primary' : ($item->service_rating >= 2 ? 'text-secondary' : ($item->service_rating >= 1 ? 'text-danger' : 'text-purple')))}}" style="font-size:32px;"><i class="fa fa-users"></i></span>
                    </div>
                    <div>
                        <h3 class="font-strong">{{round($item->service_rating, 2)}}  <i class="fa fa-star text-warning"></i></h3>
                        <div class="text-muted">{{$item->service_count}} CUSTOMERS {{$item->country}}</div>
                    </div>
                </div>
            </div>
        </div>
        @endforeach
        
    </div>
<div class="ibox">
    <div class="ibox-body">
        <h5 class="font-strong mb-4">AGENTs RATING</h5>
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
                        <th>#</th>
                        <th class="no-sort">Agent ID</th>
                        <th class="no-sort">Agent name</th>
                        <th class="no-sort">Country</th>
                        <th class="no-sort">Number of reviewers</th>
                        <th>Rating</th>
                        <th class="no-sort">Stars</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($fleetsRating as $index => $item)
                    <tr>
                        <td>{{$index + 1}}</td>
                        <td>{{$item->fleet_id}}</td>
                        <td>{{$item->fleet_name}}</td>
                        <td>{{$item->country}}</td>
                        <td>{{$item->fleet_count}}</td>
                        <td>{{round($item->fleet_rating, 2);}}</td>
                        <td>
                            @for($i = 0; $i < ceil($item->fleet_rating); $i++)
                                    <i class="fa fa-star text-warning"></i>
                            @endfor
                        </td>
                        
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>
</div>
@endsection

@section('js')

<script src="{{asset('vendors/jquery.easy-pie-chart/dist/jquery.easypiechart.min.js')}}"></script>



<script>

    $(function() {
        
            
        var table = $('#table').DataTable({
            pageLength: 20,
            responsive: true,
            fixedHeader: true,
            dom: 'rtip',
            columnDefs: [{
                targets: 'no-sort',
                orderable: false
            }],
            select: true
        });
        
        $('#key-search').on('keyup', function() {
            table.search(this.value).draw();
        });
        $('#type-filter').on('change', function() {
            table.column(3).search($(this).val()).draw();
        });
        
        
    });
  
    </script>

@endsection