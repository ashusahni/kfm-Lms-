import { Link } from "react-router-dom";
import { useQuery } from "@tanstack/react-query";
import { api } from "@/lib/api";
import { paths } from "@/constants/api-paths";
import { ShoppingCart } from "lucide-react";
import { Button } from "@/components/ui/button";
import { Card, CardContent, CardHeader, CardTitle } from "@/components/ui/card";

export default function PanelCart() {
  const { data: cart, isLoading } = useQuery({
    queryKey: ["panel-cart"],
    queryFn: () => api.get<{ cart?: { items?: unknown[]; total?: number }; items?: unknown[] }>(paths.panel.cart.list),
  });

  const rawItems =
    cart && typeof cart === "object" && "cart" in cart && (cart as { cart?: { items?: unknown[] } }).cart?.items;
  const items = Array.isArray(rawItems)
    ? rawItems
    : (cart && typeof cart === "object" && "items" in cart && Array.isArray((cart as { items: unknown[] }).items))
      ? (cart as { items: unknown[] }).items
      : Array.isArray(cart)
        ? cart
        : [];

  return (
    <>
      <div className="mb-8 flex items-center gap-4">
        <Link to="/panel" className="text-sm text-muted-foreground hover:text-foreground">
          ← Dashboard
        </Link>
      </div>
      <h1 className="text-2xl font-display font-bold text-foreground mb-8">
        Cart
      </h1>

      {isLoading && (
        <div className="space-y-4">
          <div className="h-24 bg-muted rounded-xl animate-pulse" />
          <div className="h-24 bg-muted rounded-xl animate-pulse" />
        </div>
      )}

      {!isLoading && items.length === 0 && (
        <Card className="border border-border">
          <CardContent className="py-16 text-center">
            <ShoppingCart className="mx-auto h-16 w-16 text-muted-foreground mb-4" />
            <p className="text-muted-foreground mb-4">Your cart is empty.</p>
            <Link to="/programs">
              <Button className="bg-gradient-cta">Browse programs</Button>
            </Link>
          </CardContent>
        </Card>
      )}

      {!isLoading && items.length > 0 && (
        <Card className="border border-border">
          <CardHeader>
            <CardTitle className="text-lg">Items ({items.length})</CardTitle>
          </CardHeader>
          <CardContent>
            <ul className="space-y-4">
              {items.map((item: { id?: number; title?: string; webinar_id?: number }, i: number) => (
                <li key={item.id ?? i} className="flex items-center justify-between py-3 border-b border-border">
                  <span className="font-medium">{item.title ?? `Program #${item.webinar_id ?? item.id}`}</span>
                  <Link to={`/programs/${item.webinar_id ?? item.id}`} className="text-sm text-primary hover:underline">
                    View
                  </Link>
                </li>
              ))}
            </ul>
            <div className="mt-6 flex flex-wrap gap-3">
              <Link to="/programs">
                <Button variant="outline">Continue browsing</Button>
              </Link>
              <Button className="bg-gradient-cta" asChild>
                <Link to="/panel/checkout">Proceed to checkout</Link>
              </Button>
            </div>
          </CardContent>
        </Card>
      )}
    </>
  );
}
