/**
 * Format a program/course price for display.
 * Uses price_string from API when available (backend-formatted with admin currency).
 * Otherwise formats using config currency (sign, position, decimals).
 */
export interface CurrencyConfig {
  sign?: string;
  name?: string;
}

export interface FormatPriceOptions {
  /** Pre-formatted price string from API (preferred) */
  priceString?: string | null;
  /** Raw price number (used when priceString not available) */
  price?: number | null;
  /** Currency sign from config ($, ₹, etc.) */
  currencySign?: string;
  /** Position: left, right, left_with_space, right_with_space */
  currencyPosition?: string;
  /** Number of decimal places */
  decimals?: number;
  /** Label for free items */
  freeLabel?: string;
}

const DEFAULT_SIGN = "$";
const DEFAULT_POSITION = "left";

export function formatProgramPrice(options: FormatPriceOptions): string {
  const {
    priceString,
    price,
    currencySign = DEFAULT_SIGN,
    currencyPosition = DEFAULT_POSITION,
    decimals = 0,
    freeLabel = "Free",
  } = options;

  // Prefer API-formatted string (backend uses admin currency)
  if (priceString && priceString.trim()) {
    return priceString;
  }

  if (price == null || price <= 0) {
    return freeLabel;
  }

  const formatted = Number(price).toLocaleString("en-IN", {
    minimumFractionDigits: decimals,
    maximumFractionDigits: decimals,
  });

  switch (currencyPosition) {
    case "right":
      return `${formatted}${currencySign}`;
    case "right_with_space":
      return `${formatted} ${currencySign}`;
    case "left_with_space":
      return `${currencySign} ${formatted}`;
    case "left":
    default:
      return `${currencySign}${formatted}`;
  }
}
