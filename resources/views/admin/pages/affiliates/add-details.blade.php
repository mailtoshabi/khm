@extends('admin.layouts.default')
@section('title','Manage Product')
@section('content')

        <!-- Content Header (Page header) -->
<section class="content-header">
    <h1>
        Manage Product
    </h1>
    <ol class="breadcrumb">
        <li><a href="{{ route('admin.index') }}"><i class="fa fa-dashboard"></i> Dashboard</a></li>
        <li class="active">Manage Product</li>
    </ol>
</section>

<!-- Main content -->
<section class="content">
    <div class="row">
        <!-- form start -->
        <form action="@if(isset($product->id)) {{ route('admin.affiliate.products.update',[$product->id]) }} @endif" method="POST" role="form" id="add-product-form" enctype="multipart/form-data">
            {!! csrf_field() !!}
            <div class="col-md-8">
                <div class="box box-primary">
                    <div class="box-header with-border">
                        {{--<h3 class="box-title"> Product</h3>--}}
                    </div>
                    <!-- /.box-header -->

                        @if(isset($product->id))
                            <input type="hidden" name="_method" value="PUT" />
                        @endif
                        <div class="box-body">

                            <div class="row">
                                <div class="form-group col-sm-12">
                                    <h3>{{ isset($product->name) ? $product->name : null }}</h3>
                                </div>
                            </div>
                            @if(!empty($is_dealer) )
                            <div class="row">
                                <div class="form-group col-sm-12">
                                    <label class="control-label">Distributor Price</label>
                                    <input class="form-control" type="text" name="distributor_price" id="distributor_price" placeholder="Distributor Price" value="{{ old('distributor_price', isset($affiliate_product->distributor_price) ? $affiliate_product->distributor_price : null) }}">
                                </div>
                            </div>
                            @endif
                            <div class="row">

                                <div class="col-sm-12">
                                    <label class="control-label">Profit Details</label>
                                    <div id="price_container">
                                        @if(isset($product->id))
                                            @foreach($type_sizes as $key_main=>$type_size )
                                                <div id="dvprice-details-{{ $key_main }}" class="well" style="position: relative">
                                                    <div class="form-group">
                                                        <label class="control-label">Type OR Size : {{ Utility::getCategoryName($type_size->type_id) }}</label>
                                                    </div>
                                                        <div class="row">

                                                            <div class="form-group col-sm-6">
                                                                <label class="control-label" for="profit">Selling Rate<span class="text-red">*</span></label>
                                                                <div class="input-group">
                                                                    <div class="">
                                                                        {{--<div class="col-md-5 col-sm-5 col-lg-5 col-xs-5" style="padding-right: 0px">
                                                                            <select class="form-control" id="profit_type-{{ $key_main }}" name="profit_type[{{ $key_main }}]">
                                                                                @foreach(Utility::profit_types() as $value => $profit_type)
                                                                                    <option value="{{ $value }}" {{ $value==$type_size->profit_type ? 'selected':'' }}>{{ $profit_type }}</option>
                                                                                @endforeach
                                                                            </select>
                                                                        </div>--}}
                                                                        <div class="col-md-12 col-sm-12 col-lg-12 col-xs-12" style="padding-left: 0px">
                                                                            <input type="hidden" id="type_size-{{ $key_main }}" name="type_size[{{ $key_main }}]" value="{{ $type_size->type_id }}">
                                                                            <input type="text" class="form-control" id="profit-{{ $key_main }}" name="profit[{{ $key_main }}]" placeholder="Enter Selling Rate"
                                                                                   autocomplete="off" value="{{ isset($type_size->profit) ? $type_size->profit : null }}" onkeyup="getSellingPrice(this.value, '{{ $key_main }}','{{ $type_size->type_id }}');">
                                                                        </div>

                                                                    </div>
                                                                </div>

                                                            </div>
                                                            <div class="form-group col-sm-6">
                                                                <div class="well">
                                                                    <h4 class="">Price Details</h4>

                                                                    <table width="100%">
                                                                        {{--<tr>
                                                                            <td style="padding: 5px; font-weight:bold;">Your Selling Price</td>
                                                                            <td style="padding: 5px; font-weight:bold;"><i class="fa fa-inr"></i> <span id="affiliate_sell_price-{{ $key_main }}">{{ round(Utility::getParticularAffiliatePrice($type_size->profit,$product->id,$type_size->type_id,$type_size->profit_type),2) }}</span></td>
                                                                        </tr>
                                                                        <tr style="background: darkred; color: yellow;">
                                                                            <td style="padding: 5px;" width="50%">Your  Cost Price</td>
                                                                            <td style="padding: 5px;" width="50%"><i class="fa fa-inr"></i> {{ round(Utility::getAffiliatePrice($product->id,$type_size->type_id)['cost'],2) }}</td>
                                                                        </tr>--}}
                                                                        <tr style="background: darkgreen; color: yellow;">
                                                                            <td style="padding: 5px;">KHM Price</td>
                                                                            <td style="padding: 5px;"><i class="fa fa-inr"></i> {{ round(Utility::getAffiliatePrice($product->id,$type_size->type_id)['khm'],2) }}</td>
                                                                        </tr>
                                                                        <tr style="background: darkred; color: yellow;">
                                                                            <td style="padding: 5px;">MRP</td>
                                                                            <td style="padding: 5px;"><i class="fa fa-inr"></i> {{ !empty($type_size->mrp) ? round($type_size->mrp,2) :  round(Utility::getAffiliatePrice($product->id,$type_size->type_id)['khm'],2) }}</td>
                                                                        </tr>
                                                                    </table>
                                                                </div>
                                                            </div>
                                                        </div>

                                                </div>
                                            @endforeach
                                        @endif
                                    </div>
                                    <div class="clearfix"></div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="form-group col-sm-6">
                                    <input type="checkbox" class="form-control" id="is_home" name="is_home" {{ isset($affiliate_product->is_home) && ($affiliate_product->is_home) ? 'checked' : '' }} value="1" >
                                    &nbsp;&nbsp;&nbsp;<label for="is_home">View in Homepage</label>
                                </div>
                                <div class="form-group col-sm-6">
                                    <input type="checkbox" class="form-control" id="is_offer" name="is_offer" {{ isset($affiliate_product->is_offer) && $affiliate_product->is_offer ? 'checked' : '' }} value="1" >
                                    &nbsp;&nbsp;&nbsp;<label for="is_offer">Offer of the Day</label>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-sm-12">
                                    <label class="control-label">SEO Tools</label>
                                    <div id="dv_seotools" class="well">
                                    <div class="form-group">
                                    <div class="row">
                                    <div class="col-md-12">
                                    <label class="control-label">Product Title</label>
                                    <input type="text" class="form-control" id="site_title" name="site_title" placeholder="Enter Site Title"
                                    autocomplete="off" value="{{ old('site_title', isset($affiliate_product->site_title) ? $affiliate_product->site_title : null) }}">
                                    </div>
                                    <div class="col-md-12">
                                    <label class="control-label">Product Keywords</label>
                                        <textarea class="form-control" id="site_keywords" name="site_keywords" placeholder="Enter Site Keywords">{{ old('site_keywords', isset($affiliate_product->site_keywords) ? $affiliate_product->site_keywords : null) }}</textarea>
                                        @if ($errors->has('site_keywords'))
                                            <span class="help-block">
                                        {{ $errors->first('site_keywords') }}
                                    </span>
                                        @endif
                                    </div>
                                    <div class="col-md-12">
                                        <label class="control-label">Product Description</label>
                                        <textarea class="form-control" id="site_description" name="site_description" placeholder="Enter Site Description">{{ old('site_description', isset($affiliate_product->site_description) ? $affiliate_product->site_description : null) }}</textarea>
                                        @if ($errors->has('site_description'))
                                            <span class="help-block">
                                                {{ $errors->first('site_description') }}
                                            </span>
                                        @endif
                                    </div>
                                    </div>
                                    </div>
                                    <div class="clearfix"></div>
                                    </div>
                                </div>
                            </div>

                        </div>
                        <!-- /.box-body -->

                        <div class="box-footer">
                            <button type="submit" class="btn btn-primary">@if(!isset($product->id)) Create @else Update @endif</button>
                        </div>

                </div>

            </div>

            <div class="col-md-4">
                <div class="panel panel-primary">
                    <div class="panel-heading">
                        @if(!isset($product->id)) Create @else Update @endif
                    </div>
                    <div class="panel-body" style="text-align: center;">
                        <button type="submit" class="btn btn-primary btn-lg">@if(!isset($product->id)) Create @else Update @endif</button>
                    </div>
                </div>
            </div>
            @if(!empty(json_decode($dealers)))
            <div class="col-md-4">
                <div class="panel panel-primary">
                    <div class="panel-heading">
                        Dealers
                    </div>
                    <div class="panel-body" style="text-align: justify;">
                        @foreach($dealers as $dealer)

                            <p><strong>{{ $dealer->name }} {{ $dealer->affiliate->city }}</strong> - Price : {{ $dealer->dp }}</p>

                        @endforeach
                    </div>
                </div>
            </div>
            @endif

        </form>
        <!-- /. box -->
    </div>
    <!-- /.col -->
    </div>
    <!-- /.row -->
