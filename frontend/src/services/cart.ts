/**
 * Cart & checkout API – add to cart, list, checkout, payment (Razorpay / balance).
 */
import { api } from "@/lib/api";
import { paths } from "@/constants/api-paths";

export type CheckoutData = {
  order?: { id: number; total_amount?: number; [k: string]: unknown };
  paymentChannels?: { id: number; title: string; class_name: string; [k: string]: unknown }[];
  userCharge?: number;
  razorpay?: boolean;
  amounts?: { total?: number; sub_total?: number; total_discount?: number; tax?: number; tax_price?: number };
  count?: number;
};

export const cartService = {
  list: () => api.get<unknown>(paths.panel.cart.list),

  add: (webinarId: number | string, ticketId?: number, batchId?: number | null) => {
    const body: Record<string, unknown> = {
      webinar_id: typeof webinarId === "string" ? parseInt(webinarId, 10) : webinarId,
    };
    if (ticketId != null) body.ticket_id = ticketId;
    if (batchId != null) body.batch_id = batchId;
    return api.post<unknown>(paths.panel.cart.store, body);
  },

  remove: (id: number | string) =>
    api.delete(paths.panel.cart.delete(id)),

  /** Create order and return checkout data for payment page (React flow). */
  checkout: (body?: { discount_id?: number | string }) =>
    api.post<CheckoutData>(paths.panel.cart.checkout, body),

  webCheckout: (body?: { coupon?: string }) =>
    api.post<{ url?: string; link?: string }>(paths.panel.cart.webCheckout, body),

  validateCoupon: (coupon: string) =>
    api.post<unknown>(paths.panel.cart.validateCoupon, { coupon }),

  /** Pay with account balance. */
  payByCredit: (orderId: number) =>
    api.post<unknown>(paths.panel.payments.credit, { order_id: orderId }),

  /** Get Razorpay order data to open Checkout in frontend. */
  createRazorpayOrder: (orderId: number) =>
    api.post<{ razorpay_order_id: string; amount: number; currency: string; key: string; order_id: number; verify_url: string }>(
      paths.panel.payments.razorpayOrder,
      { order_id: orderId }
    ),
};
