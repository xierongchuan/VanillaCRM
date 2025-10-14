/**
 * VanillaCRM - Helper Functions
 *
 * This file provides utility functions for:
 * - CSRF token management
 * - Fetch API wrapper with automatic CSRF injection
 * - Date formatting
 * - Common UI utilities
 */

/**
 * Get CSRF token from meta tag
 * @returns {string} CSRF token or empty string if not found
 */
function csrfToken() {
  const el = document.querySelector('meta[name="csrf-token"]');
  return el ? el.getAttribute('content') : '';
}

/**
 * Wrapper for fetch API with CSRF token and error handling
 * Automatically adds required headers for Laravel backend
 *
 * @param {string} url - The URL to fetch
 * @param {object} options - Fetch options (method, body, headers, etc.)
 * @returns {Promise<any>} Parsed JSON response
 * @throws {Error} HTTP error with status and message
 */
async function apiFetch(url, options = {}) {
  const token = csrfToken();

  // Merge default headers with custom headers
  const headers = Object.assign({
    'X-Requested-With': 'XMLHttpRequest',
    'X-CSRF-TOKEN': token,
    'Accept': 'application/json',
    'Content-Type': 'application/json'
  }, options.headers || {});

  // Merge default config with custom options
  const config = Object.assign({
    credentials: 'same-origin',
    headers
  }, options);

  try {
    const res = await fetch(url, config);

    // Check if response is OK
    if (!res.ok) {
      let errorMessage = `HTTP ${res.status}`;
      try {
        const errorData = await res.json();
        errorMessage += `: ${errorData.message || JSON.stringify(errorData)}`;
      } catch {
        const text = await res.text();
        errorMessage += `: ${text}`;
      }
      throw new Error(errorMessage);
    }

    // Parse JSON response
    const contentType = res.headers.get('content-type');
    if (contentType && contentType.includes('application/json')) {
      return await res.json();
    }

    // Return text if not JSON
    return await res.text();
  } catch (error) {
    console.error('API Fetch Error:', error);
    throw error;
  }
}

/**
 * Format date string for display
 *
 * @param {string|Date} dateString - Date to format
 * @param {string} format - Format string ('dd.mm.yyyy', 'dd.mm.yyyy HH:ii:ss')
 * @returns {string} Formatted date string
 */
function formatDate(dateString, format = 'dd.mm.yyyy') {
  if (!dateString) return '';

  const date = new Date(dateString);

  // Check if date is valid
  if (isNaN(date.getTime())) {
    return dateString;
  }

  const day = String(date.getDate()).padStart(2, '0');
  const month = String(date.getMonth() + 1).padStart(2, '0');
  const year = date.getFullYear();
  const hours = String(date.getHours()).padStart(2, '0');
  const minutes = String(date.getMinutes()).padStart(2, '0');
  const seconds = String(date.getSeconds()).padStart(2, '0');

  switch (format) {
    case 'dd.mm.yyyy':
      return `${day}.${month}.${year}`;
    case 'dd.mm.yyyy HH:ii:ss':
      return `${day}.${month}.${year} ${hours}:${minutes}:${seconds}`;
    case 'yyyy-mm-dd':
      return `${year}-${month}-${day}`;
    default:
      return dateString;
  }
}

/**
 * Format number with thousands separator
 *
 * @param {number} num - Number to format
 * @param {number} decimals - Number of decimal places
 * @returns {string} Formatted number
 */
function formatNumber(num, decimals = 0) {
  if (typeof num !== 'number' && typeof num !== 'string') {
    return num;
  }

  const n = parseFloat(num);
  if (isNaN(n)) return num;

  return n.toLocaleString('ru-RU', {
    minimumFractionDigits: decimals,
    maximumFractionDigits: decimals
  });
}

/**
 * Debounce function to limit function calls
 *
 * @param {Function} func - Function to debounce
 * @param {number} wait - Wait time in milliseconds
 * @returns {Function} Debounced function
 */
function debounce(func, wait = 300) {
  let timeout;
  return function executedFunction(...args) {
    const later = () => {
      clearTimeout(timeout);
      func(...args);
    };
    clearTimeout(timeout);
    timeout = setTimeout(later, wait);
  };
}

/**
 * Show confirmation dialog
 *
 * @param {string} message - Confirmation message
 * @returns {boolean} User confirmation result
 */
function confirmAction(message) {
  return confirm(message);
}

/**
 * Generate Laravel route URL (basic implementation)
 * Note: This is a simple version. For complex routes with parameters,
 * consider using a more robust solution like Ziggy.
 *
 * @param {string} routeName - Route name
 * @param {object} params - Route parameters
 * @returns {string} Route URL
 */
function route(routeName, params = {}) {
  // This is a placeholder. In production, you might want to:
  // 1. Use Ziggy package for Laravel route generation in JS
  // 2. Or define a routes map from backend
  const routes = {
    'home.index': '/',
    'auth.sign_in': '/sign_in',
    'auth.logout': '/logout',
    'company.list': '/company',
    'admin.index': '/admin',
    'stat.index': '/stat',
    'user.permission': '/user/permission',
    'theme.switch': (theme) => `/theme/${theme}`
  };

  if (typeof routes[routeName] === 'function') {
    return routes[routeName](params);
  }

  return routes[routeName] || '#';
}

// Export functions for use in modules (if needed)
if (typeof module !== 'undefined' && module.exports) {
  module.exports = {
    csrfToken,
    apiFetch,
    formatDate,
    formatNumber,
    debounce,
    confirmAction,
    route
  };
}

// Make functions available globally
window.csrfToken = csrfToken;
window.apiFetch = apiFetch;
window.formatDate = formatDate;
window.formatNumber = formatNumber;
window.debounce = debounce;
window.confirmAction = confirmAction;
window.route = route;
