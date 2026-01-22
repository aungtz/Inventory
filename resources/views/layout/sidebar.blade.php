<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Responsive Sidebar</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        /* Active Navigation Item Styles - All Same Color */
        .nav-item.active {
            background: linear-gradient(135deg, rgba(59, 130, 246, 0.2) 0%, rgba(6, 182, 212, 0.2) 100%);
            color: white;
            border-left: 3px solid #3b82f6;
            box-shadow: 0 4px 12px -2px rgba(59, 130, 246, 0.3);
        }
        
        .nav-item.active svg {
            stroke: #60a5fa;
        }
        
        /* Smooth hover effect */
        .nav-item {
            transition: all 0.2s ease;
        }
        
        .nav-item:hover:not(.active) {
            background-color: rgba(71, 85, 105, 0.3);
            transform: translateX(2px);
        }
        
        /* Mobile sidebar animation */
        .sidebar-mobile {
            transform: translateX(-100%);
            transition: transform 0.3s ease-in-out;
        }
        
        .sidebar-mobile.open {
            transform: translateX(0);
        }
        
        /* Overlay for mobile */
        .sidebar-overlay {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background-color: rgba(0, 0, 0, 0.5);
            z-index: 30;
        }
        
        .sidebar-overlay.active {
            display: block;
        }
        
        /* Ensure main content adjusts */
        .main-content {
            margin-left: 0;
            transition: margin-left 0.3s ease;
        }
        
        @media (min-width: 1024px) {
            .main-content {
                margin-left: 16rem;
            }
        }
        
        /* Mobile menu button */
        .mobile-menu-btn {
    display: block;
    position: fixed;
    top: 1rem;
    left: 1rem;
    z-index: 50;
    background: white;            /* white background */
    color: #1f2937;               /* ← change this to control line color */
    border: 1px solid rgba(71, 85, 105, 0.5);
    backdrop-filter: blur(10px);
}

        
        @media (min-width: 1024px) {
            .mobile-menu-btn {
                display: none;
            }
        }
    </style>
