import { Link, useLocation } from "react-router-dom";
import { motion } from "framer-motion";
import { Home, Search } from "lucide-react";
import { Button } from "@/components/ui/button";
import { AnimatedBackground } from "@/components/aceternity/AnimatedBackground";

const NotFound = () => {
  const location = useLocation();

  return (
    <AnimatedBackground variant="dots">
      <motion.div
        initial={{ opacity: 0, y: 20 }}
        animate={{ opacity: 1, y: 0 }}
        transition={{ duration: 0.5 }}
        className="text-center px-4"
      >
        <motion.p
          className="text-8xl md:text-9xl font-display font-bold text-foreground/90 mb-2"
          initial={{ scale: 0.8 }}
          animate={{ scale: 1 }}
          transition={{ delay: 0.2, type: "spring", stiffness: 200 }}
        >
          404
        </motion.p>
        <h1 className="text-xl md:text-2xl font-semibold text-foreground mb-2">
          Page not found
        </h1>
        <p className="text-muted-foreground mb-8 max-w-md mx-auto">
          The page <code className="px-1.5 py-0.5 rounded bg-muted text-sm">{location.pathname}</code> doesn’t exist or was moved.
        </p>
        <div className="flex flex-wrap justify-center gap-4">
          <Button asChild size="lg" className="bg-gradient-cta text-primary-foreground gap-2 rounded-xl">
            <Link to="/">
              <Home size={18} /> Back to Home
            </Link>
          </Button>
          <Button asChild variant="outline" size="lg" className="gap-2 rounded-xl">
            <Link to="/programs">
              <Search size={18} /> Browse programs
            </Link>
          </Button>
        </div>
      </motion.div>
    </AnimatedBackground>
  );
};

export default NotFound;
