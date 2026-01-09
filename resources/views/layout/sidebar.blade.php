
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
        
        /* Ensure sidebar takes full height */
        .sidebar {
            overflow-y: auto;
        }
        
        /* Smooth hover effect */
        .nav-item {
            transition: all 0.2s ease;
        }
        
        .nav-item:hover:not(.active) {
            background-color: rgba(71, 85, 105, 0.3);
            transform: translateX(2px);
        }
    </style>

<div class="flex min-h-screen">
<aside class="sidebar fixed lg:relative top-0 left-0 z-40 bg-gradient-to-b from-slate-900 via-slate-900 to-slate-800 backdrop-blur border-r border-slate-700/80 shadow-xl lg:shadow-none">        
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

        <div class="p-4 space-y-2">
            <!-- <a href="#" id="nav-dashboard" class="nav-item flex items-center space-x-3 p-3 rounded-lg hover:bg-slate-700/50 transition-colors text-slate-300 hover:text-white">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" />
                </svg>
                <span>Dashboard</span>
            </a> -->

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

            <a href="import-log" id="nav-import" class="nav-item flex items-center space-x-3 p-3 rounded-lg hover:bg-slate-700/50 transition-colors text-slate-300 hover:text-white">
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
                    <p class="font-medium text-sm text-white">..</p>
                    <p class="text-xs text-slate-400">..</p>
                </div>
                <button class="p-2 rounded-lg hover:bg-slate-700/50 transition-colors">
                    <svg class="w-5 h-5 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                    </svg>
                </button>
            </div>
        </div>
    </aside>
    
   


<script>
    // Navigation highlighting functionality - Simple click-based only
    function setActiveNav(linkId) {
        // Remove active class from all nav items
        document.querySelectorAll('.nav-item').forEach(item => {
            item.classList.remove('active');
        });
        
        // Add active class to clicked item
        const activeItem = document.getElementById(linkId);
        if (activeItem) {
            activeItem.classList.add('active');
        }
        
        // Store active nav in sessionStorage (only for current session)
        sessionStorage.setItem('activeNav', linkId);
    }
    
    // Initialize navigation highlighting on page load
    document.addEventListener('DOMContentLoaded', function() {
        // Try to restore from sessionStorage
        const savedNav = sessionStorage.getItem('activeNav');
        
        // List of all navigation IDs
        const navIds = ['nav-dashboard', 'nav-items', 'nav-create-items', 'nav-import'];

        // Determine the ID of the link that should be active
        let activeIdToSet = 'nav-dashboard'; // Default to Dashboard

        if (savedNav && document.getElementById(savedNav)) {
            activeIdToSet = savedNav; // Use saved ID if it exists
        } else if (document.getElementById('nav-items')) {
             // If no saved ID, default to 'Items' as per your original JS default logic
             activeIdToSet = 'nav-items';
        }

        // Set the active class
        if (document.getElementById(activeIdToSet)) {
             document.getElementById(activeIdToSet).classList.add('active');
        }


        // Add click event listeners to all nav items
        document.querySelectorAll('.nav-item').forEach(item => {
            item.addEventListener('click', function() {
                // Prevent default navigation if using '#'
                // this.preventDefault(); 
                const linkId = this.id;
                setActiveNav(linkId);
            });
        });
    });
</script>

</body>
</html>