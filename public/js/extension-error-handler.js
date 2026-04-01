/**
 * Extension Error Handler
 * Prevents browser extension communication errors from breaking the application
 */

// Prevent extension runtime errors
window.addEventListener('error', function(e) {
    // Filter out extension-related errors
    if (e.message && e.message.includes('Could not establish connection')) {
        console.warn('Browser extension communication error prevented:', e.message);
        e.preventDefault();
        return false;
    }
    
    if (e.message && e.message.includes('Receiving end does not exist')) {
        console.warn('Browser extension communication error prevented:', e.message);
        e.preventDefault();
        return false;
    }
    
    if (e.filename && e.filename.includes('all-frames.js')) {
        console.warn('Browser extension script error prevented:', e.message);
        e.preventDefault();
        return false;
    }
});

// Prevent unhandled promise rejections from extensions
window.addEventListener('unhandledrejection', function(e) {
    if (e.reason && e.reason.message && 
        (e.reason.message.includes('Could not establish connection') ||
         e.reason.message.includes('Receiving end does not exist'))) {
        console.warn('Browser extension promise rejection prevented:', e.reason.message);
        e.preventDefault();
        return false;
    }
});

// Override potential extension methods that might cause errors
if (typeof chrome !== 'undefined' && chrome.runtime) {
    const originalSendMessage = chrome.runtime.sendMessage;
    chrome.runtime.sendMessage = function() {
        try {
            return originalSendMessage.apply(this, arguments);
        } catch (error) {
            if (error.message.includes('Could not establish connection') ||
                error.message.includes('Receiving end does not exist')) {
                console.warn('Extension message error prevented:', error.message);
                return Promise.resolve();
            }
            throw error;
        }
    };
}

console.log('Extension error handler loaded');
