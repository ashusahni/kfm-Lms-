/**
 * Cart & checkout API – add to cart, list, checkout (subscription / one-time).
 */
import { api } from "@/lib/api";
import { paths } from "@/constants/api-paths";

export const cartService = {
  list: () => api.get<unknown>(paths.panel.cart.list),

  add: (webinarId: number | string, ticketId?: number) =>
    api.post<unknown>(paths.panel.cart.store, { webinar_id: webinarId, ticket_id: ticketId }),

  remove: (id: number | string) =>
    api.delete(paths.panel.cart.delete(id)),

  checkout: (body?: { coupon?: string }) =>
    api.post<unknown>(paths.panel.cart.checkout, body),

  webCheckout: (body?: { coupon?: string }) =>
    api.post<{ url?: string; link?: string }>(paths.panel.cart.webCheckout, body),

  validateCoupon: (coupon: string) =>
    api.post<unknown>(paths.panel.cart.validateCoupon, { coupon }),
};
