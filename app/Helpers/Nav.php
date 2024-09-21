<?php


namespace App\Helpers;


class Nav
{

    public static function test()
    {
        return "active";
    }

    public static function setActive($route, $menu, $output = 'active')
    {
        $route_name = isset($menu['route']) ? $menu['route'] : null;
        $sub_route_names = isset($menu['sub_routes']) ? $menu['sub_routes'] : [];
        $sub_route_prefix = isset($menu['sub_route_prefixes']) ? $menu['sub_route_prefixes'] : [];

        if ($route->getName() == $route_name && !empty($route_name)) return $output;

        if (!empty($sub_route_names)) {
            $sub_route_names = is_array($sub_route_names) ? $sub_route_names : [$sub_route_names];
            foreach ($sub_route_names as $route_name) {
                if ($route->getName() == $route_name) return $output;
            }
        }

        if (!empty($sub_route_prefix)) {
            $sub_route_prefix = is_array($sub_route_prefix) ? $sub_route_prefix : [$sub_route_prefix];
            foreach ($sub_route_prefix as $route_prefix) {
                $prefix = $route->getPrefix();
                if (empty($prefix) || empty($route_prefix)) continue;
                if ($route->getPrefix() == $route_prefix) return $output;
            }
        }

        if(isset($menu['sub_menus']) && is_array($menu['sub_menus'])) {
            foreach($menu['sub_menus'] as $menu) {
                $class = self::setActive($route, $menu, $output);
                if($class) return $class;
            }
        }

        return false;
    }

}