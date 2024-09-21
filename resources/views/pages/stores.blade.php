@extends('layouts.app')
@section('title','Stores')
@section('description','')
@section('keywords','')
@section('content')
    <div class="wrapper sub_page_pt">

        <div class="container"> <!---->
            <div class="row">
                <div role="tabpanel">

                    <div class="col-sm-12" >
                        <h3 class="">Stores</h3>
                    </div>


                    <div class="col-xs-12 col-sm-4 col-md-3 col-lg-3">
                        <form action="{{ route('store.search') }}" method="POST" name="form-search-store" id="form-search-store">
                            {!! csrf_field() !!}
                            <div class="input-group">
                                <input style="background: #fff" type="text" class="form-control"  placeholder="Search by PIN Code" id="pin_search" name="pin_search" value="{{ isset($term)? $term : '' }}" >
                                        <span class="input-group-addon">
                                        <button id="pin_search_button" type="submit" style="border: none; background: transparent;">
                                            <span class="fa fa-search"></span>
                                        </button>
                                        </span>
                            </div>
                        </form>
                    </div>
                    <div class="col-md-12">
                        <br><p>search store nearby you by using your location pin code. Select store to get more information about available products</p>
                    </div>

                </div>
            </div>
        </div>

        <div class="container">
            <div class="row destacados">

                @if(isset($stores) && !empty($stores))
                    @foreach($stores as $store)
                        <div class="col-md-4">
                            <div class="result_cont">
                                {{--<img src="http://lorempixel.com/200/200/abstract/1/" alt="Texto Alternativo" class="img-circle img-thumbnail">--}}
                                <h3>{{ $store->name }}</h3>
                                <p><i class="fa fa-phone"></i> {{ !empty($store->phone) ? $store->phone : Utility::settings('admin_phone') }}</p>
                                <p><i class="fa fa-envelope"></i> {{ !empty($store->email) ? $store->email : Utility::settings('admin_email') }}</p>
                                <p>{{ $store->location . ', ' }} {{ $store->district }}</p>
                                <a href="{{ route('all.slug',$store->slug) }}" target="_blank" class="btn btn-primary" title="Enlace">View Details »</a>
                            </div>
                        </div>
                    @endforeach
                    <div class="col-md-12 text-justify" style="padding-top: 20px;">
                        {!! $stores->appends(Request::only('pin_search'))->links() !!}
                    </div>
                @else
                    <p>No stores found..!!</p>
                @endif
            </div>
        </div>

    </div>
    <!-- Wrapper -->
@endsection

@push('page_scripts')
<script>
    $(document).ready(function() {

    });
</script>
@endpush