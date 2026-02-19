<head>
    <title>@yield('title', 'Painel Administrativo - SmartClick360')</title>
    <meta charset="utf-8" />
    <meta name="description" content="Painel Administrativo — Gestão de tenants, planos e assinaturas do SmartClick360." />
    <meta name="keywords" content="backoffice, painel administrativo, gestão de tenants, SaaS, SmartClick360" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <meta name="csrf-token" content="{{ csrf_token() }}" />
    <meta name="theme-color" content="#d8842a" />
    <meta name="author" content="SmartClick360" />

    <!--begin::Open Graph-->
    <meta property="og:locale" content="pt_BR" />
    <meta property="og:type" content="website" />
    <meta property="og:title" content="@yield('title', 'Painel Administrativo - SmartClick360')" />
    <meta property="og:description" content="Gestão de tenants, planos e assinaturas." />
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

    <!--begin::Custom Stylesheets-->
    <link href="{{ asset('assets/css/custom.css') }}" rel="stylesheet" type="text/css" />
    <!--end::Custom Stylesheets-->

    @stack('styles')
</head>
