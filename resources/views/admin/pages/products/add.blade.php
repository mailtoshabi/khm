@extends('admin.layouts.default')
@section('title','Add New Product')
@section('content')

        <!-- Content Header (Page header) -->
<section class="content-header">
    <h1>
        {{ isset($product->id) ? 'Edit' : 'Add New' }} Product
    </h1>
    <ol class="breadcrumb">
        <li><a href="{{ route('admin.index') }}"><i class="fa fa-dashboard"></i> Dashboard</a></li>
        <li class="active">{{ isset($product->id) ? 'Edit' : 'Add New' }} Product</li>
    </ol>
</section>

<!-- Main content -->
<section class="content">
    <div class="row">
        <!-- form start -->
        <form action="@if(isset($product->id)) {{ route('admin.products.update',[$product->id]) }} @else {{ route('admin.products.store') }}  @endif" method="POST" role="form" id="add-product-form" enctype="multipart/form-data">
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
                                @if(isset($slug))
                                    <div class="form-group col-sm-12">
                                        <input type="text" class="form-control" value="{{ config('app.website_url') }}/{{ $slug }}" disabled>
                                    </div>
                                @endif
                                <div class="form-group col-sm-6">
                                    <input type="checkbox" class="form-control" id="is_home" name="is_home" {{ isset($product->is_home) && $product->is_home ? 'checked' : '' }} value="1" >
                                    &nbsp;&nbsp;&nbsp;<label for="is_home">View in Homepage</label>
                                </div>
                            </div>

                            <div class="row">
                                <div class="form-group col-sm-6 @if ($errors->has('name')) has-error @endif">
                                        <label for="name">Name<span class="text-red">*</span> </label>
                                        <input type="text" class="form-control" id="name" name="name" placeholder="Enter Product Name"
                                               value="{{ old('name', isset($product->name) ? $product->name : null) }}">
                                        @if ($errors->has('name'))
                                            <span class="help-block">
                                    {{ $errors->first('name') }}
                                </span>
                                    @endif
                                </div>
                                <div class="form-group col-sm-6 @if ($errors->has('unit_om')) has-error @endif">
                                    <label class="control-label" for="unit_om">Unit of Measurement<span class="text-red">*</span></label>
                                    <input type="text" class="form-control" id="unit_om" name="unit_om" placeholder="Enter Unit of Measurement"
                                           autocomplete="off" value="{{ old('unit_om', isset($product->unit_om) ? $product->unit_om : null) }}">
                                    @if ($errors->has('unit_om'))
                                        <span class="help-block">
                              {{ $errors->first('unit_om') }}
                          </span>
                                    @endif
                                </div>

                            </div>
                            <div class="row">
                                <div class="form-group col-sm-12 @if ($errors->has('description')) has-error @endif">
                                    <label for="name">Description</label>
                                    <textarea class="form-control" id="description" name="description" placeholder="Enter Description">{{ old('description', isset($product->description) ? $product->description : null) }}</textarea>
                                    @if ($errors->has('description'))
                                        <span class="help-block">
                                        {{ $errors->first('description') }}
                                    </span>
                                    @endif
                                </div>
                            </div>
                            <div class="row">
                                <div class="form-group col-sm-4">
                                    <label>Main Image</label>
                                    <input type="hidden" name="is_image" id="is_image" value="1" >
                                    <div id="dvUploadImage" class="{{ isset($product->id)&&!empty($product->image) ? 'hidden' : '' }}">
                                        <input type="file" id="image" name="image">
                                        <p class="help-block">Upload images with a size of {{ Utility::IMAGE_PRODUCT }}</p>
                                    </div>
                                    @if(isset($product->id)&&!empty($product->image))
                                        <div id="dvImage">
                                            <div class="col-md-8 col-xs-7">
                                                <a href="{{ asset(Utility::DEFAULT_STORAGE . App\Models\Product::FILE_DIRECTORY .  '/' . Utility::IMAGE_PRODUCT . '_' . $product->image) }}" target="_blank">
                                                <img src="{{ asset(Utility::DEFAULT_STORAGE . App\Models\Product::FILE_DIRECTORY .  '/' . $product->image) }}" alt="" height="50" />
                                                </a>
                                            </div>
                                            <div class="col-md-1 col-xs-2">
                                                <a id="testtrig" data-target="#dvImage" data-replace="#dvUploadImage" class="icon wb-close close_data"></a>
                                            </div>
                                        </div>
                                    @endif
                                </div>
                                <div class="form-group col-sm-4">
                                    <label>Other Images</label>
                                    <input type="hidden" name="is_images" id="is_images" value="1" >
                                    <div id="dvUploadImageOther" class="{{ isset($product->id)&&!empty($product->images) ? 'hidden' : '' }}">
                                        <input type="file" id="images" name="images[]" multiple>
                                        <p class="help-block">Max four images of size {{ Utility::IMAGE_PRODUCT }}</p>
                                    </div>
                                    @if(isset($product->id)&&!empty($product->images))
                                        <div id="dvImageOther">
                                            <div class="col-md-8 col-xs-7">
                                                @foreach($product->images as $otherImage)
                                                    {{-- <img src="{{ asset(Utility::DEFAULT_STORAGE . $otherImage) }}" alt="" height="50" /> --}}
                                                    <a href="{{ asset(Utility::DEFAULT_STORAGE . App\Models\Product::FILE_DIRECTORY .  '/' . Utility::IMAGE_PRODUCT . '_' . $otherImage) }}" target="_blank">
                                                        <img src="{{ asset(Utility::DEFAULT_STORAGE . App\Models\Product::FILE_DIRECTORY .  '/' . $otherImage) }}" alt="" height="32" />
                                                    </a>
                                                @endforeach
                                            </div>
                                            <div class="col-md-1 col-xs-2">
                                                <a data-target="#dvImageOther" data-replace="#dvUploadImageOther" class="icon wb-close close_data"></a>
                                            </div>
                                        </div>
                                    @endif
                                </div>
                                <div class="form-group col-sm-4">
                                    <label >Product Brochure</label>
                                    <input type="hidden" name="is_brochure" id="is_brochure" value="1" >
                                    <div id="dvBrochureUpload" class="{{ isset($product->id)&&!empty($product->brochure) ? 'hidden' : '' }}">
                                        <input type="file" id="brochure" name="brochure">
                                    </div>
                                    @if(isset($product->id)&&!empty($product->brochure))
                                        <div id="dvBrochure">
                                            <div class="col-md-8 col-xs-7">
                                                <a href="{{ asset(Utility::DEFAULT_STORAGE . App\Models\Product::FILE_DIRECTORY_BROCHURE .  '/' . $product->brochure) }}" target="_blank"><span class="fa fa-file-o" style="font-size: 45px;"></span></a>
                                            </div>
                                            <div class="col-md-1 col-xs-2">
                                                <a data-target="#dvBrochure" data-replace="#dvBrochureUpload" class="icon wb-close close_data"></a>
                                            </div>
                                        </div>
                                    @endif
                                </div>
                            </div>

                            <div class="row">
                                <div class="form-group col-sm-12 @if ($errors->has('video')) has-error @endif">
                                    <label for="name">Video - Youtube Link</label>
                                    <input type="text" class="form-control" id="video" name="video" placeholder="Youtube Link " value="{{ old('video', isset($product->video) ? $product->video : null) }}">
                                    @if ($errors->has('video'))
                                        <span class="help-block">
                                        {{ $errors->first('video') }}
                                    </span>
                                    @endif
                                </div>
                            </div>

                            <div class="row">
                                <div class="form-group col-sm-6">
                                    <label class="control-label" for="hsn_code">HSN code</label>
                                    <input type="text" class="form-control" id="hsn_code" name="hsn_code" placeholder="Enter HSN code"
                                           autocomplete="off" value="{{ old('hsn_code', isset($product->hsn_code) ? $product->hsn_code : null) }}">
                                </div>
                                <div class="form-group col-sm-6 @if ($errors->has('tax')) has-error @endif">
                                    <label class="control-label" for="tax">GST<span class="text-red">*</span></label>
                                    <div class="input-group">
                                        <div class="row">
                                            <div class="col-md-9 col-sm-9 col-lg-9 col-xs-9" style="padding-right: 0px">
                                                <input type="text" class="form-control" id="tax" name="tax" placeholder="Enter GST %"
                                                       autocomplete="off" value="{{ old('tax', isset($product->tax) ? $product->tax : null) }}">
                                            </div>
                                            <div class="col-md-3 col-sm-3 col-lg-3 col-xs-3" style="padding-left: 0px">
                                                <input type="text" class="form-control" id="post_tax" name="post_tax" value="%" placeholder="%" disabled>
                                            </div>
                                        </div>
                                    </div>
                                    @if ($errors->has('tax'))
                                        <span class="help-block">
                                          {{ $errors->first('tax') }}
                                      </span>
                                    @endif

                                </div>
                            </div>

                            <div class="row">
                                <div class="form-group col-md-4">
                                    <label class="control-label">Delivery Unit</label>
                                    <input type="text" class="form-control" id="delivery_unit" name="delivery_unit" onkeyup="getDeliveryCost(this.value);" placeholder="Enter Delivery Unit"
                                           autocomplete="off" value="{{ old('delivery_unit', isset($product->id) ? $product->delivery_unit : 1) }}">
                                    <p class="help-block">Total Delivery Cost will be INR <strong id="total_delivery_cost">{{ isset($product->id) ? Utility::settings('delivery_charge') * $product->delivery_unit : Utility::settings('delivery_charge') }}</strong></p>
                                </div>
                                <div class="form-group col-md-4">
                                    <label class="control-label">Minimum Delivery Charge</label>
                                    <input type="text" class="form-control" id="delivery_min" name="delivery_min" placeholder="Enter Minimum Delivery Charge"
                                           autocomplete="off" value="{{ old('delivery_min', isset($product->id) ? $product->delivery_min : '') }}">
                                </div>
                                <div class="form-group col-md-4">
                                    <label class="control-label">Maximum Delivery Charge</label>
                                    <input type="text" class="form-control" id="delivery_max" name="delivery_max" placeholder="Enter Maximum Delivery Charge"
                                           autocomplete="off" value="{{ old('delivery_max', isset($product->id) ? $product->delivery_max : '') }}">
                                </div>
                            </div>

                            <div class="row">

                                    <div class="col-sm-12">
                                        <label class="control-label">Price Detais</label>
                                        <div id="price_container">
                                        @if(isset($product->id))
                                            @foreach($type_sizes as $key_main=>$type_size )
                                            <div id="dvprice-details-{{ $key_main }}" class="well" style="position: relative">
                                                @if($key_main!=0)
                                                    <div class="wb-close-container">
                                                        <a data-target="#dvprice-details-{{ $key_main }}" class="icon wb-close close_data"></a>
                                                    </div>
                                                @endif
                                                <div class="form-group">
                                                    <label class="control-label">Type & Size</label>
                                                    <select class="form-control select2" id="type_size-{{ $key_main }}" name="type_size[{{ $key_main }}]" style="width: 100%;">
                                                        <option value="">Select Type & Size</option>
                                                        @foreach($product_types as $id => $product_type)
                                                            <option {{ $type_size->type_id==$id ? 'selected' : '' }} value="{{ $id }}">{{ $product_type }}</option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                                <div class="form-group">
                                                    <div class="row">
                                                        <div class="col-md-6">
                                                            <label class="control-label">MRP</label>
                                                            <input type="text" class="form-control" id="mrp-{{ $key_main }}" name="mrp[{{ $key_main }}]" placeholder="Enter MRP"
                                                                   autocomplete="off" value="{{ $type_size->mrp }}">
                                                        </div>
                                                        <div class="col-md-6">
                                                            <label class="control-label">Stock</label>
                                                            <input type="text" class="form-control" id="stock-{{ $key_main }}" name="stock[{{ $key_main }}]" placeholder="Enter Stock"
                                                                   autocomplete="off" value="{{ $type_size->stock }}">
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="form-group" id="quantity_container-{{ $key_main }}">
                                                    <?php $key=0 ?>
                                                    @foreach($type_size->quantities as $key=>$quantity)
                                                        <div id="single_quantity-{{ $key_main }}-{{ $key }}" class="row" style="padding-top:10px;">
                                                            <div class="col-md-4">
                                                            @if($key==0)
                                                            <label class="control-label">Quantity From</label>
                                                            @endif
                                                            <input type="text" class="form-control" id="quantity_from-{{ $key_main }}-{{ $key }}" name="quantity_from[{{ $key_main }}][{{ $key }}]" placeholder="From" autocomplete="off" value="{{ $quantity->quantity_from }}" {{ $key==0 ? 'readonly' : '' }}>
                                                            </div>
                                                            <div class="col-md-4">
                                                                @if($key==0)
                                                                <label class="control-label">Quantity To</label>
                                                                @endif
                                                                <input type="text" class="form-control" id="quantity_to-{{ $key_main }}-{{ $key }}" name="quantity_to[{{ $key_main }}][{{ $key }}]" placeholder="To" autocomplete="off" value="{{ $quantity->quantity_to }}">
                                                            </div>
                                                            <div class="col-md-3 col-xs-10">
                                                                @if($key==0)
                                                                <label class="control-label">Price</label>
                                                                @endif
                                                                <input type="text" class="form-control" id="price-{{ $key_main }}-{{ $key }}" name="price[{{ $key_main }}][{{ $key }}]" placeholder="Price" autocomplete="off" value="{{ $quantity->price }}">
                                                            </div>
                                                            @if($key!=0)
                                                                <div class="col-md-1 col-xs-2">
                                                                    <a data-target="#single_quantity-{{ $key_main }}-{{ $key }}" class="icon wb-close close_data"></a>
                                                                </div>
                                                            @endif
                                                        </div>
                                                        <?php $key++ ?>
                                                    @endforeach
                                                </div>
                                                <div class="pull-right">
                                                    <a href="#" data-toggle="add-more" data-template="#template_quantity_dyn-{{ $key_main }}"
                                                       data-close=".wb-close" data-container="#quantity_container-{{ $key_main }}"
                                                       data-count="{{ $type_size->quantities->count()-1 }}"
                                                       data-addindex='[{"selector":".type_size","attr":"name"},{"selector":".quantity_from","attr":"name"},{"selector":".quantity_to","attr":"name"},{"selector":".price","attr":"name"}]'
                                                            {{--data-plugins='[{"selector":"select","plugin":"selectpicker"},{"selector":".timepicker","plugin":"clockpicker"}]'--}}
                                                       data-increment='[{"selector":".type_size","attr":"id"},{"selector":".quantity_from","attr":"id"},{"selector":".quantity_to","attr":"id"},{"selector":".price","attr":"id"}]'><i
                                                                class="fa fa-plus-circle"></i>&nbsp;&nbsp;Add more Range</a>
                                                </div>
                                                <div class="clearfix"></div>
                                            </div>
                                            @endforeach
                                        @else
                                            <div class="well">
                                                <div class="form-group @if ($errors->has('type_size')) has-error @endif">
                                                    <label class="control-label">Type & Size</label>
                                                    <select class="form-control select2" id="type_size-0" name="type_size[0]" style="width: 100%;">
                                                        <option value="">Select Type & Size</option>
                                                        @foreach($product_types as $id => $product_type)
                                                            <option value="{{ $id }}">{{ $product_type }}</option>
                                                        @endforeach
                                                    </select>
                                                    @if ($errors->has('type_size'))
                                                        <span class="help-block">
                                                          {{ $errors->first('type_size') }}
                                                        </span>
                                                    @endif
                                                </div>
                                                <div class="form-group">
                                                    <div class="row">
                                                        <div class="col-md-6">
                                                            <label class="control-label">MRP</label>
                                                            <input type="text" class="form-control" id="mrp-0" name="mrp[0]" placeholder="Enter MRP"
                                                                   autocomplete="off" value="">
                                                        </div>
                                                        <div class="col-md-6">
                                                            <label class="control-label">Stock</label>
                                                            <input type="text" class="form-control" id="stock-0" name="stock[0]" placeholder="Enter Stock"
                                                                   autocomplete="off" value="">
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="form-group" id="quantity_container">
                                                    <div class="row">
                                                        <div class="col-md-4">
                                                            <label class="control-label">Quantity From</label>
                                                            <input type="text" class="form-control" id="quantity_from-0-0" name="quantity_from[0][0]" placeholder="From" autocomplete="off" value="1" readonly>
                                                        </div>
                                                        <div class="col-md-4">
                                                            <label class="control-label">Quantity To</label>
                                                            <input type="text" class="form-control" id="quantity_to-0-0" name="quantity_to[0][0]" placeholder="To" autocomplete="off" value="">
                                                        </div>
                                                        <div class="col-md-3">
                                                            <label class="control-label">Price</label>
                                                            <input type="text" class="form-control" id="price-0-0" name="price[0][0]" placeholder="Price" autocomplete="off" value="">
                                                        </div>
                                                    </div>

                                                </div>
                                                <div class="pull-right">
                                                    <a href="#" data-toggle="add-more" data-template="#template_quantity"
                                                       data-close=".wb-close" data-container="#quantity_container"
                                                       data-count="0"
                                                       data-addindex='[{"selector":".type_size","attr":"name"},{"selector":".quantity_from","attr":"name"},{"selector":".quantity_to","attr":"name"},{"selector":".price","attr":"name"}]'
                                                       {{--data-plugins='[{"selector":"select","plugin":"selectpicker"},{"selector":".timepicker","plugin":"clockpicker"}]'--}}
                                                       data-increment='[{"selector":".type_size","attr":"id"},{"selector":".quantity_from","attr":"id"},{"selector":".quantity_to","attr":"id"},{"selector":".price","attr":"id"}]'><i
                                                                class="fa fa-plus-circle"></i>&nbsp;&nbsp;Add more Range</a>
                                                </div>
                                                <div class="clearfix"></div>
                                            </div>
                                                @if ($errors->has('type_size'))
                                                    <span class="help-block">
                                                          {{ $errors->first('type_size') }}
                                                        </span>
                                                @endif
                                        @endif
                                        </div>
                                        <div class="pull-right">
                                            <a href="#" data-toggle="add-more" data-template="#template_price"
                                               data-close=".wb-close" data-container="#price_container"
                                               data-count="{{ isset($product->id) ? $type_sizes->count()-1 : 0 }}"
                                               data-addindex='[{"selector":".type_size","attr":"name"},{"selector":".mrp","attr":"name"},{"selector":".stock","attr":"name"},{"selector":".quantity_from","attr":"name","have_child":"0"},{"selector":".quantity_to","attr":"name","have_child":"0"},{"selector":".price","attr":"name","have_child":"0"},{"selector":".quantity_from_copy","attr":"name"},{"selector":".quantity_to_copy","attr":"name"},{"selector":".price_copy","attr":"name"}]'
                                               data-plugins='[{"selector":"select","plugin":"select2"}]'
                                               data-increment='[{"selector":".type_size","attr":"id"},{"selector":".mrp","attr":"id"},{"selector":".stock","attr":"id"},{"selector":".quantity_from","attr":"id","have_child":"0"},{"selector":".quantity_to","attr":"id","have_child":"0"},{"selector":".price","attr":"id","have_child":"0"},{"selector":".template_quantity_copy","attr":"id"},{"selector":".quantity_container_copy","attr":"id"},{"selector":".add_more_range_copy","attr":"data-template"},{"selector":".add_more_range_copy","attr":"data-container"},{"selector":".quantity_from_copy","attr":"id"},{"selector":".quantity_to_copy","attr":"id"},{"selector":".price_copy","attr":"id"}]'><i
                                                        class="fa fa-plus-circle"></i>&nbsp;&nbsp;Add more Price Details </a>
                                        </div>
                                        <div class="clearfix"></div>
                                    </div>
                            </div>
                            <div class="row">
                                <div class="form-group col-sm-6">
                                    <input type="checkbox" class="form-control" id="is_featured" name="is_featured" {{ isset($product->is_featured) && $product->is_featured ? 'checked' : '' }} value="1" >
                                    &nbsp;&nbsp;&nbsp;<label for="is_featured">Featured product</label>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-sm-12">
                                    <label class="control-label">SEO Tools</label>
                                    <div id="dv_seotools" class="well">
                                    <div class="form-group">
                                    <div class="row">
                                    <div class="col-md-12">
                                    <label class="control-label">Site Title</label>
                                    <input type="text" class="form-control" id="site_title" name="site_title" placeholder="Enter Site Title"
                                    autocomplete="off" value="{{ old('site_title', isset($product->id) ? $product->site_title : null) }}">
                                    </div>
                                    <div class="col-md-12">
                                    <label class="control-label">Site Keywords</label>
                                        <textarea class="form-control" id="site_keywords" name="site_keywords" placeholder="Enter Site Keywords">{{ old('site_keywords', isset($product->site_keywords) ? $product->site_keywords : null) }}</textarea>
                                        @if ($errors->has('site_keywords'))
                                            <span class="help-block">
                                        {{ $errors->first('site_keywords') }}
                                    </span>
                                        @endif
                                    </div>
                                    <div class="col-md-12">
                                        <label class="control-label">Site Description</label>
                                        <textarea class="form-control" id="site_description" name="site_description" placeholder="Enter Site Description">{{ old('site_description', isset($product->site_description) ? $product->site_description : null) }}</textarea>
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
            <div class="col-md-4">
                <div class="panel panel-primary">
                    <div class="panel-heading">
                        <span class="glyphicon glyphicon-list"></span> Brand
                    </div>
                    <div id="" class="panel-body">
                        <div class="form-group">
                            <label class="control-label">Brand</label>
                            <select class="form-control select2" id="brands" name="brands[]" style="width: 100%;"  data-placeholder="Select brands"> {{--multiple--}}
                                @foreach($brands as $id => $brand)
                                    <option {{ isset($brand_id_array) && in_array($id, $brand_id_array) ? 'selected' : '' }} value="{{ $id }}">{{ $brand }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                </div>

            </div>
            <div class="col-md-4">
                <div class="panel panel-primary">
                    <div class="panel-heading">
                       Categories &nbsp;
                    </div>
                    <a href="#" style="width:100%; padding-left:10px;" id="select_all">Uncheck All </a>
                    <div id="sidebar_categories" class="panel-body">

                    </div>
                </div>
            </div>
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
<div class="well hidden" id="template_price" style="position: relative">
    <div class="wb-close-container">
        <a class="icon wb-close"></a>
    </div>
    <div class="form-group">
        <label class="control-label">Type & Size</label>
        {{--<input type="text" class="form-control type_size" id="type_size" name="type_size" placeholder="Enter Type & Size"
               autocomplete="off" value="">--}}
        <select class="form-control type_size" id="type_size" name="type_size" style="width: 100%;">
            <option value="">Select Type & Size</option>
            @foreach($product_types as $id => $product_type)
                <option value="{{ $id }}">{{ $product_type }}</option>
            @endforeach
        </select>
    </div>
    <div class="form-group">
        <div class="row">
            <div class="col-md-6">
                <label class="control-label">MRP</label>
                <input type="text" class="form-control mrp" id="mrp" name="mrp" placeholder="Enter MRP"
                       autocomplete="off" value="">
            </div>
            <div class="col-md-6">
                <label class="control-label">Stock</label>
                <input type="text" class="form-control stock" id="stock" name="stock" placeholder="Enter Stock"
                       autocomplete="off" value="">
            </div>
        </div>
    </div>
    <div class="form-group quantity_container_copy" id="quantity_container_copy">
        <div class="template_quantity_copy row hidden" id="template_quantity_copy">
            <div style="padding-top:10px;">
                <div class="col-md-4">
                    <input type="text" class="form-control quantity_from_copy" id="quantity_from" name="quantity_from" placeholder="From" autocomplete="off" value="">
                </div>
                <div class="col-md-4">
                    <input type="text" class="form-control quantity_to_copy" id="quantity_to" name="quantity_to" placeholder="To" autocomplete="off" value="">
                </div>
                <div class="col-md-3">
                    <input type="text" class="form-control price_copy" id="price" name="price" placeholder="Price" autocomplete="off" value="">
                </div>
                <div class="col-md-1">
                    <a class="icon wb-close"></a>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-md-4">
                <label class="control-label">Quantity From</label>
                <input type="text" class="form-control quantity_from" id="quantity_from" name="quantity_from" placeholder="From" autocomplete="off" value="1" readonly>
            </div>
            <div class="col-md-4">
                <label class="control-label">Quantity To</label>
                <input type="text" class="form-control quantity_to" id="quantity_to" name="quantity_to" placeholder="To" autocomplete="off" value="">
            </div>
            <div class="col-md-3">
                <label class="control-label">Price</label>
                <input type="text" class="form-control price" id="price" name="price" placeholder="Price" autocomplete="off" value="">
            </div>
        </div>

    </div>
    <div class="pull-right">
        <a class="add_more_range_copy" href="#" data-toggle="add-more" data-template="#template_quantity_copy"
           data-close=".wb-close" data-container="#quantity_container_copy"
           data-count="0"
           data-addindex='[{"selector":".quantity_from_copy","attr":"name"},{"selector":".quantity_to_copy","attr":"name"},{"selector":".price_copy","attr":"name"}]'
           data-increment='[{"selector":".quantity_from_copy","attr":"id"},{"selector":".quantity_to_copy","attr":"id"},{"selector":".price_copy","attr":"id"}]'><i
                    class="fa fa-plus-circle"></i>&nbsp;&nbsp;Add more Range</a>
    </div>
    <div class="clearfix"></div>
</div>

<div class="row hidden" id="template_quantity">
    <div style="padding-top:10px;">
        <div class="col-md-4">
            <input type="text" class="form-control quantity_from" id="quantity_from-0" name="quantity_from[0]" placeholder="From" autocomplete="off" value="">
        </div>
        <div class="col-md-4">
            <input type="text" class="form-control quantity_to" id="quantity_to-0" name="quantity_to[0]" placeholder="To" autocomplete="off" value="">
        </div>
        <div class="col-md-3 col-xs-10">
            <input type="text" class="form-control price" id="price-0" name="price[0]" placeholder="Price" autocomplete="off" value="">
        </div>
        <div class="col-md-1 col-xs-2">
            <a class="icon wb-close"></a>
        </div>
    </div>
</div>





@if(isset($product->id))
    @foreach($type_sizes as $key_main2=>$type_size2 )
        <div class="row hidden" id="template_quantity_dyn-{{ $key_main2 }}">
            <div style="padding-top:10px;">
                <div class="col-md-4">
                    <input type="text" class="form-control quantity_from" id="quantity_from-{{ $key_main2 }}" name="quantity_from[{{ $key_main2 }}]" placeholder="From" autocomplete="off" value="">
                </div>
                <div class="col-md-4">
                    <input type="text" class="form-control quantity_to" id="quantity_to-{{ $key_main2 }}" name="quantity_to[{{ $key_main2 }}]" placeholder="To" autocomplete="off" value="">
                </div>
                <div class="col-md-3 col-xs-10">
                    <input type="text" class="form-control price" id="price-{{ $key_main2 }}" name="price[{{ $key_main2 }}]" placeholder="Price" autocomplete="off" value="">
                </div>
                <div class="col-md-1 col-xs-2">
                    <a class="icon wb-close"></a>
                </div>
            </div>
        </div>
    @endforeach
@endif
@include('admin.pages.products.type-size-modal')
@endpush


@push('page_scripts')
<script>
    $(document).ready(function() {
        var sidebar_categories = $('#sidebar_categories');

        @if(isset($product->id))

            var url = '{{ route('admin.products.sidebar_categories',[$product->id]) }}';

        @else

            url = '{{ route('admin.products.sidebar_categories') }}';

        @endif

        $.ajax({
            type: "GET",
            url: url,
            success: function (data) {
                sidebar_categories.append(data.content);
            }
        });
        $('#dvUploadImage').on('close_data.success', function (e) {
            $('#is_image').val(0);
        });
        $('#dvUploadImageOther').on('close_data.success', function (e) {
            $('#is_images').val(0);
        });
        $('#dvBrochureUpload').on('close_data.success', function (e) {
            $('#is_brochure').val(0);
        });

        jQuery.validator.addMethod("allRequired", function(value, elem){
            // Use the name to get all the inputs and verify them
            var name = elem.name;
            return  $('input[name="'+name+'"]').map(function(i,obj){return $(obj).val();}).get().every(function(v){ return v; });
        });


        var $validator = $('#add-product-form').validate({
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
                tax: {
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
                tax: {
                    required: "Tax is required",
                    digits: "Invalide data",
                    maxlength: "Invalide data"
                }
            }
        });

        $("#type_size-0").rules("add", {
            required:true,
            messages: {
                required: "Type size is required."
            }
        });

        $("#price-0-0").rules("add", {
            required:true,
            messages: {
                required: "Price is required."
            }
        });

        /*$( "input[id^='mrp-']").rules("add", {
            required:true,
            messages: {
                required: "MRP is required."
            }
        });*/

        var pageModal = $('#myTypeSizeModal');
        /*pageModal.on('af.success','#add-type-size-form',function() {
            $(this).trigger('hidden.bs.modal');
            pageModal.modal('hide');

            var select_id = $(this);
            console.log(select_id);
        });*/

        pageModal.on('modal_hide.post_activity',function(e,data){
            /*console.log(data);*/
        });


        $(document).on("click", '#select_all', function(e) {
            e.preventDefault();
            $('.list-group input[type="checkbox"]').attr('checked', false);

        });


        /*$('#select_all').on('ifUnchecked', function(event){
            $('input[type="checkbox"]').attr('checked', false);
        });

        $('#select_all').on('ifChecked', function(event){
            $('input[type="checkbox"]').attr('checked', true);
        });*/

    });
</script>
<script>
    $(function () {
        //bootstrap WYSIHTML5 - text editor
        $("#description").wysihtml5();
    });
    function getDeliveryCost(val) {
        if(val == '') {
            val = 0;
        }
        var default_delivery = "{{ Utility::settings('delivery_charge') }}";
        var total_cost = val*parseInt(default_delivery);
        $('#total_delivery_cost').text(total_cost);
    }
</script>
@endpush
