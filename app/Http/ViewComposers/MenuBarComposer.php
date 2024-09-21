<?php
namespace App\Http\ViewComposers;

use Illuminate\View\View;

class MenuBarComposer
{


    public function __construct()
    {
        // Dependencies automatically resolved by service container...
    }

    /**
     * Bind data to the view.
     *
     * @param  View $view
     * @return void
     */
    public function compose(View $view)
    {
        $menus = [
            [
                "label" => "Dashboard",
                "route" => "admin.index",
                "icon" => "fa fa-dashboard",
                "roles" => "admin,affiliate,clinic,brand",
                "sub_routes" => ['admin.index'],
                "sub_route_prefixes" => null,
            ],
            [
                "label" => "Products",
                "route" => "admin.products.index",
                "icon" => "fa fa-product-hunt",
                "roles" => "admin",
                "sub_routes" => ['admin.products.index','admin.products.create','admin.products.edit'],
                "sub_route_prefixes" => null,
                "sub_menus" => [
                    [
                        "label" => "Add New",
                        "route" => "admin.products.create",
                        "icon" => "fa fa-plus",
                        "sub_routes" => ['admin.products.create'],
                        "sub_route_prefixes" => null,
                    ],
                    [
                        "label" => "Products",
                        "route" => "admin.products.index",
                        "icon" => "fa fa-list",
                        "sub_routes" => ['admin.products.index','admin.products.edit'],
                        "sub_route_prefixes" => null,
                    ],
                    [
                        "label" => "Type & Size",
                        "route" => "admin.products.types.index",
                        "icon" => "fa fa-briefcase",
                        "sub_routes" => ['admin.products.types.index'],
                        "sub_route_prefixes" => null,
                    ]

                ]
            ],
            [
                "label" => "Categories",
                "route" => "admin.categories.index",
                "icon" => "fa fa-briefcase",
                "roles" => "admin",
                "sub_routes" => ['admin.categories.index'],
                "sub_route_prefixes" => null,
            ],
            [
                "label" => "Brands",
                "route" => "admin.brands.index",
                "icon" => "fa fa-adjust",
                "roles" => "admin",
                "sub_routes" => ['admin.brands.index'],
                "sub_route_prefixes" => null,
            ],
            [
                "label" => "Sales",
                "route" => "admin.sales.index",
                "icon" => "fa fa-inr",
                "roles" => "admin",
                "sub_routes" => ['admin.sales.index','admin.sales.show'],
                "sub_route_prefixes" => null,
            ],
            [
                "label" => "One Click Purchase",
                "route" => "admin.oneclick_purchase.index",
                "icon" => "fa fa-inr",
                "roles" => "admin",
                "sub_routes" => ['admin.oneclick_purchase.index'],
                "sub_route_prefixes" => null,
            ],
            // [
            //     "label" => "Prescription",
            //     "route" => "admin.prescriptions.index",
            //     "icon" => "fa fa-list",
            //     "roles" => "admin",
            //     "sub_routes" => ['admin.prescriptions.index'],
            //     "sub_route_prefixes" => null,
            // ],
            [
                "label" => "Customers",
                "route" => "admin.customers.index",
                "icon" => "fa fa-user",
                "roles" => "admin",
                "sub_routes" => ['admin.customers.index'],
                "sub_route_prefixes" => null,
            ],
            /*[
                "label" => "Store",
                "route" => "admin.stores.index",
                "icon" => "fa fa-building",
                "roles" => "admin",
                "sub_routes" => ['admin.stores.index','admin.stores.create','admin.stores.edit'],
                "sub_route_prefixes" => null,
                "sub_menus" => [
                    [
                        "label" => "Stores",
                        "route" => "admin.stores.index",
                        "icon" => "fa fa-building",
                        "sub_routes" => ['admin.stores.index'],
                        "sub_route_prefixes" => null,
                    ],
                    [
                        "label" => "Add Store",
                        "route" => "admin.stores.create",
                        "icon" => "fa fa-plus",
                        "sub_routes" => ['admin.stores.create'],
                        "sub_route_prefixes" => null,
                    ],
                    [
                        "label" => "Pins",
                        "route" => "admin.stores.pins.index",
                        "icon" => "fa fa-key",
                        "sub_routes" => ['admin.stores.pins.index'],
                        "sub_route_prefixes" => null,
                    ],
                ],
            ],*/
            // [
            //     "label" => "Clinic",
            //     "route" => "admin.clinics.index",
            //     "icon" => "fa fa-building",
            //     "roles" => "admin",
            //     "sub_routes" => ['admin.clinics.index','admin.clinics.create','admin.clinics.edit','admin.clinics.types.index'],
            //     "sub_route_prefixes" => null,
            //     "sub_menus" => [
            //         [
            //             "label" => "Clinics",
            //             "route" => "admin.clinics.index",
            //             "icon" => "fa fa-building",
            //             "sub_routes" => ['admin.clinics.index'],
            //             "sub_route_prefixes" => null,
            //         ],
            //         [
            //             "label" => "Add Clinic",
            //             "route" => "admin.clinics.create",
            //             "icon" => "fa fa-plus",
            //             "sub_routes" => ['admin.clinics.create'],
            //             "sub_route_prefixes" => null,
            //         ],
            //         [
            //             "label" => "Clinic Types",
            //             "route" => "admin.clinics.types.index",
            //             "icon" => "fa fa-briefcase",
            //             "roles" => "admin",
            //             "sub_routes" => ['admin.clinics.types.index'],
            //             "sub_route_prefixes" => null,
            //         ],
            //         [
            //             "label" => "Cities",
            //             "route" => "admin.cities.index",
            //             "icon" => "fa fa-home",
            //             "roles" => "admin",
            //             "sub_routes" => ['admin.cities.index'],
            //             "sub_route_prefixes" => null,
            //         ],
            //     ],
            // ],
            // [
            //     "label" => "Treatment",
            //     "route" => "admin.treatments.index",
            //     "icon" => "fa fa-building",
            //     "roles" => "admin",
            //     "sub_routes" => ['admin.treatments.index','admin.treatments.create','admin.treatments.edit'],
            //     "sub_route_prefixes" => null,
            //     "sub_menus" => [
            //         [
            //             "label" => "Treatments",
            //             "route" => "admin.treatments.index",
            //             "icon" => "fa fa-building",
            //             "sub_routes" => ['admin.treatments.index'],
            //             "sub_route_prefixes" => null,
            //         ],
            //         [
            //             "label" => "Add Treatment",
            //             "route" => "admin.treatments.create",
            //             "icon" => "fa fa-plus",
            //             "sub_routes" => ['admin.treatments.create'],
            //             "sub_route_prefixes" => null,
            //         ]
            //     ],
            // ],
            // [
            //     "label" => "Doctor",
            //     "route" => "admin.doctors.index",
            //     "icon" => "fa fa-building",
            //     "roles" => "admin",
            //     "sub_routes" => ['admin.doctors.index','admin.doctors.create','admin.doctors.edit'],
            //     "sub_route_prefixes" => null,
            //     "sub_menus" => [
            //         [
            //             "label" => "Doctors",
            //             "route" => "admin.doctors.index",
            //             "icon" => "fa fa-building",
            //             "sub_routes" => ['admin.doctors.index'],
            //             "sub_route_prefixes" => null,
            //         ],
            //         [
            //             "label" => "Add Doctor",
            //             "route" => "admin.doctors.create",
            //             "icon" => "fa fa-plus",
            //             "sub_routes" => ['admin.doctors.create'],
            //             "sub_route_prefixes" => null,
            //         ]
            //     ],
            // ],
            [
                "label" => "Affiliate Store",
                "route" => "admin.affiliates.index",
                "icon" => "fa fa-building",
                "roles" => "admin",
                "sub_routes" => ['admin.affiliates.index','admin.affiliates.create','admin.affiliates.edit'],
                "sub_route_prefixes" => null,
                "sub_menus" => [
                    [
                        "label" => "Affiliates",
                        "route" => "admin.affiliates.index",
                        "icon" => "fa fa-building",
                        "sub_routes" => ['admin.affiliates.index'],
                        "sub_route_prefixes" => null,
                    ],
                    [
                        "label" => "Add Affiliate",
                        "route" => "admin.affiliates.create",
                        "icon" => "fa fa-plus",
                        "sub_routes" => ['admin.affiliates.create'],
                        "sub_route_prefixes" => null,
                    ]
                ],
            ],
            [
                "label" => "Banners",
                "route" => "admin.banners.index",
                "icon" => "fa fa-briefcase",
                "roles" => "admin",
                "sub_routes" => ['admin.banners.index'],
                "sub_route_prefixes" => null,
            ],
            [
                "label" => "Sliders",
                "route" => "admin.sliders.index",
                "icon" => "fa fa-briefcase",
                "roles" => "admin",
                "sub_routes" => ['admin.sliders.index'],
                "sub_route_prefixes" => null,
            ],
            [
                "label" => "Subscribers",
                "route" => "admin.subscriber.index",
                "icon" => "fa fa-user",
                "roles" => "admin",
                "sub_routes" => ['admin.subscriber.index'],
                "sub_route_prefixes" => null,
            ],
            [
                "label" => "Settings",
                "route" => "admin.settings.general.edit",
                "icon" => "fa fa-cogs",
                "roles" => "admin",
                "sub_routes" => ['admin.settings.general.edit'],
                "sub_route_prefixes" => null,
                "sub_menus" => [
                    [
                        "label" => "General Settings",
                        "route" => "admin.settings.general.edit",
                        "icon" => "fa fa-cog",
                        "sub_routes" => ['admin.settings.general.edit'],
                        "sub_route_prefixes" => null,
                    ],
                    [
                        "label" => "Change Password",
                        "route" => "admin.settings.password.edit",
                        "icon" => "fa fa-key",
                        "sub_routes" => ['admin.settings.password.edit'],
                        "sub_route_prefixes" => null,
                    ],

                ]
            ],
            [
                "label" => "Categories",
                "route" => "admin.affiliate.categories.index",
                "icon" => "fa fa-briefcase",
                "roles" => "affiliate",
                "sub_routes" => ['admin.affiliate.categories.index'],
                "sub_route_prefixes" => null,
            ],
            // [
            //     "label" => "Categories",
            //     "route" => "admin.affiliate.categories.index",
            //     "icon" => "fa fa-briefcase",
            //     "roles" => "affiliate",
            //     "sub_routes" => ['admin.affiliate.categories.index','admin.affiliate.categories.child'],
            //     "sub_route_prefixes" => null,
            //     "sub_menus" => [
            //         [
            //             "label" => "Main Categories",
            //             "route" => "admin.affiliate.categories.index",
            //             "icon" => "fa fa-briefcase",
            //             "sub_routes" => ['admin.affiliate.categories.index'],
            //             "sub_route_prefixes" => null,
            //         ],
            //         [
            //             "label" => "Sub Categories",
            //             "route" => "admin.affiliate.categories.child",
            //             "icon" => "fa fa-briefcase",
            //             "sub_routes" => ['admin.affiliate.categories.child'],
            //             "sub_route_prefixes" => null,
            //         ]
            //     ],
            // ],
            [
                "label" => "Brands",
                "route" => "admin.affiliate.brands.index",
                "icon" => "fa fa-adjust",
                "roles" => "affiliate",
                "sub_routes" => ['admin.affiliate.brands.index'],
                "sub_route_prefixes" => null,
            ],
            [
                "label" => "Products",
                "route" => "admin.affiliate.products.index",
                "icon" => "fa fa-product-hunt",
                "roles" => "affiliate",
                "sub_routes" => ['admin.affiliate.products.index'],
                "sub_route_prefixes" => null,
            ],
            [
                "label" => "Banners",
                "route" => "admin.affiliate.banners.index",
                "icon" => "fa fa-briefcase",
                "roles" => "affiliate",
                "sub_routes" => ['admin.affiliate.banners.index'],
                "sub_route_prefixes" => null,
            ],
            [
                "label" => "Sales",
                "route" => "admin.affiliate.sales.index",
                "icon" => "fa fa-inr",
                "roles" => "affiliate",
                "sub_routes" => ['admin.affiliate.sales.index','admin.affiliate.sales.show'],
                "sub_route_prefixes" => null,
            ],
            [
                "label" => "One Click Purchase",
                "route" => "admin.affiliate.oneclick_purchase.index",
                "icon" => "fa fa-inr",
                "roles" => "affiliate",
                "sub_routes" => ['admin.affiliate.oneclick_purchase.index'],
                "sub_route_prefixes" => null,
            ],
            // [
            //     "label" => "Prescription",
            //     "route" => "admin.affiliate.prescriptions.index",
            //     "icon" => "fa fa-list",
            //     "roles" => "affiliate",
            //     "sub_routes" => ['admin.affiliate.prescriptions.index'],
            //     "sub_route_prefixes" => null,
            // ],


            [
                "label" => "Settings",
                "route" => "admin.settings.password.edit",
                "icon" => "fa fa-cogs",
                "roles" => "affiliate",
                "sub_routes" => ['admin.settings.general.edit'],
                "sub_route_prefixes" => null,
                "sub_menus" => [
                    [
                        "label" => "General Settings",
                        "route" => "admin.affiliate.show.meta",
                        "icon" => "fa fa-cog",
                        "sub_routes" => ['admin.affiliate.show.meta'],
                        "sub_route_prefixes" => null,
                    ],
            //         [
            //             "label" => "Change Password",
            //             "route" => "admin.settings.password.edit",
            //             "icon" => "fa fa-key",
            //             "sub_routes" => ['admin.settings.password.edit'],
            //             "sub_route_prefixes" => null,
            //         ],

                ]
            ],


            // [
            //     "label" => "Doctor",
            //     "route" => "admin.clinics.doctor",
            //     "icon" => "fa fa-tag",
            //     "roles" => "clinic",
            //     "sub_routes" => ['admin.clinics.doctor'],
            //     "sub_route_prefixes" => null,
            // ],




            // [
            //     "label" => "Settings",
            //     "route" => "admin.settings.password.edit",
            //     "icon" => "fa fa-key",
            //     "roles" => "clinic",
            //     "sub_routes" => ['admin.settings.password.edit'],
            //     "sub_route_prefixes" => null,
            // ],
            [
                "label" => "Products",
                "route" => "admin.brands.products.index",
                "icon" => "fa fa-product-hunt",
                "roles" => "brand",
                "sub_routes" => ['admin.brands.products.index','admin.brands.products.create','admin.brands.products.edit'],
                "sub_route_prefixes" => null,
                "sub_menus" => [
                    [
                        "label" => "Add New",
                        "route" => "admin.brands.products.create",
                        "icon" => "fa fa-plus",
                        "sub_routes" => ['admin.brands.products.create'],
                        "sub_route_prefixes" => null,
                    ],
                    [
                        "label" => "Products",
                        "route" => "admin.brands.products.index",
                        "icon" => "fa fa-list",
                        "sub_routes" => ['admin.brands.products.index','admin.brands.products.edit'],
                        "sub_route_prefixes" => null,
                    ]

                ]
            ],
            [
                "label" => "Dealers",
                "route" => "admin.brands.dealer.index",
                "icon" => "fa fa-list",
                "roles" => "brand",
                "sub_routes" => ['admin.brands.dealer.index'],
                "sub_route_prefixes" => null,
            ],
            [
                "label" => "SEO Tools",
                "route" => "admin.brands.show.meta",
                "icon" => "fa fa-key",
                "roles" => "brand",
                "sub_routes" => ['admin.brands.show.meta'],
                "sub_route_prefixes" => null,
            ],

        ];
        $view->with('menus', $menus);
    }
}
//"/calendar"
