import { useSearchParams, Link } from "react-router-dom";
import { CheckCircle, BookOpen, LayoutDashboard } from "lucide-react";
import { Button } from "@/components/ui/button";
import { Card, CardContent } from "@/components/ui/card";
import { motion } from "framer-motion";

export default function PanelCartSuccess() {
  const [searchParams] = useSearchParams();
  const orderId = searchParams.get("order_id");

  return (
    <motion.div
      initial={{ opacity: 0, y: 12 }}
      animate={{ opacity: 1, y: 0 }}
      transition={{ duration: 0.3 }}
      className="max-w-lg mx-auto"
    >
      <Card className="border-2 border-green-500/20 bg-green-500/5 overflow-hidden">
        <CardContent className="pt-10 pb-10 text-center">
          <div className="mx-auto w-20 h-20 rounded-full bg-green-500/20 flex items-center justify-center mb-6">
            <CheckCircle className="h-12 w-12 text-green-600" />
          </div>
          <h1 className="text-2xl font-display font-bold text-foreground mb-2">
            Payment successful
          </h1>
          <p className="text-muted-foreground mb-8">
            {orderId
              ? `Order #${orderId} has been confirmed. You can access your program from My Programs.`
              : "Your order has been confirmed. You can access your program from My Programs."}
          </p>
          <div className="flex flex-col sm:flex-row gap-3 justify-center">
            <Button className="bg-gradient-cta text-primary-foreground shadow-md" asChild>
              <Link to="/panel/programs">
                <BookOpen className="mr-2 h-4 w-4" />
                View my programs
              </Link>
            </Button>
            <Button variant="outline" asChild>
              <Link to="/panel">
                <LayoutDashboard className="mr-2 h-4 w-4" />
                Dashboard
              </Link>
            </Button>
            <Button variant="ghost" asChild>
              <Link to="/programs">Browse more programs</Link>
            </Button>
          </div>
        </CardContent>
      </Card>
    </motion.div>
  );
}
