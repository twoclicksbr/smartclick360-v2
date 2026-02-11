<head>
    <title>@yield('title', 'SmartClick360°')</title>
    <meta charset="utf-8" />
    <meta name="description" content="SmartClick360° — ERP web completo para gestão da sua empresa. Controle vendas, compras, financeiro, estoque e muito mais em uma única plataforma." />
    <meta name="keywords" content="ERP, SaaS, gestão empresarial, controle de vendas, financeiro, estoque, compras, sistema para empresas, SmartClick360" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <meta name="theme-color" content="#d8842a" />
    <meta name="author" content="SmartClick360" />

    <!--begin::Open Graph-->
    <meta property="og:locale" content="pt_BR" />
    <meta property="og:type" content="website" />
    <meta property="og:title" content="@yield('title', 'SmartClick360° — ERP Web para sua Empresa')" />
    <meta property="og:description" content="Gerencie vendas, compras, financeiro e muito mais em uma única plataforma." />
    <meta property="og:url" content="https://smartclick360.com" />
    <meta property="og:site_name" content="SmartClick360°" />
    <!--end::Open Graph-->

    <link rel="shortcut icon" href="{{ asset('assets/media/logos/favicon.ico') }}" />

    <!--begin::Fonts(mandatory for all pages)-->
    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Inter:300,400,500,600,700" />
    <!--end::Fonts-->

    <!--begin::Vendor Stylesheets(used for this page only)-->
    <link href="{{ asset('assets/plugins/custom/fullcalendar/fullcalendar.bundle.css') }}" rel="stylesheet" type="text/css" />
    <link href="{{ asset('assets/plugins/custom/datatables/datatables.bundle.css') }}" rel="stylesheet" type="text/css" />
    <!--end::Vendor Stylesheets-->

    <!--begin::Global Stylesheets Bundle(mandatory for all pages)-->
    <link href="{{ asset('assets/plugins/global/plugins.bundle.css') }}" rel="stylesheet" type="text/css" />
    <link href="{{ asset('assets/css/style.bundle.css') }}" rel="stylesheet" type="text/css" />
    <!--end::Global Stylesheets Bundle-->

    @stack('styles')
</head>