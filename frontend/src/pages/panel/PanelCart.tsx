import { useState } from "react";
import { Link } from "react-router-dom";
import { useQuery, useMutation, useQueryClient } from "@tanstack/react-query";
import { api } from "@/lib/api";
import { cartService } from "@/services/cart";
import { paths } from "@/constants/api-paths";
import { useConfig } from "@/context/ConfigContext";
import { ShoppingCart, Trash2, ChevronRight, BookOpen } from "lucide-react";
import { Button } from "@/components/ui/button";
import { Card, CardContent, CardHeader, CardTitle } from "@/components/ui/card";
import { motion, AnimatePresence } from "framer-motion";

type CartItem = {
  id: number;
  type?: string;
  image?: string | null;
  title?: string | null;
  teacher_name?: string | null;
  price?: number;
  discount?: number;
  batch?: { id: number; name?: string; code?: string };
  batch_id?: number;
  webinar_id?: number;
};

type CartAmounts = {
  sub_total?: number;
  total_discount?: number;
  tax_price?: number;
  total?: number;
};

type CartResponse = {
  cart?: {
    items?: CartItem[];
    amounts?: CartAmounts;
  };
  items?: CartItem[];
};

function parseCartResponse(cart: unknown): { items: CartItem[]; amounts?: CartAmounts } {
  const data = cart as CartResponse | undefined;
  const cartObj = data?.cart ?? data;
  const rawItems = (cartObj && typeof cartObj === "object" && "items" in cartObj && cartObj.items) ?? [];
  const items = Array.isArray(rawItems) ? rawItems : [];
  const amounts = cartObj && typeof cartObj === "object" && "amounts" in cartObj ? (cartObj as { amounts?: CartAmounts }).amounts : undefined;
  return { items, amounts };
}

