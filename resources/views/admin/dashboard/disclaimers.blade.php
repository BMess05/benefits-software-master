@extends('admin.layouts.admin_layout')
@section('style')
<style>

</style>
@endsection
@section('content')
@include('admin.layouts.admin_top_menu')
<section class="cases_listing_wrap">
    <div class="container-fluid">
        <div class="row">
            <div class="col-md-12">
                @include('admin.layouts.messages')
            </div>
            <div class="col-md-12">
                <div class="listing-title">
                    <p class="text-left">Manage Disclaimers</p>
                    <p class="text-right"><a href="{{url('/disclaimer/add')}}">Add New Disclaimers</a></p>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-md-12">
                <div class="table_wrap">
                    <table class="table table-bordered">
                        <thead class="case_list_head">
                            <tr>
                                <th>Disclaimers</th>
                                <th>Default</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($disclaimers as $disclaimer)
                            <tr>
                                <td><a href="{{ url('disclaimer/edit'.'/'. base64_encode($disclaimer['DisclaimerId'])) }}">{{$disclaimer->DisclaimerName}}</a></td>
                                <td>{{($disclaimer->IsDefault == 1) ? 'True' : 'False'}}</td>
                            </tr>
                            @empty
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</section>
@endsection
