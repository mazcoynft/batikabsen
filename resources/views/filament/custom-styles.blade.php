<style>
    /* Custom gradient backgrounds for stats cards */
    .fi-wi-stats-overview-stat {
        transition: all 0.3s ease;
        border-radius: 12px !important;
        overflow: hidden;
    }
    
    .fi-wi-stats-overview-stat:hover {
        transform: translateY(-2px);
        box-shadow: 0 10px 25px rgba(0,0,0,0.1);
    }
    
    /* Custom brand logo animation */
    .brand-logo {
        animation: pulse 2s infinite;
    }
    
    @keyframes pulse {
        0%, 100% { transform: scale(1); }
        50% { transform: scale(1.05); }
    }
    
    /* Custom navigation styling */
    .fi-sidebar-nav {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    }
    
    /* Custom header styling */
    .fi-topbar {
        background: linear-gradient(90deg, #4facfe 0%, #00f2fe 100%);
        border-bottom: none;
    }
</style>