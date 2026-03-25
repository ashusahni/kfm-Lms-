import { clsx, type ClassValue } from "clsx";
import { twMerge } from "tailwind-merge";

export function cn(...inputs: ClassValue[]) {
  return twMerge(clsx(inputs));
}

/**
 * Converts HTML snippet to plain text: strips tags and decodes entities
 * so strings like "<p>You got 165&nbsp;points</p>" become "You got 165 points".
 */
export function htmlToPlainText(html: string): string {
  if (typeof html !== "string" || !html.trim()) return html;
  let text = html
    .replace(/&nbsp;/g, "\u00A0")
    .replace(/&amp;/g, "&")
    .replace(/&lt;/g, "<")
    .replace(/&gt;/g, ">")
    .replace(/&quot;/g, '"')
    .replace(/&#39;/g, "'")
    .replace(/&apos;/g, "'")
    .replace(/&#(\d+);/g, (_, code) => String.fromCharCode(parseInt(code, 10)))
    .replace(/&#x([0-9a-fA-F]+);/g, (_, code) => String.fromCharCode(parseInt(code, 16)));
  text = text.replace(/<[^>]*>/g, " ").replace(/\s+/g, " ").trim();
  return text;
}
