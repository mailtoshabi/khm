@if(empty($menu['sub_menus']))

    @if(isset($menu["roles"]))
        @ability($menu["roles"],'')
        <li class="treeview {{ Nav::setActive(app('request')->route(), $menu) }}" >
            <a href="{{ route($menu['route']) }}">
                <i class="{{ $menu['icon'] }}"></i> <span>{{ $menu['label'] }}</span>
            </a>
        </li>
        @endability
    @else
        <li class="treeview {{ Nav::setActive(app('request')->route(), $menu) }}" >
            <a href="{{ route($menu['route']) }}">
                <i class="{{ $menu['icon'] }}"></i> <span>{{ $menu['label'] }}</span>
                @if($level == 0 && $menu['route'] == 'admin.sales.index' )
                    <span class="pull-right-container">
                        <span class="label label-primary pull-right">{{ $new_sale_count }}</span>
                    </span>
                @endif
            </a>
        </li>
    @endif
@else

    @if(isset($menu["roles"]))
        @ability($menu["roles"],'')
            <li class="{{ Nav::setActive(app('request')->route(), $menu, 'active open') }} treeview">
                <a href="#">
                    <i class="{{ $menu['icon'] }}"></i> <span>{{ $menu['label'] }}</span>
                        <span class="pull-right-container">
                          <i class="fa fa-angle-left pull-right"></i>
                        </span>
                </a>
                <ul class="treeview-menu">
                    @foreach($menu['sub_menus'] as $menu)
                        @include('admin.includes.side-menu-item', ['level' => $level+1, 'menu' => $menu])
                    @endforeach
                </ul>
            </li>
        @endability
    @else
        <li class="{{ Nav::setActive(app('request')->route(), $menu, 'active open') }} treeview">
            <a href="#">
                <i class="{{ $menu['icon'] }}"></i> <span>{{ $menu['label'] }}</span>
                        <span class="pull-right-container">
                          <i class="fa fa-angle-left pull-right"></i>
                        </span>
            </a>
            <ul class="treeview-menu">
                @foreach($menu['sub_menus'] as $menu)
                    @include('admin.includes.side-menu-item', ['level' => $level+1, 'menu' => $menu])
                @endforeach
            </ul>
        </li>
    @endif
@endif
