<!-- Live Streaming Banner -->
<div x-data="liveStreamingBanner()" x-init="init()" class="site-live-banner relative">
    <!-- Banner Container -->
    <div class="bg-gradient-to-r from-red-600 to-red-500 text-white shadow-lg">
        <div class="container mx-auto px-6 py-4">
            <div class="flex items-center justify-between">
                <div class="flex items-center space-x-3">
                    <div class="relative">
                        <div class="w-3 h-3 bg-white rounded-full animate-pulse"></div>
                        <div class="absolute inset-0 w-3 h-3 bg-white rounded-full animate-ping"></div>
                    </div>
                    <div>
                        <h3 class="text-lg font-bold text-white">Live Classes</h3>
                        <p class="text-sm text-white opacity-90" x-show="loading">Loading...</p>
                        <p class="text-sm text-white opacity-90" x-show="!loading">
                            <span x-text="liveClasses.length"></span> class<span x-bind:plural="liveClasses.length !== 1">es</span> available now
                        </p>
                    </div>
                </div>
                <button @click="showModal = true"
                        x-show="!loading && liveClasses.length > 0"
                        class="bg-white text-red-600 px-4 py-2 rounded-lg font-semibold hover:bg-red-50 transition-colors">
                    Join Live Class
                </button>
            </div>
        </div>
    </div>

    <!-- Live Classes Modal -->
    <div x-show="showModal" 
         x-transition:enter="transition ease-out duration-300"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="transition ease-in duration-200"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0"
         class="fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center p-4"
         @click.self="showModal = false">
        
        <div class="bg-white rounded-xl max-w-4xl w-full max-h-[80vh] overflow-hidden"
             x-transition:enter="transition ease-out duration-300"
             x-transition:enter-start="opacity-0 transform scale-90"
             x-transition:enter-end="opacity-100 transform scale-100"
             x-transition:leave="transition ease-in duration-200"
             x-transition:leave-start="opacity-100 transform scale-100"
             x-transition:leave-end="opacity-0 transform scale-90">
            
            <!-- Modal Header -->
            <div class="bg-gradient-to-r from-red-600 to-red-500 text-white p-6">
                <div class="flex items-center justify-between">
                    <h2 class="text-2xl font-bold">Available Live Classes</h2>
                    <button @click="showModal = false" class="text-white hover:text-gray-200 transition-colors">
                        <i class="fas fa-times text-xl"></i>
                    </button>
                </div>
            </div>

            <!-- Modal Body -->
            <div class="p-6 max-h-[60vh] overflow-y-auto">
                <!-- Loading State -->
                <div x-show="checkingAccess" class="text-center py-8">
                    <div class="inline-block animate-spin rounded-full h-8 w-8 border-b-2 border-red-600"></div>
                    <p class="mt-2 text-gray-600">Checking access...</p>
                </div>

                <!-- Live Classes List -->
                <div x-show="!checkingAccess" class="space-y-4">
                    <template x-for="liveClass in liveClasses" :key="liveClass.id">
                        <div class="border border-gray-200 rounded-lg p-4 hover:shadow-md transition-shadow">
                            <div class="flex items-start justify-between">
                                <div class="flex-1">
                                    <h3 class="text-lg font-semibold text-gray-900" x-text="liveClass.title"></h3>
                                    <p class="text-gray-600 mt-1" x-text="liveClass.description"></p>
                                    
                                    <div class="mt-3 flex flex-wrap gap-2">
                                        <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                            <i class="fas fa-book mr-1"></i>
                                            <span x-text="liveClass.courses.map(c => c.course_name).join(', ')"></span>
                                        </span>
                                        <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                            <i class="fas fa-building mr-1"></i>
                                            <span x-text="liveClass.branches.map(b => b.name).join(', ')"></span>
                                        </span>
                                        <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-purple-100 text-purple-800">
                                            <i class="fas fa-clock mr-1"></i>
                                            <span x-text="formatTime(liveClass.start_time) + ' - ' + formatTime(liveClass.end_time)"></span>
                                        </span>
                                    </div>

                                    <div class="mt-3" x-show="liveClass.teacher">
                                        <span class="text-sm text-gray-500">
                                            <i class="fas fa-chalkboard-teacher mr-1"></i>
                                            <span x-text="liveClass.teacher.teacher_name"></span>
                                        </span>
                                    </div>
                                </div>

                                <div class="ml-4">
                                    <button @click="checkAccess(liveClass)" 
                                            :disabled="checkingAccess"
                                            class="bg-red-600 text-white px-4 py-2 rounded-lg font-semibold hover:bg-red-700 transition-colors disabled:opacity-50">
                                        Join Class
                                    </button>
                                </div>
                            </div>
                        </div>
                    </template>

                    <!-- No Classes Available -->
                    <div x-show="liveClasses.length === 0" class="text-center py-8">
                        <i class="fas fa-video-slash text-4xl text-gray-400 mb-4"></i>
                        <p class="text-gray-600">No live classes are currently available.</p>
                        <p class="text-sm text-gray-500 mt-2">Check back later for upcoming sessions.</p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Access Check Modal -->
    <div x-show="accessModal.show" 
         x-transition:enter="transition ease-out duration-300"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="transition ease-in duration-200"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0"
         class="fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center p-4"
         @click.self="accessModal.show = false">
        
        <div class="bg-white rounded-xl max-w-md w-full"
             x-transition:enter="transition ease-out duration-300"
             x-transition:enter-start="opacity-0 transform scale-90"
             x-transition:enter-end="opacity-100 transform scale-100"
             x-transition:leave="transition ease-in duration-200"
             x-transition:leave-start="opacity-100 transform scale-100"
             x-transition:leave-end="opacity-0 transform scale-90">
            
            <div class="p-6">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-lg font-semibold">Access Check</h3>
                    <button @click="accessModal.show = false" class="text-gray-400 hover:text-gray-600">
                        <i class="fas fa-times"></i>
                    </button>
                </div>

                <!-- Can Join -->
                <div x-show="accessModal.data.canJoin" class="text-center">
                    <div class="w-16 h-16 bg-green-100 rounded-full flex items-center justify-center mx-auto mb-4">
                        <i class="fas fa-check text-green-600 text-2xl"></i>
                    </div>
                    <p class="text-gray-900 mb-4">You can join this live class!</p>
                    <button @click="joinClass(accessModal.selectedClass)" 
                            class="w-full bg-green-600 text-white py-2 rounded-lg font-semibold hover:bg-green-700 transition-colors">
                        Enter Live Class
                    </button>
                </div>

                <!-- Requires Payment -->
                <div x-show="!accessModal.data.canJoin && accessModal.data.requiresPayment">
                    <div class="text-center">
                        <div class="w-16 h-16 bg-yellow-100 rounded-full flex items-center justify-center mx-auto mb-4">
                            <i class="fas fa-dollar-sign text-yellow-600 text-2xl"></i>
                        </div>
                        <p class="text-gray-900 mb-2" x-text="accessModal.data.reason"></p>
                        <p class="text-2xl font-bold text-gray-900 mb-4">
                            <span x-text="accessModal.data.currency"></span> <span x-text="accessModal.data.paymentAmount"></span>
                        </p>
                        
                        <!-- Email Input for Payment Verification -->
                        <div x-show="!accessModal.emailVerified" class="mb-4">
                            <p class="text-sm text-gray-600 mb-2">Enter your email to check if you've already paid:</p>
                            <div class="flex gap-2">
                                <input type="email" 
                                       x-model="accessModal.email" 
                                       placeholder="Enter your email address"
                                       class="flex-1 px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-red-500 focus:border-transparent"
                                       @keyup.enter="verifyPayment()">
                                <button @click="verifyPayment()" 
                                        :disabled="!accessModal.email || accessModal.verifying"
                                        class="bg-blue-600 text-white px-4 py-2 rounded-lg font-semibold hover:bg-blue-700 transition-colors disabled:opacity-50">
                                    <span x-show="!accessModal.verifying">Check</span>
                                    <span x-show="accessModal.verifying">Checking...</span>
                                </button>
                            </div>
                        </div>
                        
                        <!-- Already Paid Message -->
                        <div x-show="accessModal.emailVerified && accessModal.data.canJoin" class="mb-4 p-3 bg-green-50 border border-green-200 rounded-lg">
                            <p class="text-green-800 text-sm">
                                <i class="fas fa-check-circle mr-1"></i>
                                Payment verified! You can join the class.
                            </p>
                        </div>
                        
                        <button @click="processPayment(accessModal.selectedClass)" 
                                x-show="!accessModal.emailVerified || !accessModal.data.canJoin"
                                class="w-full bg-yellow-600 text-white py-2 rounded-lg font-semibold hover:bg-yellow-700 transition-colors">
                            Proceed to Payment
                        </button>
                        
                        <button @click="joinClass(accessModal.selectedClass)" 
                                x-show="accessModal.emailVerified && accessModal.data.canJoin"
                                class="w-full bg-green-600 text-white py-2 rounded-lg font-semibold hover:bg-green-700 transition-colors">
                            Enter Live Class
                        </button>
                    </div>
                </div>

                <!-- Cannot Join -->
                <div x-show="!accessModal.data.canJoin && !accessModal.data.requiresPayment">
                    <div class="text-center">
                        <div class="w-16 h-16 bg-red-100 rounded-full flex items-center justify-center mx-auto mb-4">
                            <i class="fas fa-times text-red-600 text-2xl"></i>
                        </div>
                        <p class="text-gray-900" x-text="accessModal.data.reason"></p>
                        <button @click="accessModal.show = false" 
                                class="w-full bg-gray-600 text-white py-2 rounded-lg font-semibold hover:bg-gray-700 transition-colors mt-4">
                            Close
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        function liveStreamingBanner() {
            return {
                loading: true,
                checkingAccess: false,
                showModal: false,
                accessModal: {
                    show: false,
                    data: {},
                    selectedClass: null,
                    email: '',
                    emailVerified: false,
                    verifying: false
                },
                liveClasses: [],

                async init() {
                    await this.loadLiveClasses();
                    // Refresh every 30 seconds
                    setInterval(() => this.loadLiveClasses(), 30000);
                },

                async loadLiveClasses() {
                    try {
                        const response = await fetch('/api/live-classes/homepage');
                        const data = await response.json();
                        console.log('Live Classes API Response:', data);
                        this.liveClasses = data.liveClasses || [];
                        console.log('Live Classes loaded:', this.liveClasses.length);
                    } catch (error) {
                        console.error('Error loading live classes:', error);
                    } finally {
                        this.loading = false;
                    }
                },

                async checkAccess(liveClass) {
                    this.checkingAccess = true;
                    this.accessModal.selectedClass = liveClass;
                    // Reset email verification state
                    this.accessModal.email = '';
                    this.accessModal.emailVerified = false;
                    this.accessModal.verifying = false;
                    
                    try {
                        const response = await fetch(`/api/live-classes/${liveClass.id}/check-access`);
                        const data = await response.json();
                        this.accessModal.data = data;
                        
                        // Handle different access scenarios
                        if (data.reason === 'This is not your course') {
                            // Show not your course message
                            alert('This is not your course. You can only join live classes for courses you are enrolled in.');
                            return;
                        }
                        
                        if (data.canJoin) {
                            // Direct join for enrolled students
                            this.joinClass(liveClass);
                            return;
                        }
                        
                        if (data.requiresPayment) {
                            // Show payment modal with email verification
                            this.accessModal.show = true;
                            return;
                        }
                        
                        // Show other access denied messages
                        alert(data.reason || 'Access denied');
                        
                    } catch (error) {
                        console.error('Error checking access:', error);
                        alert('Error checking access. Please try again.');
                    } finally {
                        this.checkingAccess = false;
                    }
                },

                async verifyPayment() {
                    if (!this.accessModal.email) {
                        return;
                    }
                    
                    this.accessModal.verifying = true;
                    
                    try {
                        const response = await fetch(`/api/live-classes/${this.accessModal.selectedClass.id}/check-access?email=${encodeURIComponent(this.accessModal.email)}`);
                        const data = await response.json();
                        
                        if (data.canJoin) {
                            // Payment verified - update UI
                            this.accessModal.data = data;
                            this.accessModal.emailVerified = true;
                        } else {
                            // No payment found - show message
                            this.accessModal.emailVerified = false;
                            alert('No payment found for this email. Please proceed with payment.');
                        }
                    } catch (error) {
                        console.error('Error verifying payment:', error);
                        alert('Error verifying payment. Please try again.');
                    } finally {
                        this.accessModal.verifying = false;
                    }
                },

                joinClass(liveClass) {
                    // Check for existing session
                    if (this.checkExistingSession()) {
                        alert('You already have an active live class session. Please close it first.');
                        return;
                    }

                    // Open live class in new window with email parameter if verified
                    let roomUrl = `/live-classes/${liveClass.id}/room`;
                    if (this.accessModal.emailVerified && this.accessModal.email) {
                        roomUrl += `?email=${encodeURIComponent(this.accessModal.email)}`;
                    }
                    window.open(roomUrl, '_blank', 'width=1200,height=800,scrollbars=yes,resizable=yes');
                    
                    // Track session
                    this.trackSession(liveClass.id);
                    
                    this.accessModal.show = false;
                    this.showModal = false;
                },

                processPayment(liveClass) {
                    // Redirect to payment page
                    window.location.href = `/live-classes/${liveClass.id}/payment`;
                },

                checkExistingSession() {
                    const sessions = JSON.parse(localStorage.getItem('liveClassSessions') || '[]');
                    const activeSessions = sessions.filter(s => 
                        s.status === 'active' && 
                        new Date(s.expiresAt) > new Date()
                    );
                    return activeSessions.length > 0;
                },

                trackSession(classId) {
                    const sessions = JSON.parse(localStorage.getItem('liveClassSessions') || '[]');
                    const session = {
                        classId: classId,
                        startedAt: new Date().toISOString(),
                        expiresAt: new Date(Date.now() + 4 * 60 * 60 * 1000).toISOString(), // 4 hours
                        status: 'active'
                    };
                    sessions.push(session);
                    localStorage.setItem('liveClassSessions', JSON.stringify(sessions));
                },

                formatTime(timeString) {
                    const date = new Date(timeString);
                    return date.toLocaleTimeString('en-US', { 
                        hour: '2-digit', 
                        minute: '2-digit',
                        hour12: true 
                    });
                }
            }
        }
    </script>
</div>
