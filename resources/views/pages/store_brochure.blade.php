<!DOCTYPE html>
<html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <title>{{ $store->name }} - Brochure </title>
    <style>
        @page {
            margin: 0;
            background: #D8E8F1 !important;
        }
        *{
            color: #111;
        }
        body {
            padding: 0;
            margin: 0;
        }
        .page-break {
            page-break-after: always;
        }
        .row:before,
        .row:after
        {
            display: table;
            content: " ";
        }
        .row:after
        {
            clear: both;
        }
        .row {
            margin-right: -15px;
            margin-left: -15px;
        }

    </style>

</head>
<body>
<div style="width: 100%; height: 100%; background-color: #D8E8F1 !important;">
<div style="width:100%;">
    <div class="row">
        <div>
            <table width="100%" border="0" style="border: none; background: #12467F; color:#E7F1F7 !important; padding-right: 5%; padding-left: 5%">
                <tbody style="border: none;">
                <tr style="border: none;">
                    <td style="border: none; text-align:center;" width="100%">
                        <h1 style="padding-bottom:0px; margin-bottom:0px; font-size:57px;">{{ $store->name }}</h1>
                        <p style="padding-top:0px; margin-top:0px; font-size: 26px; margin-bottom: 0px;">{{ $store->short_description }}</p>

                        <p style="margin-top: 10px; font-size: 23px;">
                            @if(!empty($store->city))
                                {{ $store->city }}
                            @endif
                            @if(!empty($store->phone))
                                , Phone : {{ $store->phone }} &nbsp;
                            @endif
                        </p>
                    </td>
                </tr>
                </tbody>
            </table>
        </div>
    </div>
</div>

<div style="color: #082D33; width: 100%; padding-top:20px; ">
    <div style="text-align: center; ">
    @if(!empty($store->image))
        <img src="{{ asset($store->image) }}" >
    @endif
    </div>
</div>

<div style="color: #082D33; width: 700px; padding-top:20px; padding-bottom: 30px; padding-left: 5%">
    <div style="text-align: center; ">
        <div style="width: 100%; ">
            <h3 style="text-align: center;" >{!! strip_tags($store->description) !!} </h3>
        </div>
    </div>
</div>
<div class="page-break"></div>


<?php
$allChilds = [];
$allChildIds = [];
?>
@foreach($categories as $category)
    @foreach($category->childs as $childcat)
        @if(!in_array($childcat->id,$allChildIds))
            <?php
            $allChilds[] = $childcat;
            $allChildIds[] = $childcat->id;
            ?>
        @endif
    @endforeach
@endforeach


<?php $no_child = 1; ?>

    @foreach($allChilds as $child)
        @if(in_array($child->id,$store_cats))
            @if($no_child==1)
                {{--<div style="padding-top:25px;">--}}
                <div class="row" style="width:95%; padding-left: 4.5%; padding-top:50px;">
                    @endif
            <div style="width:31.33%; text-align: center; float:left; padding:0 1%;">
                <img src="{{ empty($child->image) ? asset('images/no-image.jpg') : asset($child->image) }}" width="100%" >
                <p style="width:100%;padding-top: 5px; color:#4B0081; font-size: 16px; ">{{ $child->name }}</p> {{--\Illuminate\Support\Str::limit($child->name, $limit = 20, $end = '...')--}}
            </div>

            @if($no_child % 3 == 0)
            <div style="clear: both;"></div>
            </div>
            <div class="row" style="width:95%; padding-left: 4.5%; padding-top:15px;">
            @endif

            @if($no_child % 9 == 0)
                <div style="clear: both;"></div>
                </div>

                @if(count($store_cats)!=$no_child)
                    <div class="page-break"></div>
                    <div class="row" style="width:95%; padding-left: 4.5%; padding-top:50px;">
                @endif

            @endif
            <?php $no_child++; ?>
        @endif
            @endforeach

    <div class="row" style="color: #082D33; width:90%; padding-left: 7.3%; padding-top:0px;">
    <div style="text-align: center; ">
        <table width="100%" border="0" style="border: none;  color:#000 !important;">
            <tbody style="border: none;">
                <tr style="border: none;">
                    <td style="border: none; text-align:justify;" width="100%">
                        <p style="text-align: center;" >{!! $store->footer_description !!} </p>
                    </td>
                </tr>
            </tbody>
        </table>
        <table width="100%" border="0" style="border: none; background: #ffe11b; color:#000 !important; margin-top:15px;">
            <tbody style="border: none;">
            <tr style="border: none;">
                <td style="border: none; text-align:center;" width="100%">
                    <h1 style="padding-top:10px; margin-top:0; padding-bottom:0; margin-bottom:0; color:#000">{{ $store->name }}</h1>
                    @if(!empty($store->location) && !empty($store->district))
                        <h4 style="padding-top:0px; margin-top:0px; font-size:20px;" ><b style="color:#000">{!!  nl2br($store->location ) !!} {!! !empty($store->location) ? '<br> ' . $store->district : $store->district !!}</b></h4>
                    @endif
                    <p style="color:#000; padding-top:5px; margin-top:0;">
                        <b>
                        @if(!empty($store->phone))
                            Phone : {{ $store->phone }} &nbsp;
                        @endif
                        @if(!empty($store->email))
                            Email : {{ $store->email }}
                        @endif
                        <br>Website : {{ config('app.domain') . '/' . $store->username }}
                        </b>
                    </p>
                </td>
            </tr>
            </tbody>
        </table>
    </div>
</div>
</div>
</body>
</html>