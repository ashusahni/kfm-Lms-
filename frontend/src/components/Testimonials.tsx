import { motion } from "framer-motion";
import { Star } from "lucide-react";

const testimonials = [
  {
    name: "Meera K.",
    program: "Clean Eating Reset",
    story: "Lost 14 kg in 12 weeks. The daily tracking and live coaching kept me accountable. My energy levels have completely transformed.",
    rating: 5,
  },
  {
    name: "Rahul D.",
    program: "Strength & Sculpt",
    story: "Gained 6 kg of lean muscle. The structured meal plans and workout routines were exactly what I needed. Best investment in my health.",
    rating: 5,
  },
  {
    name: "Anitha G.",
    program: "Holistic Wellness",
    story: "My overall wellness improved within 8 weeks. The guidance on nutrition and lifestyle changes made all the difference. Highly recommend.",
    rating: 5,
  },
];

const Testimonials = () => {
  return (
    <section id="stories" className="py-20 md:py-24 bg-card/40">
      <div className="section-container">
        <motion.div
          initial={{ opacity: 0, y: 20 }}
          whileInView={{ opacity: 1, y: 0 }}
          viewport={{ once: true }}
          className="text-center mb-14"
        >
          <span className="text-sm font-semibold text-primary uppercase tracking-wider">Testimonials</span>
          <h2 className="text-2xl md:text-3xl font-bold text-foreground tracking-tight mt-2">
            Success stories
          </h2>
          <p className="text-muted-foreground mt-2 max-w-xl mx-auto">Hear from people who transformed their fitness with our programs.</p>
        </motion.div>

        <div className="grid md:grid-cols-3 gap-6 lg:gap-8 max-w-5xl mx-auto">
          {testimonials.map((t, i) => (
            <motion.div
              key={t.name}
              initial={{ opacity: 0, y: 24 }}
              whileInView={{ opacity: 1, y: 0 }}
              viewport={{ once: true }}
              transition={{ delay: i * 0.1 }}
              className="group bg-background rounded-2xl p-6 lg:p-8 border border-border shadow-card hover:shadow-card-hover hover:border-primary/20 transition-all duration-300"
            >
              <div className="flex gap-1 mb-4">
                {Array.from({ length: t.rating }).map((_, j) => (
                  <Star key={j} size={18} className="fill-primary text-primary" />
                ))}
              </div>
              <p className="text-foreground/90 leading-relaxed mb-6 text-[15px]">"{t.story}"</p>
              <div className="pt-4 border-t border-border">
                <div className="font-semibold text-foreground">{t.name}</div>
                <div className="text-sm text-muted-foreground">{t.program}</div>
              </div>
            </motion.div>
          ))}
        </div>
      </div>
    </section>
  );
};

export default Testimonials;