</head>
<body class="bg-slate-950 text-white">
    <!-- Mobile Menu Button -->
    <button id="mobileMenuBtn" class="mobile-menu-btn p-3 rounded-xl shadow-lg lg:hidden">
        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path>
        </svg>
    </button>
    
    <!-- Mobile Overlay -->
    <div id="sidebarOverlay" class="sidebar-overlay"></div>
    
    <div class="flex min-h-screen">
        <!-- Sidebar for Mobile -->
        <aside id="mobileSidebar" class="sidebar-mobile fixed top-0 left-0 z-40 h-screen w-64 bg-gradient-to-b from-slate-900 via-slate-900 to-slate-800 backdrop-blur border-r border-slate-700/80 shadow-2xl lg:hidden">
            <div class="p-6 border-b border-slate-700/80">
                <div class="flex items-center justify-between">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 rounded-xl bg-gradient-to-br from-blue-500 to-cyan-500 flex items-center justify-center shadow-md">
                            <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path>
                            </svg>
                        </div>
                        <div>
                            <h1 class="text-xl font-bold bg-gradient-to-r from-blue-400 to-cyan-400 bg-clip-text text-transparent">
                                Inventory Pro
                            </h1>
                            <p class="text-xs text-slate-400">Management System</p>
                        </div>
                    </div>
                    <button id="closeMobileMenu" class="p-2 rounded-lg hover:bg-slate-700/50 lg:hidden">
                        <svg class="w-5 h-5 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                    </button>
                </div>
            </div>

            <div class="p-4 space-y-2 h-[calc(100vh-200px)] overflow-y-auto">
                <a href="#" id="nav-dashboard" class="nav-item flex items-center space-x-3 p-3 rounded-lg hover:bg-slate-700/50 transition-colors text-slate-300 hover:text-white">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" />
                    </svg>
                    <span>Dashboard</span>
                </a>

                <a href="/itemList" id="nav-items" class="nav-item flex items-center space-x-3 p-3 rounded-lg hover:bg-slate-700/50 transition-colors text-slate-300 hover:text-white">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4" />
                    </svg>
                    <span>Items</span>
                </a>

                <a href="/items-create" id="nav-create-items" class="nav-item flex items-center space-x-3 p-3 rounded-lg hover:bg-slate-700/50 transition-colors text-slate-300 hover:text-white">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                    </svg>
                    <span>Create Items</span>
                </a>

                <a href="/import-log" id="nav-import" class="nav-item flex items-center space-x-3 p-3 rounded-lg hover:bg-slate-700/50 transition-colors text-slate-300 hover:text-white">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4" />
                    </svg>
                    <span>Import</span>
                </a>
            </div>

            <div class="absolute bottom-0 left-0 right-0 p-4 border-t border-slate-700/80 bg-slate-900/50 backdrop-blur">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-full bg-gradient-to-br from-blue-500 to-cyan-500 flex items-center justify-center shadow-sm">
                        <span class="text-white font-semibold">A</span>
                    </div>
                    <div class="flex-1">
                        <p class="font-medium text-sm text-white">Admin</p>
                        <p class="text-xs text-slate-400">Administrator</p>
                    </div>
                    <button class="p-2 rounded-lg hover:bg-slate-700/50 transition-colors">
                        <svg class="w-5 h-5 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                        </svg>
                    </button>
                </div>
            </div>
        </aside>

        <!-- Sidebar for Desktop -->
        <aside class="sidebar hidden lg:flex lg:relative top-0 left-0 z-40 w-64 flex-col bg-gradient-to-b from-slate-900 via-slate-900 to-slate-800 backdrop-blur border-r border-slate-700/80 shadow-xl lg:shadow-none">
            <div class="p-6 border-b border-slate-700/80">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-xl bg-gradient-to-br from-blue-500 to-cyan-500 flex items-center justify-center shadow-md">
                        <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path>
                        </svg>
                    </div>
                    <div>
                        <h1 class="text-xl font-bold bg-gradient-to-r from-blue-400 to-cyan-400 bg-clip-text text-transparent">
                            Inventory Pro
                        </h1>
                        <p class="text-xs text-slate-400">Management System</p>
                    </div>
                </div>
            </div>

            <div class="p-4 space-y-2 flex-1 overflow-y-auto">
       
                <a href="/itemList" id="nav-items-desktop" class="nav-item flex items-center space-x-3 p-3 rounded-lg hover:bg-slate-700/50 transition-colors text-slate-300 hover:text-white">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4" />
                    </svg>
                    <span>Items</span>
                </a>

                <a href="/items-create" id="nav-create-items-desktop" class="nav-item flex items-center space-x-3 p-3 rounded-lg hover:bg-slate-700/50 transition-colors text-slate-300 hover:text-white">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                    </svg>
                    <span>Create Items</span>
                </a>

                <a href="/import-log" id="nav-import-desktop" class="nav-item flex items-center space-x-3 p-3 rounded-lg hover:bg-slate-700/50 transition-colors text-slate-300 hover:text-white">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4" />
                    </svg>
                    <span>Import</span>
                </a>
            </div>

            <div class="mt-auto p-4 border-t border-slate-700/80 bg-slate-900/50 backdrop-blur">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-full bg-gradient-to-br from-blue-500 to-cyan-500 flex items-center justify-center shadow-sm">
                        <span class="text-white font-semibold">A</span>
                    </div>
                    <div class="flex-1">
                        <p class="font-medium text-sm text-white">Admin</p>
                        <p class="text-xs text-slate-400">Administrator</p>
                    </div>
                    <button class="p-2 rounded-lg hover:bg-slate-700/50 transition-colors">
                        <svg class="w-5 h-5 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                        </svg>
                    </button>
                </div>
            </div>
        </aside>

        <!-- Main Content -->
 

      <script>
        // Mobile sidebar toggle functionality
        const mobileMenuBtn = document.getElementById('mobileMenuBtn');
        const mobileSidebar = document.getElementById('mobileSidebar');
        const closeMobileMenu = document.getElementById('closeMobileMenu');
        const sidebarOverlay = document.getElementById('sidebarOverlay');

        function openMobileSidebar() {
            mobileSidebar.classList.add('open');
            sidebarOverlay.classList.add('active');
            document.body.style.overflow = 'hidden';
        }

        function closeMobileSidebar() {
            mobileSidebar.classList.remove('open');
            sidebarOverlay.classList.remove('active');
            document.body.style.overflow = '';
        }

        function toggleMobileSidebar() {
            if (mobileSidebar.classList.contains('open')) {
                closeMobileSidebar();
            } else {
                openMobileSidebar();
            }
        }

        // Use toggle for the mobile menu button
        mobileMenuBtn.addEventListener('click', toggleMobileSidebar);
        closeMobileMenu.addEventListener('click', closeMobileSidebar);
        sidebarOverlay.addEventListener('click', closeMobileSidebar);

        // Close sidebar when clicking on a nav link on mobile
        document.querySelectorAll('#mobileSidebar .nav-item').forEach(item => {
            item.addEventListener('click', () => {
                if (window.innerWidth < 1024) {
                    closeMobileSidebar();
                }
            });
        });

        // Navigation highlighting functionality
        function setActiveNav(linkId) {
            // For desktop
            document.querySelectorAll('#mobileSidebar .nav-item, .sidebar .nav-item').forEach(item => {
                item.classList.remove('active');
            });
            
            // Add active class to both mobile and desktop versions
            const mobileItem = document.getElementById(linkId);
            const desktopItem = document.getElementById(linkId + '-desktop');
            
            if (mobileItem) mobileItem.classList.add('active');
            if (desktopItem) desktopItem.classList.add('active');
            
            sessionStorage.setItem('activeNav', linkId);
        }
        
        // Initialize navigation highlighting
        document.addEventListener('DOMContentLoaded', function() {
            const savedNav = sessionStorage.getItem('activeNav');
            const navIds = ['nav-dashboard', 'nav-items', 'nav-create-items', 'nav-import'];

            let activeIdToSet = 'nav-dashboard';

            if (savedNav && (document.getElementById(savedNav) || document.getElementById(savedNav + '-desktop'))) {
                activeIdToSet = savedNav;
            } else if (document.getElementById('nav-items')) {
                activeIdToSet = 'nav-items';
            }

            // Set active class for both versions
            const mobileItem = document.getElementById(activeIdToSet);
            const desktopItem = document.getElementById(activeIdToSet + '-desktop');
            
            if (mobileItem) mobileItem.classList.add('active');
            if (desktopItem) desktopItem.classList.add('active');

            // Add click event listeners to all nav items
            document.querySelectorAll('.nav-item').forEach(item => {
                item.addEventListener('click', function(e) {
                    if (this.href && this.href !== '#') {
                        // For actual navigation links
                        const linkId = this.id.replace('-desktop', '');
                        setActiveNav(linkId);
                    } else {
                        // For hash links
                        e.preventDefault();
                        const linkId = this.id.replace('-desktop', '');
                        setActiveNav(linkId);
                    }
                });
            });

            // Close mobile sidebar on window resize if it becomes desktop
            window.addEventListener('resize', function() {
                if (window.innerWidth >= 1024) {
                    closeMobileSidebar();
                }
            });
        });
    </script>
</body>
</html>