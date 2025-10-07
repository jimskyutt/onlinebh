document.addEventListener('DOMContentLoaded', function() {
    const statusIndicator = document.querySelector('.user-status-indicator');
    
    if (!statusIndicator) return;

    // Function to update the session timestamp
    function updateSession() {
        fetch('/update-session', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            },
            credentials: 'same-origin'
        })
        .then(response => response.json())
        .then(data => {
            if (data.status === 'active') {
                statusIndicator.classList.add('active');
            }
        })
        .catch(error => console.error('Error updating session:', error));
    }

    // Update session every minute
    setInterval(updateSession, 60000);
    
    // Initial update
    updateSession();

    // Check for other active sessions
    function checkActiveSessions() {
        fetch('/check-active-sessions')
            .then(response => response.json())
            .then(data => {
                const sessionCount = data.active_sessions || 0;
                if (sessionCount > 0) {
                    console.log(`There are ${sessionCount} other active session(s)`);
                }
            })
            .catch(error => console.error('Error checking active sessions:', error));
    }

    // Check for active sessions every 2 minutes
    setInterval(checkActiveSessions, 120000);
    
    // Initial check
    checkActiveSessions();
});
