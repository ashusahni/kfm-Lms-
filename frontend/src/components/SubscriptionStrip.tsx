import { Link } from "react-router-dom";
import { motion } from "framer-motion";
import { Button } from "@/components/ui/button";
import { ArrowRight } from "lucide-react";

const SubscriptionStrip = () => {
  return (
    <motion.section
      initial={{ opacity: 0, y: 16 }}
      whileInView={{ opacity: 1, y: 0 }}
      viewport={{ once: true }}
      className="py-8"
    >
      <div className="section-container">
        <div className="relative overflow-hidden rounded-2xl border border-border bg-card shadow-card p-6 md:p-8 lg:p-10">
          <div className="absolute top-0 right-0 w-64 h-64 bg-primary/5 rounded-full blur-3xl -translate-y-1/2 translate-x-1/2" />
          <div className="absolute bottom-0 left-0 w-48 h-48 bg-accent/5 rounded-full blur-3xl translate-y-1/2 -translate-x-1/2" />
          <div className="relative flex flex-wrap items-center justify-between gap-6">
            <div className="max-w-xl">
              <h3 className="text-xl md:text-2xl font-bold text-foreground leading-snug tracking-tight">
                Get unlimited access to fitness programs, nutrition plans, and live coaching
              </h3>
              <p className="text-muted-foreground mt-2 text-sm md:text-base">
                Start your free trial—no credit card required.
              </p>
            </div>
            <Link to="/register">
              <Button
                size="lg"
                className="bg-gradient-cta text-primary-foreground font-semibold rounded-xl px-6 py-6 text-base shadow-lg shadow-primary/20 hover:shadow-primary/30 hover:opacity-95 transition-all gap-2"
              >
                Start free trial <ArrowRight className="w-4 h-4" />
              </Button>
            </Link>
          </div>
        </div>
      </div>
    </motion.section>
  );
};

export default SubscriptionStrip;
