/**
 * Exam Security System
 * Provides anti-cheating measures and security monitoring for online exams
 */

class ExamSecurity {
    constructor(options = {}) {
        this.options = {
            maxTabSwitches: options.maxTabSwitches || 3,
            requireFullscreen: options.requireFullscreen !== false,
            disableRightClick: options.disableRightClick !== false,
            disableCopyPaste: options.disableCopyPaste !== false,
            autoSubmitOnViolation: options.autoSubmitOnViolation !== false,
            logEndpoint: options.logEndpoint || '/log-violation',
            updateEndpoint: options.updateEndpoint || '/update-time',
            ...options
        };

        this.violations = {
            tabSwitches: 0,
            fullscreenExits: 0,
            rightClicks: 0,
            copyAttempts: 0,
            pasteAttempts: 0,
            suspiciousActivities: 0
        };

        this.startTime = Date.now();
        this.lastActivity = Date.now();
        this.isFullscreen = false;
        this.examSessionId = options.examSessionId;
        this.examId = options.examId;
        this.csrfToken = options.csrfToken;

        this.init();
    }

    init() {
        this.setupEventListeners();
        this.startActivityMonitoring();
        this.setupSecurityMeasures();
        this.checkInitialFullscreen();
    }

    setupEventListeners() {
        // Tab switching detection
        document.addEventListener('visibilitychange', this.handleVisibilityChange.bind(this));
        
        // Fullscreen changes
        document.addEventListener('fullscreenchange', this.handleFullscreenChange.bind(this));
        document.addEventListener('webkitfullscreenchange', this.handleFullscreenChange.bind(this));
        document.addEventListener('mozfullscreenchange', this.handleFullscreenChange.bind(this));
        document.addEventListener('MSFullscreenChange', this.handleFullscreenChange.bind(this));

        // Window focus/blur
        window.addEventListener('focus', this.handleWindowFocus.bind(this));
        window.addEventListener('blur', this.handleWindowBlur.bind(this));

        // Before unload
        window.addEventListener('beforeunload', this.handleBeforeUnload.bind(this));

        // Keyboard shortcuts
        document.addEventListener('keydown', this.handleKeydown.bind(this));

        // Mouse events
        document.addEventListener('contextmenu', this.handleContextMenu.bind(this));
        document.addEventListener('copy', this.handleCopy.bind(this));
        document.addEventListener('paste', this.handlePaste.bind(this));

        // Dev tools detection
        this.detectDevTools();
    }

    setupSecurityMeasures() {
        // Disable right click
        if (this.options.disableRightClick) {
            document.addEventListener('contextmenu', (e) => {
                e.preventDefault();
                this.logViolation('right_click', 'Right click disabled');
                return false;
            });
        }

        // Disable copy/paste
        if (this.options.disableCopyPaste) {
            document.addEventListener('copy', (e) => {
                e.preventDefault();
                this.logViolation('copy_attempt', 'Copy attempt blocked');
                return false;
            });

            document.addEventListener('paste', (e) => {
                e.preventDefault();
                this.logViolation('paste_attempt', 'Paste attempt blocked');
                return false;
            });

            // Disable drag and drop
            document.addEventListener('dragstart', (e) => {
                e.preventDefault();
                return false;
            });

            document.addEventListener('drop', (e) => {
                e.preventDefault();
                return false;
            });
        }

        // Disable text selection
        document.addEventListener('selectstart', (e) => {
            if (this.options.disableCopyPaste) {
                e.preventDefault();
                return false;
            }
        });

        // Disable print
        window.addEventListener('beforeprint', (e) => {
            this.logViolation('print_attempt', 'Print attempt detected');
            e.preventDefault();
            return false;
        });
    }

    checkInitialFullscreen() {
        if (this.options.requireFullscreen && !this.isFullscreen()) {
            this.showFullscreenWarning();
        }
    }

    isFullscreen() {
        return !!(document.fullscreenElement || 
                 document.webkitFullscreenElement || 
                 document.mozFullScreenElement || 
                 document.msFullscreenElement);
    }

    showFullscreenWarning() {
        const overlay = document.createElement('div');
        overlay.id = 'fullscreen-warning-overlay';
        overlay.style.cssText = `
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.9);
            color: white;
            display: flex;
            align-items: center;
            justify-content: center;
            z-index: 99999;
            font-family: Arial, sans-serif;
        `;

        overlay.innerHTML = `
            <div style="text-align: center; padding: 40px;">
                <h2 style="margin-bottom: 20px;">⚠️ Fullscreen Required</h2>
                <p style="margin-bottom: 30px; font-size: 18px;">This exam requires fullscreen mode to ensure academic integrity.</p>
                <button onclick="examSecurity.enterFullscreen()" style="
                    background: #007bff;
                    color: white;
                    border: none;
                    padding: 15px 30px;
                    font-size: 16px;
                    border-radius: 25px;
                    cursor: pointer;
                ">Enter Fullscreen Mode</button>
            </div>
        `;

        document.body.appendChild(overlay);
    }

