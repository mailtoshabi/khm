@extends('admin.layouts.default')

@section('title','Dashboard')

@section('content')

        <!-- Content Header (Page header) -->
<section class="content-header">
    <h1>
        Dashboard
        <small>Control panel</small>
    </h1>
    <ol class="breadcrumb">
        <li><a href="#"><i class="fa fa-dashboard"></i> Home</a></li>
        <li class="active">Dashboard</li>
    </ol>
</section>


<!-- Main content -->
<section class="content">
    <!-- Small boxes (Stat box) -->
    <div class="row">
        @role('admin')
            <!-- Sales -->
            <div class="col-lg-3 col-xs-6">
                <!-- small box -->
                <div class="small-box bg-green">
                    <div class="inner">
                        <h3>{{ $sale_count }}</h3>

                        <p>Sales</p>
                    </div>
                    <div class="icon">
                        <i class="fa fa-inr"></i>
                    </div>
                    <a href="{{ route('admin.sales.index') }}" class="btn small-box-footer">More info <i class="fa fa-arrow-circle-right"></i></a>
                </div>
            </div>

            <!-- Customers -->
            <div class="col-lg-3 col-xs-6">
                <!-- small box -->
                <div class="small-box bg-aqua">
                    <div class="inner">
                        <h3>{{ $customer_count }}</h3>

                        <p>Customers</p>
                    </div>
                    <div class="icon">
                        <i class="ion ion-person-add"></i>
                    </div>
                    <a href="{{ route('admin.customers.index') }}" class="small-box-footer">More info <i class="fa fa-arrow-circle-right"></i></a>
                </div>
            </div>

            <!-- Products -->
            <div class="col-lg-3 col-xs-6">
                <!-- small box -->
                <div class="small-box bg-yellow">
                    <div class="inner">
                        <h3>{{ $product_count }}</h3>

                        <p>Products</p>
                    </div>
                    <div class="icon">
                        <i class="fa fa-clone"></i>
                    </div>
                    <a href="{{ route('admin.products.index') }}" class="small-box-footer">More info <i class="fa fa-arrow-circle-right"></i></a>
                </div>
            </div>

            <!-- Stores -->
            <div class="col-lg-3 col-xs-6">
                <!-- small box -->
                <div class="small-box bg-red">
                    <div class="inner">
                        <h3>{{ $store_count }}</h3>

                        <p>Affiliates</p>
                    </div>
                    <div class="icon">
                        <i class="fa fa-bank"></i>
                    </div>
                    <a href="{{ route('admin.affiliates.index') }}" class="small-box-footer">More info <i class="fa fa-arrow-circle-right"></i></a>
                </div>
            </div>
        @endrole

        @role('affiliate')
        <!-- Products Affiliate-->
        <div class="col-lg-3 col-xs-6">
            <!-- small box -->
            <div class="small-box bg-yellow">
                <div class="inner">
                    <h3>{{ $product_count }}</h3>

                    <p>Products</p>
                </div>
                <div class="icon">
                    <i class="fa fa-clone"></i>
                </div>
                <a href="{{ route('admin.affiliate.products.index') }}" class="small-box-footer">More info <i class="fa fa-arrow-circle-right"></i></a>
            </div>
        </div>

        <!-- Sales -->
        <div class="col-lg-3 col-xs-6">
            <!-- small box -->
            <div class="small-box bg-green">
                <div class="inner">
                    <h3>{{ $sale_count }}</h3>

                    <p>Sales</p>
                </div>
                <div class="icon">
                    <i class="fa fa-inr"></i>
                </div>
                <a href="{{ route('admin.affiliate.sales.index') }}" class="btn small-box-footer">More info <i class="fa fa-arrow-circle-right"></i></a>
            </div>
        </div>

        <!-- OneClick -->
        <div class="col-lg-3 col-xs-6">
            <!-- small box -->
            <div class="small-box bg-red">
                <div class="inner">
                    <h3>{{ $oneclick_count }}</h3>

                    <p>Oneclick Sales</p>
                </div>
                <div class="icon">
                    <i class="fa fa-inr"></i>
                </div>
                <a href="{{ route('admin.affiliate.oneclick_purchase.index') }}" class="btn small-box-footer">More info <i class="fa fa-arrow-circle-right"></i></a>
            </div>
        </div>
        @endrole

        @role('brand')
        <!-- Products -->
        <div class="col-lg-3 col-xs-6">
            <!-- small box -->
            <div class="small-box bg-yellow">
                <div class="inner">
                    <h3>{{ $product_count }}</h3>

                    <p>Products</p>
                </div>
                <div class="icon">
                    <i class="fa fa-clone"></i>
                </div>
                <a href="{{ route('admin.brands.products.index') }}" class="small-box-footer">More info <i class="fa fa-arrow-circle-right"></i></a>
            </div>
        </div>
        @endrole


    </div>
    <!-- /.row -->
    <!-- Main row -->

</section>
<!-- /.content -->

@stop
