@props(['title' => 'Reactbits Demo'])

<div class="card">
    <div class="card-header">
        <h5>{{ $title }}</h5>
    </div>
    <div class="card-body">
        <p>This is a demo component showing how to use Reactbits in your Laravel Blade templates.</p>
        
        <!-- Example: Bounce Animation -->
        <div class="p-4">
            <h6>Bounce Animation Example</h6>
            <div 
                x-data="{ 
                    show: false,
                    toggle() { this.show = !this.show; }
                }" 
                class="text-center"
            >
                <button 
                    @click="toggle()" 
                    class="btn btn-primary mb-3"
                    x-text="show ? 'Hide Bounce' : 'Show Bounce'"
                ></button>
                
                <div x-show="show" x-transition:enter="transition ease-out duration-300" x-transition:leave="transition ease-in duration-300">
                    <div class="alert alert-info">
                        <i class="fas fa-info-circle"></i>
                        This content has a bounce animation!
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Example: Fade Animation -->
        <div class="p-4">
            <h6>Fade Animation Example</h6>
            <div 
                x-data="{ 
                    visible: true,
                    toggle() { this.visible = !this.visible; }
                }" 
                class="text-center"
            >
                <button 
                    @click="toggle()" 
                    class="btn btn-secondary mb-3"
                    x-text="visible ? 'Hide Fade' : 'Show Fade'"
                ></button>
                
                <div 
                    x-show="visible" 
                    x-transition:enter="transition ease-out duration-500" 
                    x-transition:leave="transition ease-in duration-500"
                    class="mt-3 p-3 bg-light rounded"
                >
                    <p class="mb-0">This content fades in and out smoothly.</p>
                    <button class="btn btn-sm btn-outline-primary">Click Me</button>
                </div>
            </div>
        </div>
        
        <div class="mt-4">
            <small class="text-muted">
                <strong>Note:</strong> Reactbits provides many more components including:
                Slide, Scale, Rotate, Flip, and more. Visit 
                <a href="https://reactbits.dev" target="_blank">reactbits.dev</a> for documentation.
            </small>
        </div>
    </div>
</div>

<script>
// You can also use Reactbits components in pure JavaScript
document.addEventListener('DOMContentLoaded', function() {
    // Example of programmatic usage
    const bounceElement = document.querySelector('.bounce-example');
    if (bounceElement && window.ReactBits) {
        // Add bounce animation to any element
        bounceElement.classList.add('animate-bounce');
    }
});
</script>
