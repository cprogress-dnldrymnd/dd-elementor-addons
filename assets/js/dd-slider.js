/**
 * DD Progress Slider Initialization
 * Handles the instantiation of Swiper and synchronization with the custom progress-bar navigation.
 */
class DDProgressSlider {

    /**
     * Constructor.
     * @param {HTMLElement} wrapper The main widget container.
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
      * Integrates loop capability, safely mitigates async dependencies for Elementor 3.x+,
      * and enables crossFade to prevent visual overlapping on transparent slides.
      */
    initSwiper() {
        const SwiperClass = typeof Swiper !== 'undefined' ? Swiper : elementorFrontend.utils.swiper;

        const swiperConfig = {
            slidesPerView: 1,
            speed: this.options.speed,
            effect: 'fade',
            fadeEffect: {
                crossFade: true // <-- Crucial: Forces the outgoing slide to fade to opacity 0
            },
            loop: true,
            autoplay: {
                delay: this.options.autoplay_delay,
                disableOnInteraction: false,
            },
            on: {
                // Trigger animation when the slide changes
                slideChange: (s) => this.animateProgress(s.realIndex)
            }
        };

        // Handle Elementor 3.x+ async Swiper loading
        if (typeof Swiper === 'undefined' && typeof elementorFrontend.utils.swiper !== 'undefined') {
            new SwiperClass(this.container, swiperConfig).then((instance) => {
                this.swiper = instance;
                // Initialize the first progress bar immediately
                this.animateProgress(this.swiper.realIndex);
            });
        } else {
            this.swiper = new SwiperClass(this.container, swiperConfig);
            // Initialize the first progress bar immediately
            this.animateProgress(this.swiper.realIndex);
        }
    }

    /**
     * Animates the progress bar width using native CSS transitions for broad compatibility.
     * @param {number} activeIndex The current active slide index.
     */
    animateProgress(activeIndex) {
        this.navItems.forEach((item, index) => {
            const fill = item.querySelector('.dd-nav-progress-fill');
            if (!fill) return;

            if (index === activeIndex) {
                item.classList.add('is-active');

                // 1. Reset the bar instantly to 0% (no transition)
                fill.style.transition = 'none';
                fill.style.width = '0%';

                // 2. Force a browser reflow so the DOM registers the reset before the animation begins
                void fill.offsetWidth;

                // 3. Apply the CSS transition matching the autoplay duration and fill to 100%
                fill.style.transition = `width ${this.options.autoplay_delay}ms linear`;
                fill.style.width = '100%';
            } else {
                // Reset inactive items
                item.classList.remove('is-active');
                fill.style.transition = 'none';
                fill.style.width = '0%';
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
    elementorFrontend.hooks.addAction('frontend/element_ready/dd_progress_slider.default', function ($scope) {
        new DDProgressSlider($scope[0]);
    });
});