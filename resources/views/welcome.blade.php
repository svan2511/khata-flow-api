<!DOCTYPE html>
<html class="scroll-smooth" lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
<meta charset="utf-8"/>
<meta content="width=device-width, initial-scale=1.0" name="viewport"/>
<title>{{ config('app.name', 'KhataFlow') }} - Smart Dukaan, Smart Hisaab</title>

<script src="https://cdn.tailwindcss.com?plugins=forms,container-queries"></script>
<link href="{{ asset('favicon.png') }}" rel="icon" type="image/png"/>
<link href="https://fonts.googleapis.com" rel="preconnect"/>
<link crossorigin="" href="https://fonts.gstatic.com" rel="preconnect"/>
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600&amp;family=Lexend:wght@400;500;600;700&amp;display=swap" rel="stylesheet"/>
<link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&amp;display=swap" rel="stylesheet"/>
<script id="tailwind-config">
        tailwind.config = {
            darkMode: "class",
            theme: {
                extend: {
                    "colors": {
                        "on-secondary-fixed": "#00201a",
                        "surface-dim": "#d8dbd8",
                        "tertiary-container": "#262e2d",
                        "background": "#f8faf7",
                        "secondary-fixed-dim": "#81d6c0",
                        "secondary": "#006b59",
                        "on-secondary-fixed-variant": "#005143",
                        "error": "#ba1a1a",
                        "on-surface": "#191c1b",
                        "outline": "#717976",
                        "on-primary-fixed-variant": "#224e45",
                        "primary-fixed-dim": "#a2d0c4",
                        "on-surface-variant": "#404846",
                        "surface-container-highest": "#e1e3e0",
                        "tertiary-fixed": "#dce4e2",
                        "on-error-container": "#93000a",
                        "surface-bright": "#f8faf7",
                        "primary": "#001c17",
                        "on-primary": "#ffffff",
                        "on-primary-fixed": "#00201b",
                        "inverse-on-surface": "#eff1ee",
                        "secondary-fixed": "#9df3dc",
                        "on-tertiary": "#ffffff",
                        "tertiary-fixed-dim": "#c0c8c6",
                        "surface": "#f8faf7",
                        "inverse-primary": "#a2d0c4",
                        "surface-container-high": "#e7e9e6",
                        "on-primary-container": "#709c91",
                        "surface-container-lowest": "#ffffff",
                        "surface-variant": "#e1e3e0",
                        "surface-tint": "#3b665d",
                        "primary-fixed": "#bdece0",
                        "on-secondary-container": "#0c715f",
                        "secondary-container": "#9df3dc",
                        "on-tertiary-container": "#8d9594",
                        "on-tertiary-fixed-variant": "#404847",
                        "tertiary": "#121919",
                        "primary-container": "#00332b",
                        "on-error": "#ffffff",
                        "error-container": "#ffdad6",
                        "surface-container-low": "#f2f4f1",
                        "surface-container": "#eceeeb",
                        "on-tertiary-fixed": "#151d1c",
                        "on-background": "#191c1b",
                        "inverse-surface": "#2e312f",
                        "on-secondary": "#ffffff",
                        "outline-variant": "#c0c8c5"
                    },
                    "borderRadius": {
                        "DEFAULT": "0.25rem",
                        "lg": "0.5rem",
                        "xl": "0.75rem",
                        "full": "9999px"
                    },
                    "spacing": {
                        "sm": "8px",
                        "lg": "24px",
                        "xxl": "48px",
                        "xs": "4px",
                        "unit": "4px",
                        "xl": "32px",
                        "gutter": "16px",
                        "container-margin": "20px",
                        "md": "16px"
                    },
                    "fontFamily": {
                        "body-lg": ["Inter"],
                        "headline-lg-mobile": ["Lexend"],
                        "headline-lg": ["Lexend"],
                        "body-sm": ["Inter"],
                        "body-md": ["Inter"],
                        "headline-md": ["Lexend"],
                        "display-lg": ["Lexend"],
                        "label-md": ["Inter"],
                        "label-sm": ["Inter"]
                    },
                    "fontSize": {
                        "body-lg": ["18px", {"lineHeight": "28px", "fontWeight": "400"}],
                        "headline-lg-mobile": ["24px", {"lineHeight": "32px", "fontWeight": "600"}],
                        "headline-lg": ["32px", {"lineHeight": "40px", "letterSpacing": "-0.01em", "fontWeight": "600"}],
                        "body-sm": ["14px", {"lineHeight": "20px", "fontWeight": "400"}],
                        "body-md": ["16px", {"lineHeight": "24px", "fontWeight": "400"}],
                        "headline-md": ["24px", {"lineHeight": "32px", "fontWeight": "500"}],
                        "display-lg": ["40px", {"lineHeight": "48px", "letterSpacing": "-0.02em", "fontWeight": "600"}],
                        "label-md": ["14px", {"lineHeight": "16px", "letterSpacing": "0.01em", "fontWeight": "600"}],
                        "label-sm": ["12px", {"lineHeight": "16px", "fontWeight": "500"}]
                    },
                    boxShadow: {
                        'soft': '0px 4px 20px rgba(0, 51, 43, 0.04)',
                        'raised': '0px 8px 30px rgba(0, 51, 43, 0.08)',
                    }
                }
            }
        }
    </script>
