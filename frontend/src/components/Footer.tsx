import { Instagram, Twitter, Youtube, Linkedin } from "lucide-react";

const Footer = () => {
  return (
    <footer className="relative bg-gradient-footer text-primary-foreground/85 py-16 md:py-20 overflow-hidden">
      <div className="absolute top-0 left-0 right-0 h-px bg-gradient-to-r from-transparent via-primary-foreground/20 to-transparent" />
      <div className="section-container relative">
        <div className="grid md:grid-cols-2 lg:grid-cols-4 gap-10 lg:gap-12 mb-14">
          {/* Brand */}
          <div className="lg:col-span-1">
            <div className="flex items-center gap-3 mb-4">
              <div className="w-10 h-10 rounded-xl bg-gradient-cta flex items-center justify-center shadow-lg shadow-primary/20">
                <span className="text-primary-foreground font-bold text-sm">FK</span>
              </div>
              <span className="font-display text-xl font-bold text-primary-foreground">
                Fit Karnataka
              </span>
            </div>
            <p className="text-sm leading-relaxed text-primary-foreground/60 max-w-xs">
              Transforming health across Karnataka through structured coaching and measurable resultss.
            </p>
          </div>

          {/* Quick Links */}
          <div>
            <h4 className="font-sans text-xs font-bold text-primary-foreground/70 uppercase tracking-wider mb-4">Programs</h4>
            <div className="space-y-3">
              <a href="#programs" className="block text-sm hover:text-primary-foreground transition-colors hover:translate-x-0.5 inline-block">Featured Programs</a>
              <a href="#batches" className="block text-sm hover:text-primary-foreground transition-colors hover:translate-x-0.5 inline-block">Upcoming Batches</a>
              <a href="#coaches" className="block text-sm hover:text-primary-foreground transition-colors hover:translate-x-0.5 inline-block">Our Coaches</a>
            </div>
          </div>

          {/* Resources */}
          <div>
            <h4 className="font-sans text-xs font-bold text-primary-foreground/70 uppercase tracking-wider mb-4">Resources</h4>
            <div className="space-y-3">
              <a href="#blog" className="block text-sm hover:text-primary-foreground transition-colors hover:translate-x-0.5 inline-block">Health Articles</a>
              <a href="#stories" className="block text-sm hover:text-primary-foreground transition-colors hover:translate-x-0.5 inline-block">Success Stories</a>
              <a href="#" className="block text-sm hover:text-primary-foreground transition-colors hover:translate-x-0.5 inline-block">FAQ</a>
            </div>
          </div>

          {/* Contact */}
          <div>
            <h4 className="font-sans text-xs font-bold text-primary-foreground/70 uppercase tracking-wider mb-4">Contact</h4>
            <div className="space-y-2.5 text-sm text-primary-foreground/80">
              <p><a href="mailto:hello@fitkarnataka.in" className="hover:text-primary-foreground transition-colors">hello@fitkarnataka.in</a></p>
              <p>+91 98765 43210</p>
              <p>Bangalore, Karnataka</p>
            </div>
            <div className="flex gap-2 mt-4">
              <a href="#" className="w-10 h-10 rounded-xl bg-primary-foreground/10 flex items-center justify-center hover:bg-primary-foreground/20 transition-all hover:scale-105" aria-label="Instagram">
                <Instagram size={18} />
              </a>
              <a href="#" className="w-10 h-10 rounded-xl bg-primary-foreground/10 flex items-center justify-center hover:bg-primary-foreground/20 transition-all hover:scale-105" aria-label="Twitter">
                <Twitter size={18} />
              </a>
              <a href="#" className="w-10 h-10 rounded-xl bg-primary-foreground/10 flex items-center justify-center hover:bg-primary-foreground/20 transition-all hover:scale-105" aria-label="YouTube">
                <Youtube size={18} />
              </a>
              <a href="#" className="w-10 h-10 rounded-xl bg-primary-foreground/10 flex items-center justify-center hover:bg-primary-foreground/20 transition-all hover:scale-105" aria-label="LinkedIn">
                <Linkedin size={18} />
              </a>
            </div>
          </div>
        </div>

        <div className="border-t border-primary-foreground/10 pt-8 flex flex-col md:flex-row justify-between items-center gap-4 text-sm text-primary-foreground/50">
          <p>© 2026 Fit Karnataka Mission. All rights reserved.</p>
          <div className="flex gap-6">
            <a href="#" className="hover:text-primary-foreground/80 transition-colors">Privacy Policy</a>
            <a href="#" className="hover:text-primary-foreground/80 transition-colors">Terms of Service</a>
          </div>
        </div>
      </div>
    </footer>
  );
};

export default Footer;
