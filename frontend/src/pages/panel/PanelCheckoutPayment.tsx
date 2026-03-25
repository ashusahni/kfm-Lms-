import { useState, useCallback } from "react";
import { Link, useLocation, useNavigate } from "react-router-dom";
import { cartService, type CheckoutData } from "@/services/cart";
import { Loader2, CreditCard, Wallet, ArrowLeft, Shield, ChevronRight } from "lucide-react";
import { Button } from "@/components/ui/button";
import { Card, CardContent, CardHeader, CardTitle } from "@/components/ui/card";

declare global {
  interface Window {
    Razorpay: new (options: {
      key: string;
      amount: number;
      currency: string;
      order_id: string;
      name?: string;
      description?: string;
      handler: (res: { razorpay_payment_id: string; razorpay_order_id: string; razorpay_signature: string }) => void;
    }) => { open: () => void };
  }
}

export default function PanelCheckoutPayment() {
  const location = useLocation();
  const navigate = useNavigate();
  const state = location.state as CheckoutData | null;
  const order = state?.order;
  const orderId = order?.id;
  const total = state?.amounts?.total ?? (order as { total_amount?: number } | undefined)?.total_amount ?? 0;
  const userCharge = state?.userCharge ?? 0;
  const count = state?.count ?? 0;
  const canPayByBalance = userCharge >= total && total > 0;
  const razorpayAvailable = state?.razorpay === true;

  const [paying, setPaying] = useState<"idle" | "razorpay" | "credit" | "error">("idle");
  const [errorMessage, setErrorMessage] = useState<string>("");

  const payByCredit = useCallback(() => {
    if (!orderId) return;
    setPaying("credit");
    setErrorMessage("");
    cartService
      .payByCredit(orderId)
      .then(() => {
        navigate("/panel/cart/success?order_id=" + orderId, { replace: true });
      })
      .catch((err) => {
        setPaying("error");
        setErrorMessage(err?.message ?? "Payment failed.");
      });
  }, [orderId, navigate]);

  const payByRazorpay = useCallback(() => {
    if (!orderId) return;
    setPaying("razorpay");
    setErrorMessage("");
    cartService
      .createRazorpayOrder(orderId)
      .then((data) => {
        const verifyUrl = data.verify_url;
        const options = {
          key: data.key,
          amount: data.amount,
          currency: data.currency,
          order_id: data.razorpay_order_id,
          name: "Rocket LMS",
          description: "Order #" + data.order_id,
          handler: (res: { razorpay_payment_id: string; razorpay_order_id: string; razorpay_signature: string }) => {
            const url =
              verifyUrl +
              (verifyUrl.includes("?") ? "&" : "?") +
              "razorpay_payment_id=" +
              encodeURIComponent(res.razorpay_payment_id) +
              "&razorpay_order_id=" +
              encodeURIComponent(res.razorpay_order_id) +
              "&razorpay_signature=" +
              encodeURIComponent(res.razorpay_signature);
            window.location.href = url;
          },
        };
        if (typeof window.Razorpay !== "undefined") {
          const rzp = new window.Razorpay(options);
          rzp.open();
          setPaying("idle");
        } else {
          const script = document.createElement("script");
          script.src = "https://checkout.razorpay.com/v1/checkout.js";
          script.async = true;
          script.onload = () => {
            const rzp = new window.Razorpay(options);
            rzp.open();
            setPaying("idle");
          };
          script.onerror = () => {
            setPaying("error");
            setErrorMessage("Could not load Razorpay.");
          };
          document.body.appendChild(script);
        }
      })
      .catch((err) => {
        setPaying("error");
        setErrorMessage(err?.message ?? "Could not start Razorpay.");
      });
  }, [orderId]);

  if (!state || !orderId) {
    return (
      <div className="min-h-[50vh] flex flex-col items-center justify-center text-center px-4">
        <p className="text-muted-foreground mb-6">No order found. Start from the cart.</p>
        <Link to="/panel/cart">
          <Button variant="outline" className="gap-2">
            <ArrowLeft className="h-4 w-4" />
            Back to cart
          </Button>
        </Link>
      </div>
    );
  }

  const totalFormatted = typeof total === "number" ? total.toFixed(2) : String(total);

  return (
    <div className="max-w-4xl mx-auto">
      {/* Breadcrumb / back */}
      <Link
        to="/panel/cart"
        className="inline-flex items-center gap-2 text-sm text-muted-foreground hover:text-foreground transition-colors mb-8"
      >
        <ArrowLeft className="h-4 w-4" />
        Back to cart
      </Link>

      {/* Step indicator */}
      <div className="flex items-center gap-2 text-sm text-muted-foreground mb-8">
        <span className="font-medium text-foreground">Cart</span>
        <ChevronRight className="h-4 w-4" />
        <span className="font-medium text-foreground">Payment</span>
      </div>

      <h1 className="text-2xl md:text-3xl font-display font-bold text-foreground mb-8">
        Choose payment method
      </h1>

      {errorMessage && (
        <div className="mb-6 p-4 rounded-xl bg-destructive/10 border border-destructive/20 text-destructive text-sm">
          {errorMessage}
        </div>
      )}

      <div className="grid md:grid-cols-[1fr,380px] gap-8">
        {/* Payment methods */}
        <div className="space-y-4">
          {razorpayAvailable && (
            <button
              type="button"
              onClick={payByRazorpay}
              disabled={paying !== "idle"}
              className="w-full text-left p-6 rounded-2xl border-2 border-border bg-card hover:border-primary/50 hover:shadow-card transition-all duration-200 focus:outline-none focus:ring-2 focus:ring-primary/20 focus:border-primary disabled:opacity-60 disabled:pointer-events-none"
            >
              <div className="flex items-start gap-4">
                <div className="flex-shrink-0 w-12 h-12 rounded-xl bg-gradient-to-br from-emerald-500 to-teal-600 flex items-center justify-center text-white shadow-lg">
                  <CreditCard className="h-6 w-6" />
                </div>
                <div className="flex-1 min-w-0">
                  <h3 className="font-semibold text-foreground text-lg mb-1">
                    Pay with Razorpay
                  </h3>
                  <p className="text-sm text-muted-foreground">
                    Card, UPI, Net Banking, Wallets — secure payment by Razorpay
                  </p>
                  {paying === "razorpay" && (
                    <div className="mt-3 flex items-center gap-2 text-sm text-primary">
                      <Loader2 className="h-4 w-4 animate-spin" />
                      Opening payment…
                    </div>
                  )}
                </div>
                <ChevronRight className="h-5 w-5 text-muted-foreground flex-shrink-0 mt-1" />
              </div>
            </button>
          )}

          {canPayByBalance && (
            <button
              type="button"
              onClick={payByCredit}
              disabled={paying !== "idle"}
              className="w-full text-left p-6 rounded-2xl border-2 border-border bg-card hover:border-primary/50 hover:shadow-card transition-all duration-200 focus:outline-none focus:ring-2 focus:ring-primary/20 focus:border-primary disabled:opacity-60 disabled:pointer-events-none"
            >
              <div className="flex items-start gap-4">
                <div className="flex-shrink-0 w-12 h-12 rounded-xl bg-muted flex items-center justify-center text-muted-foreground">
                  <Wallet className="h-6 w-6" />
                </div>
                <div className="flex-1 min-w-0">
                  <h3 className="font-semibold text-foreground text-lg mb-1">
                    Pay with wallet balance
                  </h3>
                  <p className="text-sm text-muted-foreground">
                    Use your account balance — {userCharge.toFixed(2)} available
                  </p>
                  {paying === "credit" && (
                    <div className="mt-3 flex items-center gap-2 text-sm text-primary">
                      <Loader2 className="h-4 w-4 animate-spin" />
                      Processing…
                    </div>
                  )}
                </div>
                <ChevronRight className="h-5 w-5 text-muted-foreground flex-shrink-0 mt-1" />
              </div>
            </button>
          )}

          {!razorpayAvailable && !canPayByBalance && (
            <Card className="border-dashed">
              <CardContent className="py-10 text-center">
                <p className="text-muted-foreground">No payment option available right now. Contact support.</p>
              </CardContent>
            </Card>
          )}
        </div>

        {/* Order summary sidebar */}
        <div className="md:sticky md:top-6 h-fit">
          <Card className="rounded-2xl border shadow-card overflow-hidden">
            <CardHeader className="pb-3">
              <CardTitle className="text-base font-medium text-muted-foreground">
                Order summary
              </CardTitle>
            </CardHeader>
            <CardContent className="space-y-4">
              <div className="flex justify-between text-sm">
                <span className="text-muted-foreground">Order #</span>
                <span className="font-mono font-medium">{orderId}</span>
              </div>
              {count > 0 && (
                <div className="flex justify-between text-sm">
                  <span className="text-muted-foreground">Items</span>
                  <span>{count}</span>
                </div>
              )}
              <div className="border-t pt-4 flex justify-between items-baseline">
                <span className="font-semibold text-foreground">Total</span>
                <span className="text-2xl font-bold text-foreground">{totalFormatted}</span>
              </div>
              <div className="flex items-center gap-2 pt-2 text-xs text-muted-foreground">
                <Shield className="h-3.5 w-3.5 flex-shrink-0" />
                <span>Secure payment. Your data is protected.</span>
              </div>
            </CardContent>
          </Card>
        </div>
      </div>
    </div>
  );
}
