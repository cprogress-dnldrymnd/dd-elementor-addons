/**
 * DD Progress Slider Initialization
 * Handles the instantiation of Swiper and synchronization with the custom progress-bar navigation.
 */
class DDProgressSlider {
    
    /**
     * Constructor.
     * * @param {HTMLElement} wrapper The main widget container.
     */
    constructor(wrapper) {
        this.wrapper = wrapper;
        this.container = this.wrapper.querySelector('.dd-swiper-container');
        this.navItems = this.wrapper.querySelectorAll('.dd-nav-item');
        
        if (!this.container || this.navItems.length === 0) return;

        // Parse options passed from PHP
        const rawOptions = this.wrapper.getAttribute('data-dd-options');
        this.options = rawOptions ? JSON.parse(rawOptions) : { autoplay_delay: 5000, speed: 500 };
        
        this.initSwiper();
        this.bindEvents();
    }

    /**
     * Initializes the Swiper instance.
     * Hardcodes slidesPerView to 1 as per requirements.
     */
    initSwiper() {
        const SwiperClass = typeof Swiper !== 'undefined' ? Swiper : elementorFrontend.utils.swiper;

        this.swiper = new SwiperClass(this.container, {
            slidesPerView: 1, // Fixed to 1 slide per view
            speed: this.options.speed,
            effect: 'fade', // Optional: 'slide' or 'fade' usually looks best for hero sections
            autoplay: {
                delay: this.options.autoplay_delay,
                disableOnInteraction: false,
            },
            on: {
                // Synchronize the progress bar animation using Swiper's internal timer
                autoplayTimeLeft: (s, time, progress) => this.handleProgress(s, progress),
                slideChange: (s) => this.handleSlideChange(s)
            }
        });
    }

    /**
     * Updates the progress bar width for the active navigation item.
     * * @param {Object} swiperInstance Current Swiper instance.
     * @param {number} progress Float representing remaining time (1 to 0).
     */
    handleProgress(swiperInstance, progress) {
        const activeIndex = swiperInstance.realIndex;
        
        this.navItems.forEach((item, index) => {
            const fill = item.querySelector('.dd-nav-progress-fill');
            if (index === activeIndex) {
                // Invert progress calculation so it fills from 0% to 100%
                const fillPercentage = (1 - progress) * 100;
                fill.style.width = `${fillPercentage}%`;
                item.classList.add('is-active');
            } else {
                fill.style.width = '0%';
                item.classList.remove('is-active');
            }
        });
    }

    /**
     * Resets visual states when a slide change occurs manually or automatically.
     * * @param {Object} swiperInstance Current Swiper instance.
     */
    handleSlideChange(swiperInstance) {
        const activeIndex = swiperInstance.realIndex;
        this.navItems.forEach((item, index) => {
            if (index !== activeIndex) {
                item.querySelector('.dd-nav-progress-fill').style.width = '0%';
                item.classList.remove('is-active');
            }
        });
    }

    /**
     * Binds click events to the custom navigation items to jump to specific slides.
     */
    bindEvents() {
        this.navItems.forEach(item => {
            item.addEventListener('click', (e) => {
                const index = parseInt(e.currentTarget.getAttribute('data-index'), 10);
                if (this.swiper) {
                    this.swiper.slideToLoop(index);
                }
            });
        });
    }
}

// Initialize on Elementor frontend loaded hook
window.addEventListener('elementor/frontend/init', () => {
    elementorFrontend.hooks.addAction('frontend/element_ready/dd_progress_slider.default', function($scope) {
        new DDProgressSlider($scope[0]);
    });
});