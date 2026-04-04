/**
 * Global Currency Formatter Utility
 * Ensures consistent RWF currency formatting across the app
 * Format: "RWF 102,000.00"
 */

/**
 * Format amount as RWF currency with proper number formatting
 * @param {number} amount - The amount to format
 * @param {number} decimals - Number of decimal places (default: 2)
 * @returns {string} Formatted string like "RWF 102,000.00"
 */
function formatCurrencyRWF(amount, decimals = 2) {
    if (typeof amount !== 'number' || isNaN(amount)) {
        return 'RWF 0.00';
    }

    // Parse and ensure valid number
    const num = parseFloat(amount);
    
    // Format with comma separators and specified decimals
    const formatted = new Intl.NumberFormat('en-US', {
        minimumFractionDigits: decimals,
        maximumFractionDigits: decimals,
        useGrouping: true
    }).format(num);

    return `RWF ${formatted}`;
}

/**
 * Format amount as RWF with integer only (no decimals)
 * @param {number} amount - The amount to format
 * @returns {string} Formatted string like "RWF 102,000"
 */
function formatCurrencyRWFInteger(amount) {
    return formatCurrencyRWF(amount, 0);
}

/**
 * Legacy wrapper for backwards compatibility
 * Use formatCurrencyRWF() for new code
 */
function formatCurrency(amount) {
    return formatCurrencyRWF(amount, 0);
}

// Export for use in modules if needed
if (typeof module !== 'undefined' && module.exports) {
    module.exports = { formatCurrencyRWF, formatCurrencyRWFInteger, formatCurrency };
}
