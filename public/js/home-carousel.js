/**
 * Home Page Carousel Logic (Vue 3)
 *
 * Replaces Slick Carousel with Vue-based carousel for company reports
 * Works with existing Blade-generated HTML structure
 * Minimal DOM manipulation required
 */

document.addEventListener('DOMContentLoaded', function() {
  // Check if we're on the home page
  const homeCarouselElements = document.querySelectorAll('[data-carousel-company]');

  if (homeCarouselElements.length === 0) {
    // Not on home page, skip initialization
    return;
  }

  console.log('Initializing home carousel for', homeCarouselElements.length, 'companies');

  // Initialize carousel state for each company
  const carouselStates = new Map();

  homeCarouselElements.forEach(element => {
    const companyId = element.getAttribute('data-carousel-company');
    const reportPanels = element.querySelectorAll('.perm-panel');

    if (reportPanels.length === 0) {
      console.warn(`No report panels found for company ${companyId}`);
      return;
    }

    // Initialize state for this company
    carouselStates.set(companyId, {
      currentSlide: 0,
      totalSlides: reportPanels.length,
      panels: reportPanels,
      panelContainer: element
    });

    // Show first slide, hide others
    reportPanels.forEach((panel, index) => {
      panel.style.display = index === 0 ? 'block' : 'none';
      panel.classList.remove('collapse', 'show'); // Remove Bootstrap collapse classes that interfere
    });

    console.log(`Company ${companyId}: ${reportPanels.length} report panels initialized`);
  });

  // Function to navigate to specific slide
  function goToSlide(companyId, slideIndex) {
    const state = carouselStates.get(companyId);
    if (!state) return;

    // Hide all panels
    state.panels.forEach(panel => {
      panel.style.display = 'none';
    });

    // Show target panel with fade animation
    const targetPanel = state.panels[slideIndex];
    if (targetPanel) {
      targetPanel.style.opacity = '0';
      targetPanel.style.display = 'block';

      // Trigger reflow
      targetPanel.offsetHeight;

      // Fade in
      targetPanel.style.transition = 'opacity 0.3s ease-in-out';
      targetPanel.style.opacity = '1';
    }

    // Update state
    state.currentSlide = slideIndex;

    console.log(`Company ${companyId}: navigated to slide ${slideIndex + 1}/${state.totalSlides}`);
  }

  // Function to go to next slide
  function nextSlide(companyId) {
    const state = carouselStates.get(companyId);
    if (!state) return;

    const nextIndex = (state.currentSlide + 1) % state.totalSlides;
    goToSlide(companyId, nextIndex);
  }

  // Function to go to previous slide
  function prevSlide(companyId) {
    const state = carouselStates.get(companyId);
    if (!state) return;

    const prevIndex = (state.currentSlide - 1 + state.totalSlides) % state.totalSlides;
    goToSlide(companyId, prevIndex);
  }

  // Function to toggle panel visibility
  function togglePanel(companyId) {
    const state = carouselStates.get(companyId);
    if (!state) return;

    const container = state.panelContainer;
    const isVisible = container.style.display !== 'none';

    if (isVisible) {
      container.style.display = 'none';
    } else {
      container.style.display = 'block';
    }

    console.log(`Company ${companyId}: panel ${isVisible ? 'hidden' : 'shown'}`);
  }

  // Attach event listeners to buttons
  document.querySelectorAll('.slider_next_button').forEach(button => {
    button.addEventListener('click', function(e) {
      e.preventDefault();
      const companyId = this.getAttribute('section-id');
      if (companyId) {
        nextSlide(companyId);
      }
    });
  });

  document.querySelectorAll('.slider_prev_button').forEach(button => {
    button.addEventListener('click', function(e) {
      e.preventDefault();
      const companyId = this.getAttribute('section-id');
      if (companyId) {
        prevSlide(companyId);
      }
    });
  });

  document.querySelectorAll('.perm_panel_switch').forEach(button => {
    button.addEventListener('click', function(e) {
      e.preventDefault();
      const panelId = this.getAttribute('panel');
      if (panelId) {
        // Extract company ID from panel ID (format: perm_panel_123)
        const companyId = panelId.replace('perm_panel_', '');
        togglePanel(companyId);
      }
    });
  });

  console.log('Home carousel initialization complete');
});
