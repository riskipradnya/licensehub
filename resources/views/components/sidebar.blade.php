@props([])

@php
$currentRoute = request()->path();

$menuGroups = [
    [
        'label' => 'Main',
        'items' => [
            ['name' => 'Dashboard', 'icon' => 'chart-bar', 'route' => 'dashboard', 'roles' => ['*']],
        ]
    ],
    [
        'label' => 'IT Department',
        'items' => [
            ['name' => 'License Management', 'icon' => 'document-text', 'route' => 'licenses', 'roles' => ['super_admin', 'it_staff', 'finance_manager'],
                'children' => [
                    ['name' => 'License List', 'route' => 'licenses'],
                    ['name' => 'Add License', 'route' => 'licenses/create'],
                    ['name' => 'Categories', 'route' => 'licenses/categories'],
                ]
            ],
            ['name' => 'Vendor Management', 'icon' => 'building-office', 'route' => 'vendors', 'roles' => ['super_admin', 'it_staff', 'finance_manager'],
                'children' => [
                    ['name' => 'Vendor List', 'route' => 'vendors'],
                    ['name' => 'Add Vendor', 'route' => 'vendors/create'],
                ]
            ],
            ['name' => 'Documents', 'icon' => 'folder', 'route' => 'documents', 'roles' => ['*']],
        ]
    ],
    [
        'label' => 'Monitoring',
        'items' => [
            ['name' => 'Notifications & Alerts', 'icon' => 'bell-alert', 'route' => 'notifications', 'roles' => ['*']],
            ['name' => 'Cost Projection', 'icon' => 'currency-dollar', 'route' => 'cost-projection', 'roles' => ['*']],
            ['name' => 'Audit Log', 'icon' => 'clipboard-document-list', 'route' => 'audit-log', 'roles' => ['super_admin', 'finance_manager']],
        ]
    ],
    [
        'label' => 'Finance',
        'items' => [
            ['name' => 'Payments', 'icon' => 'credit-card', 'route' => 'payments', 'roles' => ['super_admin', 'finance_manager', 'finance_staff'],
                'children' => [
                    ['name' => 'Process Payment', 'route' => 'payments'],
                    ['name' => 'Payment History', 'route' => 'payments/history'],
                ]
            ],
            ['name' => 'Invoices', 'icon' => 'document-duplicate', 'route' => 'invoices', 'roles' => ['super_admin', 'finance_manager', 'finance_staff']],
            ['name' => 'Financial Reports', 'icon' => 'chart-pie', 'route' => 'reports', 'roles' => ['super_admin', 'finance_manager', 'finance_staff']],
        ]
    ],
    [
        'label' => 'Settings',
        'items' => [
            ['name' => 'User & Role Management', 'icon' => 'users', 'route' => 'users', 'roles' => ['super_admin']],
            ['name' => 'Setup Notifications', 'icon' => 'bell', 'route' => 'notification-settings', 'roles' => ['super_admin']],
            ['name' => 'Profile & Preferences', 'icon' => 'cog-6-tooth', 'route' => 'profile', 'roles' => ['*']],
        ]
    ],
];
@endphp

<aside class="sidebar fixed top-0 left-0 h-full flex flex-col overflow-y-auto overflow-x-hidden"
       :class="{
           'open': $store.sidebar.open,
           'collapsed': $store.sidebar.collapsed,
           'w-[260px]': !$store.sidebar.collapsed,
           'w-[72px]': $store.sidebar.collapsed
       }"
       x-data="{ expandedMenu: null }"
       id="sidebar-nav">

    {{-- LOGO --}}
    <div class="flex items-center gap-3 px-5 py-5 border-b border-white/10 shrink-0">
        <div class="w-9 h-9 rounded-xl flex items-center justify-center shrink-0"
             style="background: linear-gradient(135deg, #6366f1, #4f46e5);">
            <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                      d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" />
            </svg>
        </div>
        <span class="text-white font-bold text-lg tracking-tight" x-show="!$store.sidebar.collapsed" x-transition>
            LicenseHub
        </span>
    </div>

    {{-- NAVIGATION --}}
    <nav class="flex-1 px-3 py-4 space-y-1">
        @foreach($menuGroups as $group)
            {{-- Section Label --}}
            <div class="sidebar-section" x-show="!$store.sidebar.collapsed" x-transition>
                {{ $group['label'] }}
            </div>

            @foreach($group['items'] as $item)
                @php
                    $isActive = str_starts_with($currentRoute, $item['route']);
                    $hasChildren = isset($item['children']) && count($item['children']) > 0;
                @endphp

                @if($hasChildren)
                    {{-- PARENT WITH CHILDREN --}}
                    <div>
                        <button @click="expandedMenu = expandedMenu === '{{ $item['route'] }}' ? null : '{{ $item['route'] }}'"
                                class="sidebar-link w-full justify-between {{ $isActive ? 'active' : '' }}"
                                title="{{ $item['name'] }}">
                            <span class="flex items-center gap-3">
                                <x-sidebar-icon :name="$item['icon']" />
                                <span x-show="!$store.sidebar.collapsed" x-transition>{{ $item['name'] }}</span>
                            </span>
                            <svg x-show="!$store.sidebar.collapsed"
                                 class="w-4 h-4 transition-transform duration-200"
                                 :class="expandedMenu === '{{ $item['route'] }}' ? 'rotate-90' : ''"
                                 fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                            </svg>
                        </button>
                        <div x-show="expandedMenu === '{{ $item['route'] }}' && !$store.sidebar.collapsed"
                             x-transition:enter="transition ease-out duration-200"
                             x-transition:enter-start="opacity-0 -translate-y-1"
                             x-transition:enter-end="opacity-100 translate-y-0"
                             class="ml-6 mt-1 space-y-0.5 border-l border-white/10 pl-3">
                            @foreach($item['children'] as $child)
                                <a href="/{{ $child['route'] }}"
                                   class="sidebar-link text-xs py-1.5 {{ $currentRoute === $child['route'] ? 'active' : '' }}">
                                    {{ $child['name'] }}
                                </a>
                            @endforeach
                        </div>
                    </div>
                @else
                    {{-- SINGLE ITEM --}}
                    <a href="/{{ $item['route'] }}"
                       class="sidebar-link {{ $isActive ? 'active' : '' }}"
                       title="{{ $item['name'] }}">
                        <x-sidebar-icon :name="$item['icon']" />
                        <span x-show="!$store.sidebar.collapsed" x-transition>{{ $item['name'] }}</span>
                    </a>
                @endif
            @endforeach
        @endforeach
    </nav>

    {{-- USER PROFILE (bottom) --}}
    <div class="shrink-0 border-t border-white/10 p-3" x-show="!$store.sidebar.collapsed" x-transition>
        <div class="flex items-center gap-3 px-2 py-2">
            <div class="w-9 h-9 rounded-full flex items-center justify-center text-white text-sm font-semibold shrink-0"
                 style="background: linear-gradient(135deg, #6366f1, #8b5cf6);">
                {{ substr(auth()->user()->name ?? 'A', 0, 1) }}
            </div>
            <div class="min-w-0">
                <p class="text-sm font-medium text-white truncate">{{ auth()->user()->name ?? 'Admin User' }}</p>
                <p class="text-xs truncate" style="color: #64748b;">{{ auth()->user()->role ?? 'Super Admin' }}</p>
            </div>
        </div>
        <form method="POST" action="{{ route('logout') }}">
            @csrf
            <button type="submit" class="sidebar-link mt-1 text-xs w-full" style="color: #ef4444;">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/>
                </svg>
                <span>Logout</span>
            </button>
        </form>
    </div>
</aside>
