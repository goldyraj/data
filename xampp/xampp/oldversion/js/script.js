// Full Page Slider JavaScript

document.addEventListener('DOMContentLoaded', function() {
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
});