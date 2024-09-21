<!DOCTYPE html>
<html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <title>Brochure - {{ $store->name }} </title>
    <link rel="stylesheet" type="text/css" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css">
    <style>
        .page-break {
            page-break-after: always;
        }
    </style>
</head>
<body>
<div class="container"> <!--container-->
    <div class="row">
        <div role="tabpanel">

            <div class="col-sm-12" style="padding-top:30px;">
                <div class="row">
                    <div>
                        <table class="table table-borderless" border="0" style="border: none; background: #2874f0; color:#fff;">
                            <tbody style="border: none;">
                            <tr style="border: none;">
                                <td style="padding: 0px; border: none;" width="40%" >
                                    <img src="{{ asset($store->brochure) }}" >
                                </td>
                                <td style="border: none;" width="60%">
                                    @if(!empty($store->image))
                                        <img src="{{ asset($store->image) }}" >
                                    @endif
                                    <h2 >{{ $store->name }}</h2>
                                    @if(!empty($store->location) && !empty($store->district))
                                        <h5><b>{!!  nl2br($store->location ) !!} {{ !empty($store->location) ? ', ' . $store->district : $store->district  }}</b></h5>
                                    @endif
                                    <p>
                                        @if(!empty($store->phone))
                                            Phone : {{ $store->phone }} &nbsp;
                                        @endif
                                        @if(!empty($store->email))
                                            Email : {{ $store->email }}
                                        @endif
                                        <br>Website : {{ config('app.domain') . '/' . $store->username }}
                                    </p>
                                </td>
                            </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <div class="col-md-12" style="background:#212529;color: #fff; width: 100%; padding-top:20px; padding-bottom: 30px; padding-right: 5%; padding-left: 5%">
                <div class="row" style="text-align: center; width: 100%">
                    <div class="col-md-8 text-center" style="width: 70%; padding-left:15%;">
                        <h2 >About Us</h2>
                        <hr>
                        <p >{!! $store->description !!} </p>
                    </div>
                </div>
            </div>

            <div class="page-break"></div>

            <div class="col-sm-12" style="padding-right: 5%; padding-left: 5%;">
                <div class="row">
                    <div class="col-md-12">
                        <div class="">
                            <div class="">
                                <div class="">
                                    <div class="row">
                                        <div class="col-md-12 text-center">
                                            <h2 class="section-heading">Our Product Range</h2>
                                        </div>

                                        <div class="">
                                            <div style="width: 100%; border: none" >
                                                @foreach($categories as $category)
                                                    <div style="width:100%;border: none;">
                                                        <h3 >{{ $category->name }}</h3>
                                                    </div>
                                                    <?php
                                                    $numOfCols = 4;
                                                    $rowCount = 0;
                                                    $bootstrapColWidth = 12 / $numOfCols ;
                                                    ?>

                                                    <div class="col-md-12">
                                                        <div class="row">
                                                            @foreach($category->childs as $child)
                                                                @if(in_array($child->id,$store_cats))

                                                                    <div style="width: 23%; margin-right:2%; float: left"><a target="_blank" href="{{ route('category.products.show',$child->id) }}"><img src="{{ empty($child->image) ? asset('images/no-image.jpg') : asset($child->image) }}" width="90%" ></a><p style="width:100%; text-align: center">{{ $child->name }}</p></div>

                                                                    <?php $rowCount++; ?>
                                                                    @if($rowCount % $numOfCols == 0)
                                                        </div><div class="row">
                                                            @endif

                                                            @endif
                                                            @endforeach
                                                        </div>
                                                    </div>
                                                @endforeach

                                            </div>

                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </div>
</body>
</html>