import { useEffect, useState } from "react";
import { Link, useNavigate } from "react-router-dom";
import { cartService, type CheckoutData } from "@/services/cart";
import { Loader2 } from "lucide-react";
import { Button } from "@/components/ui/button";

export default function PanelCheckout() {
  const navigate = useNavigate();
  const [status, setStatus] = useState<"loading" | "error">("loading");
  const [errorMessage, setErrorMessage] = useState<string>("");

  useEffect(() => {
    let cancelled = false;
    cartService
      .checkout()
      .then((data: CheckoutData) => {
        if (cancelled) return;
        if (data?.order?.id) {
          navigate("/panel/checkout/payment", { state: data, replace: true });
        } else {
          setStatus("error");
          setErrorMessage("Could not create order.");
        }
      })
      .catch((err) => {
        if (cancelled) return;
        setStatus("error");
        setErrorMessage(err?.message ?? "Checkout failed.");
      });
    return () => { cancelled = true; };
  }, [navigate]);

  return (
    <>
      <div className="mb-8">
        <Link to="/panel/cart" className="text-sm text-muted-foreground hover:text-foreground">
          ← Back to cart
        </Link>
      </div>
      <div className="flex flex-col items-center justify-center py-16 text-center">
        {status === "loading" && (
          <>
            <Loader2 className="h-12 w-12 animate-spin text-primary mb-4" />
            <p className="text-muted-foreground">Creating order…</p>
          </>
        )}
        {status === "error" && (
          <>
            <p className="text-destructive mb-4">{errorMessage}</p>
            <Link to="/panel/cart">
              <Button variant="outline">Back to cart</Button>
            </Link>
          </>
        )}
      </div>
    </>
  );
}
