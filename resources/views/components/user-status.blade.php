@props(['isActive' => false])

<div class="user-status-indicator">
    <div class="status-container {{ $isActive ? 'active' : 'inactive' }}" 
         title="{{ $isActive ? 'Online' : 'Offline' }}">
        <div class="status-dot {{ auth()->user()->is_online ? 'active' : 'inactive' }}" title="{{ auth()->user()->is_online ? 'Active' : 'Offline' }}"></div>
        <div class="status-content">
            <span class="status-text">{{ auth()->user()->is_online ? 'Active' : 'Offline' }}</span>            
        </div>
    </div>
</div>

<style>
.user-status-indicator {
    position: fixed;
    top: 20px;
    right: 10px;
    z-index: 1050;
    font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, 'Helvetica Neue', Arial, sans-serif;
}

.status-container {
    display: flex;
    align-items: center;
    background: rgba(255, 255, 255, 0.95);
    border-radius: 24px;
    padding: 6px 15px 6px 6px;
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
    transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    backdrop-filter: blur(5px);
    border: 1px solid rgba(0, 0, 0, 0.05);
}

.status-container:hover {
    transform: translateY(-2px);
    box-shadow: 0 6px 16px rgba(0, 0, 0, 0.12);
}

.status-dot {
    width: 12px;
    height: 12px;
    border-radius: 50%;
    margin-right: 10px;
    background-color: #e0e0e0;
    position: relative;
    transition: all 0.3s ease;
}

.status-dot.active {
    background-color: #4CAF50; 
    box-shadow: 0 0 0 3px rgba(76, 175, 80, 0.2);
    animation: pulse 2s infinite;
}

.status-dot.inactive {
    background-color: #9e9e9e;
}

@keyframes pulse {
    0% {
        box-shadow: 0 0 0 0 rgba(76, 175, 80, 0.4);
    }
    70% {
        box-shadow: 0 0 0 8px rgba(76, 175, 80, 0);
    }
    100% {
        box-shadow: 0 0 0 0 rgba(76, 175, 80, 0);
    }
}

.status-content {
    display: flex;
    align-items: center;
    gap: 8px;
}

.user-icon {
    width: 18px;
    height: 18px;
    color: #555;
}

.status-container.active .user-icon {
    color: #2e7d32;
}

.status-text {
    font-size: 14px;
    font-weight: 500;
    color: #333;
    letter-spacing: 0.3px;
}

.status-container.active .status-text {
    color: #2e7d32;
}

@media (max-width: 768px) {
    .user-status-indicator {
        top: 10px;
        right: 10px;
    }
    
    .status-container {
        padding: 4px 12px 4px 4px;
    }
    
    .status-text {
        font-size: 13px;
    }
}
</style>