    hideFullscreenWarning() {
        const overlay = document.getElementById('fullscreen-warning-overlay');
        if (overlay) {
            overlay.remove();
        }
    }

    enterFullscreen() {
        const element = document.documentElement;
        
        if (element.requestFullscreen) {
            element.requestFullscreen();
        } else if (element.webkitRequestFullscreen) {
            element.webkitRequestFullscreen();
        } else if (element.mozRequestFullScreen) {
            element.mozRequestFullScreen();
        } else if (element.msRequestFullscreen) {
            element.msRequestFullscreen();
        }

        this.hideFullscreenWarning();
    }

    handleVisibilityChange() {
        if (document.hidden) {
            this.logViolation('tab_switch', 'Tab switched or window minimized');
        }
        this.updateLastActivity();
    }

    handleFullscreenChange() {
        const wasFullscreen = this.isFullscreen;
        this.isFullscreen = this.isFullscreen();

        if (wasFullscreen && !this.isFullscreen && this.options.requireFullscreen) {
            this.logViolation('fullscreen_exit', 'Fullscreen mode exited');
            this.showFullscreenWarning();
        } else if (this.isFullscreen) {
            this.hideFullscreenWarning();
        }
    }

    handleWindowFocus() {
        this.updateLastActivity();
    }

    handleWindowBlur() {
        if (!document.hidden) {
            this.logViolation('window_blur', 'Window lost focus');
        }
    }

    handleBeforeUnload(e) {
        const message = 'You have an ongoing exam. Are you sure you want to leave? Your progress may be lost.';
        e.preventDefault();
        e.returnValue = message;
        return message;
    }

    handleKeydown(e) {
        // Disable dangerous keyboard shortcuts
        const dangerousKeys = [
            // F12 - Developer Tools
            { key: 'F12', ctrl: false, shift: false, alt: false },
            // Ctrl+Shift+I - Developer Tools
            { key: 'I', ctrl: true, shift: true, alt: false },
            // Ctrl+Shift+J - Console
            { key: 'J', ctrl: true, shift: true, alt: false },
            // Ctrl+Shift+C - Element Inspector
            { key: 'C', ctrl: true, shift: true, alt: false },
            // Ctrl+U - View Source
            { key: 'U', ctrl: true, shift: false, alt: false },
            // Ctrl+Shift+U - View Source (some browsers)
            { key: 'U', ctrl: true, shift: true, alt: false }
        ];

        const isDangerous = dangerousKeys.some(combo => 
            e.key === combo.key && 
            e.ctrlKey === combo.ctrl && 
            e.shiftKey === combo.shift && 
            e.altKey === combo.alt
        );

        if (isDangerous) {
            e.preventDefault();
            this.logViolation('suspicious_activity', `Dangerous keyboard shortcut blocked: ${e.key}`);
            return false;
        }

        // Disable Alt+Tab detection (limited effectiveness)
        if (e.altKey && e.key === 'Tab') {
            this.logViolation('alt_tab', 'Alt+Tab detected');
        }
    }

    handleContextMenu(e) {
        if (this.options.disableRightClick) {
            e.preventDefault();
            this.logViolation('right_click', 'Right click blocked');
            return false;
        }
    }

    handleCopy(e) {
        if (this.options.disableCopyPaste) {
            e.preventDefault();
            this.logViolation('copy_attempt', 'Copy attempt blocked');
            return false;
        }
    }

    handlePaste(e) {
        if (this.options.disableCopyPaste) {
            e.preventDefault();
            this.logViolation('paste_attempt', 'Paste attempt blocked');
            return false;
        }
    }

    detectDevTools() {
        const threshold = 160;
        
        setInterval(() => {
            if (window.outerHeight - window.innerHeight > threshold || 
                window.outerWidth - window.innerWidth > threshold) {
                this.logViolation('dev_tools', 'Developer tools detected');
            }
        }, 1000);
    }

