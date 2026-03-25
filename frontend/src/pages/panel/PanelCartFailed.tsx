import { useSearchParams, Link } from "react-router-dom";
import { XCircle, ShoppingCart, LayoutDashboard } from "lucide-react";
import { Button } from "@/components/ui/button";
import { Card, CardContent } from "@/components/ui/card";
import { motion } from "framer-motion";

export default function PanelCartFailed() {
  const [searchParams] = useSearchParams();
  const orderId = searchParams.get("order_id");

  return (
    <motion.div
      initial={{ opacity: 0, y: 12 }}
      animate={{ opacity: 1, y: 0 }}
      transition={{ duration: 0.3 }}
      className="max-w-lg mx-auto"
    >
      <Card className="border-2 border-destructive/20 bg-destructive/5 overflow-hidden">
        <CardContent className="pt-10 pb-10 text-center">
          <div className="mx-auto w-20 h-20 rounded-full bg-destructive/20 flex items-center justify-center mb-6">
            <XCircle className="h-12 w-12 text-destructive" />
          </div>
          <h1 className="text-2xl font-display font-bold text-foreground mb-2">
            Payment failed
          </h1>
          <p className="text-muted-foreground mb-2">
            {orderId
              ? `Payment for order #${orderId} could not be completed.`
              : "Payment could not be completed."}
          </p>
          <p className="text-sm text-muted-foreground mb-8">
            You can try again from your cart or contact support if the problem persists.
          </p>
          <div className="flex flex-col sm:flex-row gap-3 justify-center">
            <Button className="bg-gradient-cta text-primary-foreground shadow-md" asChild>
              <Link to="/panel/cart">
                <ShoppingCart className="mr-2 h-4 w-4" />
                Back to cart
              </Link>
            </Button>
            <Button variant="outline" asChild>
              <Link to="/panel">
                <LayoutDashboard className="mr-2 h-4 w-4" />
                Dashboard
              </Link>
            </Button>
          </div>
        </CardContent>
      </Card>
    </motion.div>
  );
}
