import { motion } from "framer-motion";
import { Link } from "react-router-dom";
import { ArrowRight } from "lucide-react";
import { useConfig } from "@/context/ConfigContext";
import { Button } from "@/components/ui/button";

const FinalCTA = () => {
  const { t } = useConfig();
  return (
    <section id="cta" className="py-20 md:py-24">
      <div className="section-container">
        <motion.div
          initial={{ opacity: 0, y: 20 }}
          whileInView={{ opacity: 1, y: 0 }}
          viewport={{ once: true }}
          className="relative rounded-3xl overflow-hidden bg-gradient-fitness-hero p-12 md:p-20 lg:p-24 text-center"
        >
          <div className="absolute inset-0 z-0">
            <div className="absolute inset-0 bg-gradient-to-b from-[hsl(152,50%,28%)]/95 to-[hsl(165,45%,18%)]/95" />
            <div className="absolute top-0 left-1/2 -translate-x-1/2 w-[600px] h-[300px] bg-white/5 rounded-full blur-3xl" />
            <div className="absolute bottom-0 left-0 w-80 h-80 bg-primary/20 rounded-full blur-3xl" />
            <div className="absolute bottom-0 right-0 w-80 h-80 bg-accent/10 rounded-full blur-3xl" />
          </div>
          <div className="relative z-10 max-w-2xl mx-auto">
            <h2 className="text-2xl md:text-4xl lg:text-[2.75rem] font-bold text-white mb-4 tracking-tight leading-tight">
              Ready to transform your fitness?
            </h2>
            <p className="text-white/90 text-lg md:text-xl mb-10">
              Join a program today. Get daily coaching, nutrition support, and a community that keeps you moving.
            </p>
            <Link to="/register">
              <Button size="lg" className="bg-white text-[hsl(152,55%,28%)] hover:bg-white/95 font-semibold shadow-xl hover:shadow-2xl transition-all rounded-xl px-8 gap-2 hover:scale-[1.02]">
                Join a program <ArrowRight size={18} />
              </Button>
            </Link>
          </div>
        </motion.div>
      </div>
    </section>
  );
};

export default FinalCTA;