export default function PanelCart() {
  const queryClient = useQueryClient();
  const { appConfig } = useConfig();
  const currencySign = appConfig?.currency?.sign ?? "$";
  const [removingId, setRemovingId] = useState<number | null>(null);

  const { data: cart, isLoading } = useQuery({
    queryKey: ["panel-cart"],
    queryFn: () =>
      api.get<unknown>(paths.panel.cart.list) as Promise<CartResponse | unknown>,
  });

  const removeMutation = useMutation({
    mutationFn: (id: number) => cartService.remove(id),
    onMutate: (id) => setRemovingId(id),
    onSettled: () => setRemovingId(null),
    onSuccess: () => queryClient.invalidateQueries({ queryKey: ["panel-cart"] }),
  });

  const { items, amounts } = parseCartResponse(cart);
  const total = amounts?.total ?? 0;
  const subTotal = amounts?.sub_total ?? 0;
  const totalDiscount = amounts?.total_discount ?? 0;

  const formatPrice = (value: number) =>
    `${currencySign}${Number(value).toLocaleString("en-IN", { minimumFractionDigits: 2, maximumFractionDigits: 2 })}`;

  return (
    <>
      <div className="mb-8 flex items-center gap-4">
        <Link
          to="/panel"
          className="text-sm text-muted-foreground hover:text-foreground transition-colors"
        >
          ← Dashboard
        </Link>
      </div>
      <h1 className="text-2xl md:text-3xl font-display font-bold text-foreground mb-2">
        Your cart
      </h1>
      <p className="text-muted-foreground mb-8">
        {items.length === 0 && !isLoading
          ? "Add programs from the catalog to get started."
          : `${items.length} item${items.length !== 1 ? "s" : ""} in your cart`}
      </p>

      {isLoading && (
        <div className="space-y-4">
          <div className="h-28 bg-muted rounded-2xl animate-pulse" />
          <div className="h-28 bg-muted rounded-2xl animate-pulse" />
          <div className="h-28 bg-muted rounded-2xl animate-pulse" />
        </div>
      )}

      {!isLoading && items.length === 0 && (
        <Card className="border-2 border-dashed border-border">
          <CardContent className="py-20 text-center">
            <div className="mx-auto w-20 h-20 rounded-full bg-muted flex items-center justify-center mb-6">
              <ShoppingCart className="h-10 w-10 text-muted-foreground" />
            </div>
            <h2 className="text-lg font-semibold text-foreground mb-2">Your cart is empty</h2>
            <p className="text-muted-foreground mb-6 max-w-sm mx-auto">
              Browse programs and add them to your cart to enroll.
            </p>
            <div className="flex flex-wrap justify-center gap-3">
              <Button asChild className="bg-gradient-cta text-primary-foreground shadow-md">
                <Link to="/programs">
                  <BookOpen className="mr-2 h-4 w-4" />
                  Browse programs
                </Link>
              </Button>
              <Button variant="outline" asChild>
                <Link to="/panel/programs">My programs</Link>
              </Button>
            </div>
          </CardContent>
        </Card>
      )}

      {!isLoading && items.length > 0 && (
        <div className="grid gap-8 lg:grid-cols-[1fr,360px]">
          <Card className="border border-border overflow-hidden">
            <CardHeader className="pb-3">
              <CardTitle className="text-lg font-medium">Cart items</CardTitle>
            </CardHeader>
            <CardContent className="p-0">
              <ul className="divide-y divide-border">
                <AnimatePresence mode="popLayout">
                  {items.map((item) => (
                    <motion.li
                      key={item.id}
                      layout
                      initial={{ opacity: 0, y: 8 }}
                      animate={{ opacity: 1, y: 0 }}
                      exit={{ opacity: 0, x: -20 }}
                      transition={{ duration: 0.2 }}
                      className="flex gap-4 p-4 hover:bg-muted/30 transition-colors"
                    >
                      {item.image ? (
                        <img
                          src={item.image}
                          alt=""
                          className="w-20 h-20 object-cover rounded-xl shrink-0 bg-muted"
                        />
                      ) : (
                        <div className="w-20 h-20 rounded-xl bg-muted shrink-0 flex items-center justify-center">
                          <BookOpen className="h-8 w-8 text-muted-foreground" />
                        </div>
                      )}
                      <div className="flex-1 min-w-0">
                        <h3 className="font-semibold text-foreground line-clamp-2">
                          {item.title ?? `Program #${item.webinar_id ?? item.id}`}
                        </h3>
                        {item.teacher_name && (
                          <p className="text-sm text-muted-foreground mt-0.5">
                            {item.teacher_name}
                          </p>
                        )}
                        {item.batch?.name && (
                          <p className="text-sm text-muted-foreground mt-0.5">
                            Batch: {item.batch.name}
                          </p>
                        )}
                        <div className="flex flex-wrap items-center gap-3 mt-2">
                          <Link
                            to={`/programs/${item.webinar_id ?? item.id}`}
                            className="text-sm text-primary hover:underline font-medium"
                          >
                            View program
                          </Link>
                          <Button
                            variant="ghost"
                            size="sm"
                            className="text-destructive hover:text-destructive hover:bg-destructive/10"
                            onClick={() => removeMutation.mutate(item.id)}
                            disabled={removingId === item.id}
                          >
                            {removingId === item.id ? (
                              <span className="animate-pulse">Removing…</span>
                            ) : (
                              <>
                                <Trash2 className="h-4 w-4 mr-1" />
                                Remove
                              </>
                            )}
                          </Button>
                        </div>
                      </div>
                      <div className="text-right shrink-0">
                        {item.price != null && (
                          <p className="font-semibold text-foreground">
                            {formatPrice(item.discount != null && item.discount > 0 ? item.price - item.discount : item.price)}
                          </p>
                        )}
                        {item.discount != null && item.discount > 0 && (
                          <p className="text-sm text-muted-foreground line-through">
                            {formatPrice(item.price ?? 0)}
                          </p>
                        )}
                      </div>
                    </motion.li>
                  ))}
                </AnimatePresence>
              </ul>
            </CardContent>
          </Card>

          <div className="lg:sticky lg:top-24 h-fit">
            <Card className="border border-border shadow-card">
              <CardHeader className="pb-3">
                <CardTitle className="text-base font-medium text-muted-foreground">
                  Order summary
                </CardTitle>
              </CardHeader>
              <CardContent className="space-y-3">
                {subTotal > 0 && (
                  <div className="flex justify-between text-sm">
                    <span className="text-muted-foreground">Subtotal</span>
                    <span>{formatPrice(subTotal)}</span>
                  </div>
                )}
                {totalDiscount > 0 && (
                  <div className="flex justify-between text-sm text-green-600">
                    <span>Discount</span>
                    <span>−{formatPrice(totalDiscount)}</span>
                  </div>
                )}
                {amounts?.tax_price != null && amounts.tax_price > 0 && (
                  <div className="flex justify-between text-sm">
                    <span className="text-muted-foreground">Tax</span>
                    <span>{formatPrice(amounts.tax_price)}</span>
                  </div>
                )}
                <div className="border-t border-border pt-4 flex justify-between items-baseline">
                  <span className="font-semibold text-foreground">Total</span>
                  <span className="text-xl font-bold text-foreground">
                    {formatPrice(total)}
                  </span>
                </div>
                <div className="flex flex-col gap-2 pt-2">
                  <Button className="w-full bg-gradient-cta text-primary-foreground shadow-md hover:opacity-90" asChild>
                    <Link to="/panel/checkout">
                      Proceed to checkout
                      <ChevronRight className="ml-2 h-4 w-4" />
                    </Link>
                  </Button>
                  <Button variant="outline" className="w-full" asChild>
                    <Link to="/programs">Continue browsing</Link>
                  </Button>
                </div>
              </CardContent>
            </Card>
          </div>
        </div>
      )}
    </>
  );
}
