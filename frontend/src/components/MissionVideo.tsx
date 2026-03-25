import { motion } from "framer-motion";
import { Play, ArrowRight } from "lucide-react";

const MissionVideo = () => {
  return (
    <section className="py-20 md:py-24">
      <div className="section-container">
        <div className="grid lg:grid-cols-2 gap-12 items-center">
          {/* Video placeholder */}
          <motion.div
            initial={{ opacity: 0, x: -30 }}
            whileInView={{ opacity: 1, x: 0 }}
            viewport={{ once: true }}
            className="relative aspect-video rounded-2xl bg-foreground/5 border border-border overflow-hidden flex items-center justify-center group cursor-pointer"
          >
            <div className="absolute inset-0 bg-gradient-to-br from-primary/10 to-accent/10" />
            <div className="relative z-10 w-20 h-20 rounded-full bg-gradient-cta flex items-center justify-center shadow-elevated group-hover:scale-110 transition-transform">
              <Play size={32} className="text-primary-foreground ml-1" />
            </div>
          </motion.div>

          {/* Content */}
          <motion.div
            initial={{ opacity: 0, x: 30 }}
            whileInView={{ opacity: 1, x: 0 }}
            viewport={{ once: true }}
          >
            <span className="text-sm font-semibold text-primary uppercase tracking-wider">Why We Exist</span>
            <h2 className="text-3xl md:text-4xl font-display font-bold text-foreground mt-3 mb-6">
              Our Mission
            </h2>
            <p className="text-muted-foreground leading-relaxed mb-6">
              Fit Karnataka Mission is committed to transforming health outcomes across Karnataka through structured, science-backed coaching programs. We believe every individual deserves access to expert guidance, personalised nutrition plans, and a supportive community that drives lasting change.
            </p>
            <p className="text-muted-foreground leading-relaxed mb-8">
              Our cohort-based approach ensures accountability, while daily tracking and live sessions make the journey engaging and measurable.
            </p>
            <a
              href="#cta"
              className="inline-flex items-center gap-2 px-7 py-3.5 rounded-xl bg-gradient-cta text-primary-foreground font-semibold hover:opacity-90 transition-opacity"
            >
              Start Your Journey <ArrowRight size={18} />
            </a>
          </motion.div>
        </div>
      </div>
    </section>
  );
};

export default MissionVideo;