    logViolation(type, description = '') {
        this.violations[this.getViolationProperty(type)]++;
        this.updateLastActivity();

        const violationData = {
            exam_session_id: this.examSessionId,
            student_id: this.options.studentId,
            violation_type: type,
            description: description,
            metadata: {
                timestamp: new Date().toISOString(),
                userAgent: navigator.userAgent,
                url: window.location.href,
                totalViolations: this.getTotalViolations(),
                timeInExam: Date.now() - this.startTime
            }
        };

        // Send to server
        fetch(this.options.logEndpoint, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': this.csrfToken
            },
            body: JSON.stringify(violationData)
        })
        .then(response => response.json())
        .then(data => {
            if (data.action === 'auto_submit') {
                this.autoSubmit('violations');
            } else {
                this.updateViolationDisplay();
            }
        })
        .catch(error => {
            console.error('Error logging violation:', error);
        });

        // Check if auto-submit should be triggered
        if (this.shouldAutoSubmit()) {
            this.autoSubmit('violations');
        }
    }

    getViolationProperty(type) {
        const mapping = {
            'tab_switch': 'tabSwitches',
            'fullscreen_exit': 'fullscreenExits',
            'right_click': 'rightClicks',
            'copy_attempt': 'copyAttempts',
            'paste_attempt': 'pasteAttempts',
            'suspicious_activity': 'suspiciousActivities'
        };
        return mapping[type] || 'suspiciousActivities';
    }

    getTotalViolations() {
        return Object.values(this.violations).reduce((sum, count) => sum + count, 0);
    }

    shouldAutoSubmit() {
        return this.options.autoSubmitOnViolation && 
               (this.violations.tabSwitches >= this.options.maxTabSwitches ||
                this.getTotalViolations() >= this.options.maxTabSwitches);
    }

    updateViolationDisplay() {
        // Update violation warning display
        const warningElement = document.getElementById('violationWarning');
        const messageElement = document.getElementById('violationMessage');
        const remainingElement = document.getElementById('violationsRemaining');

        if (warningElement && messageElement && remainingElement) {
            if (this.getTotalViolations() > 0) {
                warningElement.style.display = 'block';
                messageElement.textContent = 'Security violation detected! Multiple violations will result in automatic submission.';
                remainingElement.textContent = this.options.maxTabSwitches - this.violations.tabSwitches;
            }
        }
    }

    autoSubmit(reason) {
        // Disable further interactions
        document.body.style.pointerEvents = 'none';
        
        // Show auto-submit message
        const message = document.createElement('div');
        message.style.cssText = `
            position: fixed;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            background: #dc3545;
            color: white;
            padding: 30px;
            border-radius: 10px;
            z-index: 999999;
            text-align: center;
            font-size: 18px;
        `;
        message.innerHTML = `
            <h3>⚠️ Exam Auto-Submitted</h3>
            <p>Your exam has been automatically submitted due to: ${reason}</p>
            <p>Redirecting to results...</p>
        `;
        document.body.appendChild(message);

        // Submit exam
        setTimeout(() => {
            const form = document.getElementById('examForm');
            if (form) {
                const formData = new FormData(form);
                formData.append('action', 'submit');
                formData.append('auto_submit_reason', reason);
                
                fetch(form.action, {
                    method: 'POST',
                    body: formData,
                    headers: {
                        'X-CSRF-TOKEN': this.csrfToken
                    }
                })
                .then(() => {
                    window.location.href = this.options.resultsUrl || '/exam/results';
                });
            }
        }, 3000);
    }

    updateLastActivity() {
        this.lastActivity = Date.now();
    }

    startActivityMonitoring() {
        // Monitor for inactivity
        setInterval(() => {
            const inactiveTime = Date.now() - this.lastActivity;
            
            if (inactiveTime > 300000) { // 5 minutes
                this.logViolation('inactivity', `No activity for ${Math.round(inactiveTime / 60000)} minutes`);
            }
        }, 60000); // Check every minute
    }

    // Public methods
    getViolationStats() {
        return {
            ...this.violations,
            total: this.getTotalViolations(),
            timeInExam: Date.now() - this.startTime,
            lastActivity: this.lastActivity
        };
    }

    resetViolations() {
        Object.keys(this.violations).forEach(key => {
            this.violations[key] = 0;
        });
    }

    destroy() {
        // Remove all event listeners
        document.removeEventListener('visibilitychange', this.handleVisibilityChange);
        document.removeEventListener('fullscreenchange', this.handleFullscreenChange);
        document.removeEventListener('webkitfullscreenchange', this.handleFullscreenChange);
        document.removeEventListener('mozfullscreenchange', this.handleFullscreenChange);
        document.removeEventListener('MSFullscreenChange', this.handleFullscreenChange);
        window.removeEventListener('focus', this.handleWindowFocus);
        window.removeEventListener('blur', this.handleWindowBlur);
        window.removeEventListener('beforeunload', this.handleBeforeUnload);
        document.removeEventListener('keydown', this.handleKeydown);
        document.removeEventListener('contextmenu', this.handleContextMenu);
        document.removeEventListener('copy', this.handleCopy);
        document.removeEventListener('paste', this.handlePaste);

        this.hideFullscreenWarning();
    }
}

// Global instance
window.examSecurity = null;

// Initialize when DOM is ready
document.addEventListener('DOMContentLoaded', function() {
    const examSecurityElement = document.getElementById('exam-security-config');
    if (examSecurityElement) {
        const config = JSON.parse(examSecurityElement.textContent);
        window.examSecurity = new ExamSecurity(config);
    }
});

// Export for module systems
if (typeof module !== 'undefined' && module.exports) {
    module.exports = ExamSecurity;
}