</section>
<!-- /.content -->
@stop
@push('post_body')

@endpush


@push('page_scripts')
<script>
    $(document).ready(function() {


        /*var $validator = $('#add-product-form').validate({
            rules: {
                name: {
                    required: true
                },
                image: {
                    extension: "jpg|jpeg|png|gif"
                },
                brochure: {
                    extension: "pdf|doc|docx"
                },
                unit_om: {
                    required: true
                },
                profit: {
                    required: true,
                    digits: true,
                    maxlength : 2
                }
            },
            messages: {
                name: {
                    required: "Name is required"
                },
                image: {
                    extension: "Image must be jpg, png or gif"
                },
                brochure: {
                    extension: "Brochure must be in pdf, doc or docx"
                },
                unit_om: {
                    required: "Unit of Measurement is required"
                },
                profit: {
                    required: "profit is required",
                    digits: "Invalide data",
                    maxlength: "Invalide data"
                }
            }
        });*/


    });
</script>
<script>
    $(function () {
        //bootstrap WYSIHTML5 - text editor
        $("#description").wysihtml5();
    });

    function getSellingPrice (val,index,type) {

        var profit_type = $('#profit_type-'+index).val();

        var product_id = "{{ $product->id }}";
        var formdata = {profit: val,product_id: product_id,type: type,profit_type: profit_type};

        $.ajax({
            type: "POST",
            url: "{{ route('admin.affiliate.products.getprice') }}",
            data:formdata,
            success: function(data){
                /*console.log(data.khm);*/
                /*var costPrice = parseFloat(data.cost);
                var khmPrice = parseFloat(data.khm);
                var sellPrice = costPrice + (costPrice * parseFloat(val/100));*/
                /*if(isNaN(sellPrice)) {
                    $('#affiliate_sell_price' + '-' + index).text(parseFloat(khmPrice).toFixed(2));
                }else {
                    $('#affiliate_sell_price' + '-' + index).text(parseFloat(sellPrice).toFixed(2));
                }*/
                $('#affiliate_sell_price' + '-' + index).text(parseFloat(data).toFixed(2));
            }
        });

    }

</script>
@endpush
