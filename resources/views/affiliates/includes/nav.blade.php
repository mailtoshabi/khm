    <nav style="padding-top: 5px;">
        <div id="mySidenav" class="sidenav">
            <div class="container" style="background-color: #2874f0; padding-top: 10px;padding-bottom: 10px;">
                <a style="color:#fff;" href="{{ route('all.slug',$affiliate_slug) }}">
                <span class="glyphicon glyphicon-home"></span>&nbsp;&nbsp;
                <span class="sidenav-heading">Home</span>
                </a>
                <a href="javascript:void(0)" class="closebtn" onclick="closeNav()">×</a>
            </div>
            <ul class="nav navbar-nav ">
                @foreach($categoryLists as $categoryList)
                    <li class="">
                        <a href="{{ $categoryList->childs->count()!=0 ? '#':route('affiliate.all.slug',[$affiliate_slug, $categoryList->slug]) }}" class="dropdown-toggle" @if($categoryList->childs->count() !=0) data-toggle="dropdown" @endif>
                            &nbsp;&nbsp;{{ $categoryList->name }} @if($categoryList->childs->count() !=0) <span class="caret"> @endif</span></a>
                        @if($categoryList->childs->count() !=0)
                            <ul class="dropdown-menu" role="menu">
                                @foreach($categoryList->childs as $child01)
                                    <li><a href="{{ route('affiliate.all.slug',[$affiliate_slug, $child01->slug]) }}">{{ $child01->name }}</a></li>
                                    <li class="divider"></li>
                                @endforeach
                            </ul>
                        @endif
                    </li>
                    <li class="nav-divider"></li>
                @endforeach
                <li class="">
                    <a href="{{ route('affiliate.category.all',$affiliate_slug) }}" class="dropdown-toggle" >
                        &nbsp;&nbsp;More</a>
                </li>
            </ul>
        </div>
    </nav>