<style>
        .material-symbols-outlined {
            font-variation-settings: 'FILL' 0, 'wght' 400, 'GRAD' 0, 'opsz' 24;
        }
        .material-symbols-outlined.fill-icon {
            font-variation-settings: 'FILL' 1;
        }
        .glass-nav {
            background: rgba(248, 250, 247, 0.8);
            backdrop-filter: blur(12px);
            -webkit-backdrop-filter: blur(12px);
        }
        .hero-gradient {
            background: linear-gradient(135deg, #F8FAF7 0%, #E7E9E6 50%, #DCE4E2 100%);
        }
    </style>
</head>
<body class="bg-background text-on-background font-body-md antialiased overflow-x-hidden">
<!-- TopNavBar -->
<nav class="fixed top-0 w-full z-50 glass-nav border-b border-surface-variant/30 shadow-sm transition-all duration-300" id="main-nav">
<div class="flex justify-between items-center w-full px-gutter max-w-7xl mx-auto h-16">
<!-- Brand -->
<div class="flex items-center gap-sm cursor-pointer" onclick="window.scrollTo(0,0)">
<!-- <img alt="KhataFlow Logo" class="h-14 w-14 object-contain drop-shadow-sm" src="{{ asset('logo.png') }}"/> -->
<span class="font-headline-md text-headline-md font-bold text-secondary">KhataFlow</span>
</div>
<!-- Links (Desktop) -->
<div class="hidden md:flex items-center gap-lg">
<a class="font-label-md text-label-md text-on-surface-variant hover:text-secondary transition-colors" href="#features">Features</a>
<a class="font-label-md text-label-md text-on-surface-variant hover:text-secondary transition-colors" href="#benefits">Benefits</a>
<a class="font-label-md text-label-md text-on-surface-variant hover:text-secondary transition-colors" href="#download">Download</a>
</div>
<!-- Primary Action -->
<a class="bg-primary-container text-on-primary font-label-md text-label-md px-lg py-sm rounded-lg hover:opacity-90 active:scale-95 transition-all shadow-soft flex items-center gap-xs" href="#download">
                Get Started
                <span class="material-symbols-outlined text-[18px]">download</span>
</a>
</div>
</nav>
<!-- Main Content Canvas -->
<main class="pt-16">
<!-- Hero Section -->
<section class="relative min-h-[85vh] md:min-h-[95vh] flex items-center overflow-hidden bg-gradient-to-br from-[#f8faf7] via-[#eef3f0] to-[#e8eeeb]">
<!-- Animated gradient orbs -->
<div class="absolute -top-[20%] -right-[10%] w-[70vw] h-[70vw] md:w-[50vw] md:h-[50vw] bg-gradient-to-br from-secondary/8 via-primary-container/10 to-transparent rounded-full blur-[120px] animate-pulse-slow"></div>
<div class="absolute -bottom-[15%] -left-[10%] w-[60vw] h-[60vw] md:w-[45vw] md:h-[45vw] bg-gradient-to-tr from-secondary-container/10 via-surface/5 to-transparent rounded-full blur-[100px]"></div>
<div class="absolute top-1/3 left-1/4 w-48 h-48 md:w-72 md:h-72 bg-secondary/5 rounded-full blur-[80px]"></div>
<div class="absolute bottom-1/3 right-1/6 w-36 h-36 md:w-56 md:h-56 bg-primary/5 rounded-full blur-[60px]"></div>
<!-- Subtle grid overlay -->
<div class="absolute inset-0 opacity-[0.02]" style="background-image: radial-gradient(circle at 1px 1px, #00332b 1px, transparent 0); background-size: 32px 32px;"></div>
<!-- Top-right accent line -->
<div class="absolute top-0 right-0 w-64 h-64 md:w-96 md:h-96 opacity-[0.04]">
<svg viewBox="0 0 400 400" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M400 0H0V400" stroke="#006b59" stroke-width="2"/><path d="M400 50H50V400" stroke="#006b59" stroke-width="1.5" opacity="0.6"/><path d="M400 100H100V400" stroke="#006b59" stroke-width="1" opacity="0.3"/></svg>
</div>
<div class="max-w-7xl mx-auto w-full px-5 sm:px-6 md:px-gutter py-12 md:py-xxl relative z-10">
<div class="grid md:grid-cols-2 gap-8 md:gap-xl lg:gap-xxl items-center">
<!-- Left Content -->
<div class="flex flex-col gap-5 md:gap-lg order-2 md:order-1">
<!-- Premium Badge -->
<div class="inline-flex items-center gap-2 bg-surface/70 backdrop-blur-xl border border-outline-variant/20 pl-1.5 pr-4 py-1 rounded-full w-fit shadow-soft group hover:shadow-raised transition-all duration-500">
<div class="flex items-center gap-1.5 bg-secondary/10 px-2.5 py-1 rounded-full">
<span class="w-1.5 h-1.5 rounded-full bg-secondary animate-pulse"></span>
<span class="font-label-sm text-label-sm text-secondary font-medium text-[11px]">NEW</span>
</div>
<span class="font-label-sm text-label-sm text-on-surface-variant tracking-wide">Built for Indian Retailers</span>
</div>
<!-- Headline -->
<h1 class="font-display-lg text-[2.25rem] sm:text-[3rem] md:text-[3.5rem] lg:text-[4rem] leading-[1.1] sm:leading-[1.12] md:leading-[1.15] text-primary tracking-tight">
                        Smart Dukaan,<br/>
<span class="bg-gradient-to-r from-secondary via-emerald-500 to-secondary bg-clip-text text-transparent relative">
                            Smart Hisaab
<span class="absolute -bottom-1 left-0 w-full h-1 bg-gradient-to-r from-secondary/20 via-emerald-500/20 to-secondary/20 rounded-full blur-sm"></span>
</span>
</h1>
<p class="font-body-lg text-[0.95rem] sm:text-[1.05rem] md:text-body-lg text-on-surface-variant max-w-md lg:max-w-lg leading-relaxed md:leading-[1.75]">
                        Fast, Offline &amp; Smart Billing App for Kirana Stores. Take control of your daily operations with premium simplicity.
                    </p>
<!-- Social Proof -->
<div class="flex flex-wrap items-center gap-3 md:gap-4 mt-1">
<div class="flex -space-x-2.5">
<div class="w-9 h-9 md:w-10 md:h-10 rounded-full bg-gradient-to-br from-primary-container to-primary border-[2.5px] border-surface flex items-center justify-center shadow-soft transform hover:scale-110 transition-transform"><span class="material-symbols-outlined text-[14px] md:text-[16px] text-on-primary">store</span></div>
<div class="w-9 h-9 md:w-10 md:h-10 rounded-full bg-gradient-to-br from-secondary-container to-secondary border-[2.5px] border-surface flex items-center justify-center shadow-soft transform hover:scale-110 transition-transform"><span class="material-symbols-outlined text-[14px] md:text-[16px] text-on-secondary-container">receipt_long</span></div>
<div class="w-9 h-9 md:w-10 md:h-10 rounded-full bg-gradient-to-br from-surface-container-high to-surface-container-highest border-[2.5px] border-surface flex items-center justify-center shadow-soft transform hover:scale-110 transition-transform"><span class="material-symbols-outlined text-[14px] md:text-[16px] text-on-surface">trending_up</span></div>
</div>
<div class="flex flex-col">
<div class="flex items-center gap-1">
<div class="flex">
<span class="material-symbols-outlined text-secondary text-[16px] md:text-[18px]">star</span>
<span class="material-symbols-outlined text-secondary text-[16px] md:text-[18px]">star</span>
<span class="material-symbols-outlined text-secondary text-[16px] md:text-[18px]">star</span>
<span class="material-symbols-outlined text-secondary text-[16px] md:text-[18px]">star</span>
<span class="material-symbols-outlined text-secondary text-[16px] md:text-[18px]">star</span>
</div>
<span class="font-label-sm text-label-sm text-on-surface-variant">Trusted by <span class="text-primary font-semibold">500+</span> retailers</span>
</div>
</div>
</div>
<!-- CTA Buttons -->
<div class="flex flex-col sm:flex-row gap-3 md:gap-4 mt-2 md:mt-3">
<a class="group relative bg-gradient-to-br from-primary-container to-primary text-on-primary h-12 md:h-14 px-6 md:px-8 rounded-xl font-label-md text-label-md flex items-center justify-center gap-2.5 shadow-soft hover:shadow-raised hover:-translate-y-0.5 transition-all duration-300 overflow-hidden" href="#download">
<div class="absolute inset-0 bg-white/10 translate-y-full group-hover:translate-y-0 transition-transform duration-300"></div>
<span class="material-symbols-outlined fill-icon group-hover:scale-110 transition-transform duration-300 relative z-10">android</span>
<span class="relative z-10">Download APK</span>
</a>
<a class="group relative bg-surface/60 backdrop-blur-md text-primary h-12 md:h-14 px-6 md:px-8 rounded-xl font-label-md text-label-md flex items-center justify-center gap-2 border border-outline-variant/30 hover:bg-surface hover:border-outline-variant/60 hover:shadow-soft transition-all duration-300" href="#features">
<span class="relative z-10">Explore Features</span>
<span class="material-symbols-outlined group-hover:translate-x-1.5 transition-transform duration-300 relative z-10">arrow_forward</span>
</a>
</div>
</div>
<!-- Right Content - Phone -->
<div class="relative flex justify-center md:justify-end order-1 md:order-2 mt-4 md:mt-0">
<div class="relative w-[260px] sm:w-[280px] md:w-[300px] lg:w-[320px]">
<!-- Glow behind phone -->
<div class="absolute -inset-4 md:-inset-6 bg-gradient-to-b from-secondary/10 via-primary-container/5 to-transparent rounded-[3rem] blur-[40px]"></div>
<!-- Phone Frame -->
<div class="relative rounded-[2.2rem] md:rounded-[2.5rem] bg-gradient-to-b from-[#2d2d2d] to-[#1a1a1a] p-[8px] md:p-[10px] shadow-[0_20px_60px_-12px_rgba(0,51,43,0.25),0_8px_24px_-6px_rgba(0,51,43,0.15)]">
<!-- Notch -->
<div class="absolute top-0 left-1/2 -translate-x-1/2 w-[100px] md:w-[120px] h-[20px] md:h-[24px] bg-[#1a1a1a] rounded-b-[1.2rem] md:rounded-b-2xl z-30 flex items-center justify-center gap-1.5">
<div class="w-1.5 h-1.5 md:w-2 md:h-2 rounded-full bg-[#3a3a3a]"></div>
<div class="w-3 h-1.5 md:w-4 md:h-2 rounded-full bg-gradient-to-r from-[#3a3a3a] to-[#4a4a4a]"></div>
</div>
<!-- Side buttons -->
<div class="absolute top-[16px] md:top-[18px] -right-[4px] md:-right-[5px] w-[2px] md:w-[3px] h-[24px] md:h-[28px] bg-[#2d2d2d] rounded-r z-30"></div>
<div class="absolute top-[46px] md:top-[52px] -right-[4px] md:-right-[5px] w-[2px] md:w-[3px] h-[36px] md:h-[42px] bg-[#2d2d2d] rounded-r z-30"></div>
<div class="absolute top-[16px] md:top-[18px] -left-[4px] md:-left-[5px] w-[2px] md:w-[3px] h-[32px] md:h-[38px] bg-[#2d2d2d] rounded-l z-30"></div>
<!-- Screen -->
<div class="relative rounded-[1.6rem] md:rounded-[2rem] overflow-hidden bg-[#1a1a1a] aspect-[9/19]">
<div class="screenshot-slider flex transition-transform duration-700 ease-in-out h-full" id="screenshotSlider">
<div class="min-w-full h-full"><img alt="KhataFlow Screenshot 1" class="w-full h-full object-cover" src="{{ asset('slider-images/img1.jpeg') }}"/></div>
<div class="min-w-full h-full"><img alt="KhataFlow Screenshot 2" class="w-full h-full object-cover" src="{{ asset('slider-images/img2.jpeg') }}"/></div>
<div class="min-w-full h-full"><img alt="KhataFlow Screenshot 3" class="w-full h-full object-cover" src="{{ asset('slider-images/img3.jpeg') }}"/></div>
<div class="min-w-full h-full"><img alt="KhataFlow Screenshot 4" class="w-full h-full object-cover" src="{{ asset('slider-images/img4.jpeg') }}"/></div>
<div class="min-w-full h-full"><img alt="KhataFlow Screenshot 5" class="w-full h-full object-cover" src="{{ asset('slider-images/img5.jpeg') }}"/></div>
</div>
</div>
</div>
<!-- Slider Navigation Dots -->
<div class="flex items-center justify-center gap-2 md:gap-2.5 mt-3 md:mt-4 z-20">
<div class="w-2 h-2 md:w-2.5 md:h-2.5 rounded-full bg-secondary shadow-sm slider-dot active cursor-pointer transition-all duration-300 hover:scale-125" data-index="0"></div>
<div class="w-2 h-2 md:w-2.5 md:h-2.5 rounded-full bg-outline-variant/40 shadow-sm slider-dot cursor-pointer transition-all duration-300 hover:scale-125" data-index="1"></div>
<div class="w-2 h-2 md:w-2.5 md:h-2.5 rounded-full bg-outline-variant/40 shadow-sm slider-dot cursor-pointer transition-all duration-300 hover:scale-125" data-index="2"></div>
<div class="w-2 h-2 md:w-2.5 md:h-2.5 rounded-full bg-outline-variant/40 shadow-sm slider-dot cursor-pointer transition-all duration-300 hover:scale-125" data-index="3"></div>
<div class="w-2 h-2 md:w-2.5 md:h-2.5 rounded-full bg-outline-variant/40 shadow-sm slider-dot cursor-pointer transition-all duration-300 hover:scale-125" data-index="4"></div>
</div>
<!-- Floating Badge -->
<div class="absolute -bottom-2 md:-bottom-3 -right-2 md:-right-4 bg-surface/90 backdrop-blur-xl p-2.5 md:p-3 rounded-xl shadow-raised flex items-center gap-2 md:gap-2.5 border border-outline-variant/20 animate-float">
<div class="w-8 h-8 md:w-10 md:h-10 rounded-full bg-gradient-to-br from-secondary-container to-secondary flex items-center justify-center">
<span class="material-symbols-outlined text-on-secondary-container fill-icon text-[16px] md:text-[20px]">bolt</span>
</div>
<div>
<p class="font-label-md text-label-md text-on-surface leading-tight text-[12px] md:text-[14px]">Lightning Fast</p>
<p class="font-body-sm text-body-sm text-on-surface-variant leading-tight text-[11px] md:text-[14px]">Zero lag billing</p>
</div>
</div>
</div>
</div>
</div>
</div>
</section>
<section class="py-xxl px-gutter bg-surface" id="features">
<div class="max-w-7xl mx-auto">
<div class="text-center mb-xl">
<h2 class="font-headline-lg text-headline-lg text-primary mb-sm">Premium Features for Retail</h2>
<p class="font-body-lg text-body-lg text-on-surface-variant max-w-2xl mx-auto">Designed for speed and reliability, KhataFlow brings enterprise-grade tools to your shop counter.</p>
</div>
<div class="grid md:grid-cols-2 lg:grid-cols-3 gap-lg">
<div class="bg-surface-container-lowest border border-outline-variant/20 rounded-xl p-lg shadow-soft hover:shadow-raised transition-shadow group">
<div class="w-12 h-12 rounded-lg bg-surface-container-high flex items-center justify-center mb-md group-hover:bg-primary-container transition-colors">
<span class="material-symbols-outlined text-on-surface-variant group-hover:text-on-primary text-[24px]">flash_on</span>
</div>
<h3 class="font-headline-md text-[20px] text-on-surface mb-xs">Super Fast Billing</h3>
<p class="font-body-sm text-body-sm text-on-surface-variant">Generate receipts in seconds with optimized workflows designed for high-frequency counters.</p>
</div>
<div class="bg-surface-container-lowest border border-outline-variant/20 rounded-xl p-lg shadow-soft hover:shadow-raised transition-shadow group">
<div class="w-12 h-12 rounded-lg bg-surface-container-high flex items-center justify-center mb-md group-hover:bg-primary-container transition-colors">
<span class="material-symbols-outlined text-on-surface-variant group-hover:text-on-primary text-[24px]">wifi_off</span>
</div>
<h3 class="font-headline-md text-[20px] text-on-surface mb-xs">Offline Mode</h3>
<p class="font-body-sm text-body-sm text-on-surface-variant">Internet down? No problem. Continue billing flawlessly and sync automatically when back online.</p>
</div>
<div class="bg-surface-container-lowest border border-outline-variant/20 rounded-xl p-lg shadow-soft hover:shadow-raised transition-shadow group">
<div class="w-12 h-12 rounded-lg bg-surface-container-high flex items-center justify-center mb-md group-hover:bg-primary-container transition-colors">
<span class="material-symbols-outlined text-on-surface-variant group-hover:text-on-primary text-[24px]">menu_book</span>
</div>
<h3 class="font-headline-md text-[20px] text-on-surface mb-xs">Udhaar Management</h3>
<p class="font-body-sm text-body-sm text-on-surface-variant">Track customer credit easily. Send gentle automated reminders and keep your cashflow healthy.</p>
</div>
<div class="bg-surface-container-lowest border border-outline-variant/20 rounded-xl p-lg shadow-soft hover:shadow-raised transition-shadow group">
<div class="w-12 h-12 rounded-lg bg-surface-container-high flex items-center justify-center mb-md group-hover:bg-primary-container transition-colors">
<span class="material-symbols-outlined text-on-surface-variant group-hover:text-on-primary text-[24px]">inventory_2</span>
</div>
<h3 class="font-headline-md text-[20px] text-on-surface mb-xs">Stock Management</h3>
<p class="font-body-sm text-body-sm text-on-surface-variant">Real-time inventory tracking. Get low-stock alerts before you run out of fast-moving items.</p>
</div>
<div class="bg-surface-container-lowest border border-outline-variant/20 rounded-xl p-lg shadow-soft hover:shadow-raised transition-shadow group">
<div class="w-12 h-12 rounded-lg bg-surface-container-high flex items-center justify-center mb-md group-hover:bg-primary-container transition-colors">
<span class="material-symbols-outlined text-on-surface-variant group-hover:text-on-primary text-[24px]">monitoring</span>
</div>
<h3 class="font-headline-md text-[20px] text-on-surface mb-xs">Daily Reports</h3>
<p class="font-body-sm text-body-sm text-on-surface-variant">Clear, actionable insights into your daily sales, profits, and top-selling products.</p>
</div>
<div class="bg-surface-container-lowest border border-outline-variant/20 rounded-xl p-lg shadow-soft hover:shadow-raised transition-shadow group">
<div class="w-12 h-12 rounded-lg bg-surface-container-high flex items-center justify-center mb-md group-hover:bg-primary-container transition-colors">
<span class="material-symbols-outlined text-on-surface-variant group-hover:text-on-primary text-[24px]">share</span>
</div>
<h3 class="font-headline-md text-[20px] text-on-surface mb-xs">WhatsApp Bills</h3>
<p class="font-body-sm text-body-sm text-on-surface-variant">Go paperless. Share professional digital receipts directly to customers via WhatsApp instantly.</p>
</div>
</div>
</div>
</section>
<section class="py-xxl px-gutter bg-surface-container-low" id="benefits">
<div class="max-w-7xl mx-auto grid md:grid-cols-12 gap-xxl items-center">
<div class="md:col-span-5 flex flex-col gap-md">
<span class="font-label-md text-label-md text-secondary uppercase tracking-wider">The KhataFlow Advantage</span>
<h2 class="font-headline-lg text-headline-lg text-primary">Built on Trust &amp; Reliability</h2>
<p class="font-body-lg text-body-lg text-on-surface-variant">
                        We understand the chaos of a busy retail counter. That's why we designed an interface that feels calm, stable, and effortless to navigate—even during rush hours.
                    </p>
<ul class="flex flex-col gap-md mt-sm">
<li class="flex items-start gap-sm">
<span class="material-symbols-outlined text-secondary mt-xs">check_circle</span>
<div>
<h4 class="font-label-md text-label-md text-on-surface">Radical Simplicity</h4>
<p class="font-body-sm text-body-sm text-on-surface-variant">No steep learning curves. Start billing from day one.</p>
</div>
</li>
<li class="flex items-start gap-sm">
<span class="material-symbols-outlined text-secondary mt-xs">shield</span>
<div>
<h4 class="font-label-md text-label-md text-on-surface">100% Data Security</h4>
<p class="font-body-sm text-body-sm text-on-surface-variant">Your business data stays locally secured and securely backed up.</p>
</div>
</li>
</ul>
</div>
<div class="md:col-span-7 relative">
<div class="bg-surface/80 backdrop-blur-md border border-outline-variant/30 rounded-xl p-xl shadow-raised relative z-10 grid sm:grid-cols-2 gap-lg">
<div class="bg-surface-container-highest/50 rounded-lg p-md text-center">
<span class="font-display-lg text-display-lg text-primary block">99.9%</span>
<span class="font-label-sm text-label-sm text-on-surface-variant">Uptime Reliability</span>
</div>
<div class="bg-surface-container-highest/50 rounded-lg p-md text-center">
<span class="font-display-lg text-display-lg text-primary block">Zero</span>
<span class="font-label-sm text-label-sm text-on-surface-variant">Hidden Fees</span>
</div>
</div>
<div class="absolute inset-0 bg-secondary/10 blur-[60px] rounded-full transform scale-110 -z-0"></div>
</div>
</div>
</section>
<section class="py-xxl px-gutter bg-primary text-on-primary relative overflow-hidden" id="download">
<div class="absolute inset-0 opacity-10" style="background-image: radial-gradient(circle at 2px 2px, white 1px, transparent 0); background-size: 32px 32px;"></div>
<div class="max-w-4xl mx-auto text-center relative z-10 flex flex-col items-center">
<!-- <div class="w-20 h-20 bg-surface rounded-2xl mb-lg flex items-center justify-center shadow-raised">
<img alt="KhataFlow Logo Icon" class="h-12 w-12 object-contain" src="data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAABAAAAAQACAYAAAB/HSuDAAAQAElEQVR4AezdCZwkZ1k4/uft2Q1JOBJE5RA0kJDs9ubYnUH9oXJfCiIiKiCHwA/xAgRFRRGQSzxREC/4I14gKiKnnCIi/jxnNgnZ2Q1EBMVwiNxHyO52/d+azW72mKOPqu6q6m9/uqa7q+p93uf9vjM9XU9fvXAiQIAAAQIECBAgQIAAAQIEui4QCgCdn2IDJECAAAECBAgQIECAAAECoQDgl4AAAQIECBAgQIAAAQIECHReIA/QKwAygjMBAgQIECBAgAABAgQIEOiyQDk2BYBSwUKAAAECBAgQIECAAAECBLorsDYyBYA1Bj8IECBAgAABAgQIECBAgEBXBY6MSwHgiIOfBAgQIECAAAECBAgQIECgmwLXjUoB4DoIFwQIECBAgAABAgQIECBAoIsCR8ekAHBUwiUBAgQIECBAgAABAgQIEOiewLERKQAco3CFAAECBAgQIECAAAECBAh0TeD68SgAXG/hGgECBAgQIECAAAECBAgQ6JbAcaNRADgOw1UCBAgQIECAAAECBAgQINAlgePHogBwvIbrBAgQIECAAAECBAgQIECgOwInjEQB4AQONwgQIECAAAECBAgQIECAQFcEThyHAsCJHm4RIECAAAECBAgQIECAAIFuCJw0CgWAk0DcJECAAAECBAgQIECAAAECXRA4eQwKACeLuE2AAAECBAgQIECAAAECBNovcMoIFABOIbGCAAECBAgQIECAAAECBAi0XeDU/BUATjWxhgABAgQIECBAgAABAgQItFtgnewVANZBsYoAAQIECBAgQIAAAQIECLRZYL3cFQDWU7GOAAECBAgQIECAAAECBAi0V2DdzBUA1mWxkgABAgQIECBAgAABAgQItFVg/bwVANZ3sZYAAQIECBAgQIAAAQIECLRTYIOsFQA2gLGaAAECBAgQIECAAAECBAi0UWCjnBUANpKxngABAgQIECBAgAABAgQItE9gw4wVADaksYEAAQIECBAgQIAAAQIECLRNYON8FQA2trGFAAECBAgQIECAAAECBAi0S2CTbBUANsGxiQABAgQIECBAgAABAgQItElgs1wVADbTsY0AAQIECBAgQIAAAQIECLRHYNNMFQA25bGRAAECBAgQIECAAAECBAi0RWDzPBUANvexlQABAgQIECBAgAABAgQItENgiywVALYAspkAAQIECBAgQIAAAQIECLRBYKscFQC2ErKdAAECBAgQIECAAAECBAg0X2DLDBUAtiSyAwECBAgQIECAAAECBAgQaLrA1vkpAGxtZA8CBAgQIECAAAECBAgQINBsgSGyUwAYAskuBAgQIECAAAECBAgQIECgyQLD5KYAMIySfQgQIECAAAECBAgQIECAQHMFhspMAWAoJjsRIECAAAECBAgQIECAAIGmCgyXlwLAcE72IkCAAAECBAgQIECAAAECzRQYMisFgCGh7EaAAAECBAgQIECAAAECBJooMGxOCgDDStmPAAECBAgQIECAAAECBAg0T2DojBQAhqayIwECBAgQIECAAAECBAgQaJrA8PkoAAxvZU8CBAgQIECAAAECBAgQINAsgRGyUQAYAcuuBAgQIECAAAECBAgQIECgSQKj5KIAMIqWfQkQIECAAAECBAgQIECAQHMERspEAWAkLjsTIECAAAECBAgQIECAAIGmCIyWhwLAaF72JkCAAAECBAgQIECAAAECzRAYMQsFgBHB7E6AAAECBAgQIECAAAECBJogMGoOCgCjitmfAAECBAgQIECAAAECBAjMXmDkDBQARibTgAABAgQIECBAgAABAgQIzFpg9P4VAEY304IAAQIECBAgQIAAAQIECMxWYIzeFQDGQNOEAAECBAgQI..."/>
</div> -->
<h2 class="font-display-lg text-display-lg md:text-[48px] text-on-primary mb-md">Ready to Upgrade Your Shop?</h2>
<p class="font-body-lg text-body-lg text-on-primary-fixed-variant mb-xl max-w-2xl">
                     Join thousands of smart retailers using KhataFlow. Download the latest APK securely and start your free trial today.
                 </p>
<div class="bg-primary-container border border-outline-variant/20 rounded-xl p-lg w-full max-w-md flex flex-col items-center gap-md shadow-raised">
<a class="w-full bg-secondary text-on-secondary h-14 rounded-lg font-label-md text-label-md text-[16px] flex items-center justify-center gap-sm hover:bg-secondary/90 transition-colors shadow-soft group" href="https://github.com/svan2511/khataflow/releases/download/v1.0.0/app.apk" target="_blank" rel="noopener noreferrer">
<span class="material-symbols-outlined group-hover:animate-bounce">download</span>
                         Download Latest APK
                     </a>
<div class="flex flex-wrap justify-center gap-x-lg gap-y-xs font-body-sm text-body-sm text-inverse-on-surface/80">
<span class="flex items-center gap-xs">
<span class="material-symbols-outlined text-[16px]">info</span>
                             v1.0.0
                         </span>
<span class="flex items-center gap-xs">
<!-- <span class="material-symbols-outlined text-[16px]">folder</span>
                             File size: 15MB
                         </span> -->
<span class="flex items-center gap-xs w-full justify-center mt-xs">
<span class="material-symbols-outlined text-[16px]">android</span>
                             Compatible with Android 8.0+
                         </span>
</div>
</div>
<p class="font-body-sm text-body-sm text-on-primary-fixed-variant mt-lg flex items-center gap-xs">
<span class="material-symbols-outlined text-[16px]">verified_user</span>
                     Verified &amp; Secure Download
                 </p>
</div>
</section>
</main>
<footer class="bg-surface-container-low dark:bg-tertiary-container border-t border-outline-variant/20 w-full px-gutter py-xl">
<div class="flex flex-col md:flex-row justify-between items-center max-w-7xl mx-auto gap-md">
<div class="flex items-center gap-sm">
<!-- <img alt="KhataFlow Small Logo" class="h-6 w-6 object-contain rounded" src="data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAABAAAAAQACAYAAAB/HSuDAAAQAElEQVR4AezdCZwkZ1k4/uft2Q1JOBJE5RA0kJDs9ubYnUH9oXJfCiIiKiCHwA/xAgRFRRGQSzxREC/4I14gKiKnnCIi/jxnNgnZ2Q1EBMVwiNxHyO52/d+azW72mKOPqu6q6m9/uqa7q+p93uf9vjM9XU9fvXAiQIAAAQIECBAgQIAAAQIEui4QCgCdn2IDJECAAAECBAgQIECAAAECoQDgl4AAAQIECBAgQIAAAQIECHReIA/QKwAygjMBAgQIECBAgAABAgQIEOiyQDk2BYBSwUKAAAECBAgQIECAAAECBLorsDYyBYA1Bj8IECBAgAABAgQIECBAgEBXBY6MSwHgiIOfBAgQIECAAAECBAgQIECgmwLXjUoB4DoIFwQIECBAgAABAgQIECBAoIsCR8ekAHBUwiUBAgQIECBAgAABAgQIEOiewLERKQAco3CFAAECBAgQIECAAAECBAh0TeD68SgAXG/hGgECBAgQIECAAAECBAgQ6JbAcaNRADgOw1UCBAgQIECAAAECBAgQINAlgePHogBwvIbrBAgQIECAAAECBAgQIECgOwInjEQB4AQONwgQIECAAAECBAgQIECAQFcEThyHAsCJHm4RIECAAAECBAgQIECAAIFuCJw0CgWAk0DcJECAAAECBAgQIECAAAECXRA4eQwKACeLuE2AAAECBAgQIECAAAECBNovcMoIFABOIbGCAAECBAgQIECAAAECBAi0XeDU/BUATjWxhgABAgQIECBAgAABAgQItFtgnewVANZBsYoAAQIECBAgQIAAAQIECLRZYL3cFQDWU7GOAAECBAgQIECAAAECBAi0V2DdzBUA1mWxkgABAgQIECBAgAABAgQItFVg/bwVANZ3sZYAAQIECBAgQIAAAQIECLRTYIOsFQA2gLGaAAECBAgQIECAAAECBAi0UWCjnBUANpKxngABAgQIECBAgAABAgQItE9gw4wVADaksYEAAQIECBAgQIAAAQIECLRNYON8FQA2trGFAAECBAgQIECAAAECBAi0S2CTbBUANsGxiQABAgQIECBAgAABAgQItElgs1wVADbTsY0AAQIECBAgQIAAAQIECLRHYNNMFQA25bGRAAECBAgQIECAAAECBAi0RWDzPBUANvexlQABAgQIECBAgAABAgQItENgiywVALYAspkAAQIECBAgQIAAAQIECLRBYKscFQC2ErKdAAECBAgQIECAAAECBAg0X2DLDBUAtiSyAwECBAgQIECAAAECBAgQaLrA1vkpAGxtZA8CBAgQIECAAAECBAgQINBsgSGyUwAYAskuBAgQIECAAAECBAgQIECgyQLD5KYAMIySfQgQIECAAAECBAgQIECAQHMFhspMAWAoJjsRIECAAAECBAgQIECAAIGmCgyXlwLAcE72IkCAAAECBAgQIECAAAECzRQYMisFgCGh7EaAAAECBAgQIECAAAECBJooMGxOCgDDStmPAAECBAgQIECAAAECBAg0T2DojBQAhqayIwECBAgQIECAAAECBAgQaJrA8PkoAAxvZU8CBAgQIECAAAECBAgQINAsgRGyUQAYAcuuBAgQIECAAAECBAgQIECgSQKj5KIAMIqWfQkQIECAAAECBAgQIECAQHMERspEAWAkLjsTIECAAAECBAgQIECAAIGmCIyWhwLAaF72JkCAAAECBAgQIECAAAECzRAYMQsFgBHB7E6AAAECBAgQIECAAAECBJogMGoOCgCjitmfAAECBAgQIECAAAECBAjMXmDkDBQARibTgAABAgQIECBAgAABAgQIzFpg9P4VAEY304IAAQIECBAgQIAAAQIECMxWYIzeFQDGQNOEAAECBAgQI..."/> -->
<span class="font-headline-sm text-headline-sm font-bold text-secondary">KhataFlow</span>
</div>
<p class="font-body-sm text-body-sm text-on-surface-variant dark:text-on-tertiary-fixed-variant text-center">
                &copy; 2024 KhataFlow. Made with ❤️ for Indian Dukandaars
            </p>
<div class="flex gap-lg">
<a class="font-label-sm text-label-sm text-on-surface-variant hover:text-primary dark:hover:text-primary-fixed transition-all hover:underline" href="#">Privacy Policy</a>
<a class="font-label-sm text-label-sm text-on-surface-variant hover:text-primary dark:hover:text-primary-fixed transition-all hover:underline" href="#">Terms of Service</a>
<a class="font-label-sm text-label-sm text-on-surface-variant hover:text-primary dark:hover:text-primary-fixed transition-all hover:underline" href="#">Contact Us</a>
</div>
</div>
</footer>
<script>
        const nav = document.getElementById('main-nav');
        window.addEventListener('scroll', () => {
            if (window.scrollY > 10) {
                nav.classList.add('shadow-soft');
            } else {
                nav.classList.remove('shadow-soft');
            }
        });
        const style = document.createElement('style');
        style.textContent = `
            @keyframes float {
                0% { transform: translateY(0px); }
                50% { transform: translateY(-8px); }
                100% { transform: translateY(0px); }
            }
            .animate-float {
                animation: float 5s ease-in-out infinite;
            }
            @keyframes fadeInUp {
                from { opacity: 0; transform: translateY(24px); }
                to { opacity: 1; transform: translateY(0); }
            }
            .animate-fade-in-up {
                animation: fadeInUp 0.9s ease-out forwards;
            }
            @keyframes pulse-slow {
                0%, 100% { opacity: 0.6; transform: scale(1); }
                50% { opacity: 1; transform: scale(1.05); }
            }
            .animate-pulse-slow {
                animation: pulse-slow 8s ease-in-out infinite;
            }
        `;
        document.head.appendChild(style);

        const slider = document.getElementById('screenshotSlider');
        const dots = document.querySelectorAll('.slider-dot');
        if (slider && dots.length) {
            let current = 0;
            const total = dots.length;
            let interval;

            function goTo(index) {
                current = index;
                slider.style.transform = 'translateX(-' + (current * 100) + '%)';
                dots.forEach((d, i) => {
                    d.classList.toggle('active', i === current);
                    d.classList.toggle('bg-outline-variant/40', i !== current);
                    d.classList.toggle('bg-secondary', i === current);
                });
            }

            function next() {
                goTo((current + 1) % total);
            }

            dots.forEach((dot, i) => {
                dot.addEventListener('click', () => {
                    clearInterval(interval);
                    goTo(i);
                    interval = setInterval(next, 4000);
                });
            });

            goTo(0);
            interval = setInterval(next, 4000);
        }
    </script>
</body></html>
