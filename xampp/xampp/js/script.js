// Full Page Slider JavaScript

document.addEventListener('DOMContentLoaded', function() {
    // Add background to navigation on scroll
    window.addEventListener('scroll', function() {
        var topBar = document.querySelector('.top-bar');
        var mainNav = document.querySelector('.main-nav');
        var navLinks = document.querySelectorAll('.top-bar .nav-link, .main-nav .nav-link, .social-icon');
        
        if (window.scrollY > 50) {
            // Change to white background with dark text
            topBar.style.backgroundColor = '#ffffff';
            mainNav.style.backgroundColor = '#ffffff';
            mainNav.style.boxShadow = '0 2px 10px rgba(0, 0, 0, 0.2)';
            
            // Change text color to dark
            navLinks.forEach(function(link) {
                link.style.color = '#333333';
            });
        } else {
            // Revert to transparent background with white text
            topBar.style.backgroundColor = 'transparent';
            mainNav.style.backgroundColor = 'transparent';
            
            // Keep the subtle shadow for contrast even when transparent
            mainNav.style.boxShadow = '0 2px 10px rgba(0, 0, 0, 0.2)';
            
            // Revert text color to white
            navLinks.forEach(function(link) {
                link.style.color = '#ffffff';
            });
        }
    });
    
    // Initialize the carousel
    var fullPageSlider = document.getElementById('fullPageSlider');
    var carousel = new bootstrap.Carousel(fullPageSlider, {
        interval: 8000,  // Change slides every 8 seconds
        pause: 'hover',  // Pause on mouse hover
        wrap: true,      // Continuous loop
        keyboard: true   // Allow keyboard navigation
    });
    
    // Handle video playback when slides change
    fullPageSlider.addEventListener('slide.bs.carousel', function(event) {
        // Pause all videos when slide changes
        var videos = document.querySelectorAll('.slide-video');
        videos.forEach(function(video) {
            video.pause();
        });
        
        // Get the next slide
        var nextSlide = event.relatedTarget;
        
        // If the next slide contains a video, play it
        if (nextSlide.classList.contains('video-slide')) {
            var video = nextSlide.querySelector('.slide-video');
            if (video) {
                // Small timeout to ensure the slide transition has started
                setTimeout(function() {
                    video.currentTime = 0; // Reset video to beginning
                    video.play();
                }, 50);
            }
        }
    });
    
    // Ensure videos in the active slide are playing on page load
    var activeSlide = fullPageSlider.querySelector('.carousel-item.active');
    if (activeSlide && activeSlide.classList.contains('video-slide')) {
        var video = activeSlide.querySelector('.slide-video');
        if (video) {
            video.play();
        }
    }
    
    // Handle fullscreen mode
    document.addEventListener('keydown', function(e) {
        // Press 'F' key to toggle fullscreen
        if (e.key.toLowerCase() === 'f') {
            toggleFullScreen();
        }
    });
    
    function toggleFullScreen() {
        if (!document.fullscreenElement) {
            // Enter fullscreen
            if (document.documentElement.requestFullscreen) {
                document.documentElement.requestFullscreen();
            } else if (document.documentElement.mozRequestFullScreen) { // Firefox
                document.documentElement.mozRequestFullScreen();
            } else if (document.documentElement.webkitRequestFullscreen) { // Chrome, Safari and Opera
                document.documentElement.webkitRequestFullscreen();
            } else if (document.documentElement.msRequestFullscreen) { // IE/Edge
                document.documentElement.msRequestFullscreen();
            }
        } else {
            // Exit fullscreen
            if (document.exitFullscreen) {
                document.exitFullscreen();
            } else if (document.mozCancelFullScreen) { // Firefox
                document.mozCancelFullScreen();
            } else if (document.webkitExitFullscreen) { // Chrome, Safari and Opera
                document.webkitExitFullscreen();
            } else if (document.msExitFullscreen) { // IE/Edge
                document.msExitFullscreen();
            }
        }
    }
    
    // Add swipe support for mobile devices
    var touchStartX = 0;
    var touchEndX = 0;
    
    fullPageSlider.addEventListener('touchstart', function(event) {
        touchStartX = event.changedTouches[0].screenX;
    }, false);
    
    fullPageSlider.addEventListener('touchend', function(event) {
        touchEndX = event.changedTouches[0].screenX;
        handleSwipe();
    }, false);
    
    function handleSwipe() {
        if (touchEndX < touchStartX - 50) {
            // Swipe left - next slide
            carousel.next();
        }
        if (touchEndX > touchStartX + 50) {
            // Swipe right - previous slide
            carousel.prev();
        }
    }
    
    // Preload videos and images for better performance
    function preloadMedia() {
        // Preload videos
        var videoSources = document.querySelectorAll('video source');
        videoSources.forEach(function(source) {
            var video = document.createElement('video');
            video.src = source.src;
        });
        
        // Preload images
        var images = document.querySelectorAll('.image-slide img');
        images.forEach(function(img) {
            var image = new Image();
            image.src = img.src;
        });
    }
    
    // Call preload function
    preloadMedia();
    
    // Animated Counter for Placement Statistics
    function animateCounters() {
        const statValues = document.querySelectorAll('.stat-value[data-start][data-end]');
        
        // Check if there are any counter elements on the page
        if (statValues.length === 0) return;
        
        // Function to check if element is in viewport
        function isInViewport(element) {
            const rect = element.getBoundingClientRect();
            return (
                rect.top <= (window.innerHeight || document.documentElement.clientHeight) &&
                rect.bottom >= 0
            );
        }
        
        // Function to animate a single counter
        function animateCounter(element) {
            // Get start and end values from data attributes
            const start = parseInt(element.getAttribute('data-start'));
            const end = parseInt(element.getAttribute('data-end'));
            const suffix = element.getAttribute('data-suffix') || '';
            
            // Duration of animation in milliseconds
            const duration = 2000;
            // Number of steps in the animation
            const steps = 5;
            // Calculate step size and interval
            const stepValue = Math.ceil((end - start) / steps);
            const interval = duration / steps;
            
            let current = start;
            let timer = null;
            
            // Clear any existing animation
            if (element.timer) {
                clearInterval(element.timer);
            }
            
            // Set initial value
            element.innerHTML = current + suffix;
            
            // Start animation
            element.timer = setInterval(() => {
                current += stepValue;
                
                // Make sure we don't exceed the end value
                if (current >= end) {
                    current = end;
                    clearInterval(element.timer);
                    element.timer = null;
                }
                
                // Update the element with the current value and suffix
                if (suffix === ' LPA') {
                    element.innerHTML = current + '<span class="text-small">LPA</span>';
                } else {
                    element.innerHTML = current + '<span class="plus">' + suffix + '</span>';
                }
            }, interval);
        }
        
        // Check if counters are in viewport and start animation
        function checkCounters() {
            statValues.forEach(element => {
                if (isInViewport(element) && !element.classList.contains('animated')) {
                    element.classList.add('animated');
                    animateCounter(element);
                }
            });
        }
        
        // Initial check
        checkCounters();
        
        // Check on scroll
        window.addEventListener('scroll', checkCounters);
    }
    
    // Call the counter animation function
    animateCounters();
});