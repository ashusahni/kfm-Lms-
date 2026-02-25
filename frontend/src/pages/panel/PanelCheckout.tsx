import { useEffect, useState } from "react";
import { Link } from "react-router-dom";
import { cartService } from "@/services/cart";
import { Loader2 } from "lucide-react";
import { Button } from "@/components/ui/button";

export default function PanelCheckout() {
  const [status, setStatus] = useState<"loading" | "redirect" | "error">("loading");
  const [errorMessage, setErrorMessage] = useState<string>("");

  useEffect(() => {
    let cancelled = false;
    cartService
      .webCheckout()
      .then((res) => {
        if (cancelled) return;
        const url = (res && typeof res === "object" && "url" in res && res.url) || (res && typeof res === "object" && "link" in res && (res as { link?: string }).link);
        if (url) {
          setStatus("redirect");
          window.location.href = url;
        } else {
          setStatus("error");
          setErrorMessage("No payment URL returned.");
        }
      })
      .catch((err) => {
        if (cancelled) return;
        setStatus("error");
        setErrorMessage(err?.message ?? "Checkout failed.");
      });
    return () => { cancelled = true; };
  }, []);

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
            <p className="text-muted-foreground">Redirecting to payment…</p>
          </>
        )}
        {status === "redirect" && (
          <>
            <Loader2 className="h-12 w-12 animate-spin text-primary mb-4" />
            <p className="text-muted-foreground">Redirecting to payment…</p>
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
