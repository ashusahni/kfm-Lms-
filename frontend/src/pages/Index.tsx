import { useEffect } from "react";
import { useLocation } from "react-router-dom";
import Navbar from "@/components/Navbar";
import HeroSection from "@/components/HeroSection";
import SubscriptionStrip from "@/components/SubscriptionStrip";
import ExploreCategories from "@/components/ExploreCategories";
import HowItWorks from "@/components/HowItWorks";
import LatestPrograms from "@/components/LatestPrograms";
import FeaturedPrograms from "@/components/FeaturedPrograms";
import CoachesSection from "@/components/CoachesSection";
import Testimonials from "@/components/Testimonials";
import UpcomingBatches from "@/components/UpcomingBatches";
import MissionVideo from "@/components/MissionVideo";
import BlogSection from "@/components/BlogSection";
import FinalCTA from "@/components/FinalCTA";
import Footer from "@/components/Footer";

const Index = () => {
  const { hash } = useLocation();

  useEffect(() => {
    if (!hash) return;
    const id = hash.replace(/^#/, "");
    const timer = setTimeout(() => {
      const el = document.getElementById(id);
      if (el) el.scrollIntoView({ behavior: "smooth", block: "start" });
    }, 100);
    return () => clearTimeout(timer);
  }, [hash]);

  return (
    <div className="page-bg">
      <Navbar />
      <HeroSection />
      <SubscriptionStrip />
      <ExploreCategories />
      <HowItWorks />
      <LatestPrograms />
      <FeaturedPrograms />
      <CoachesSection />
      <Testimonials />
      <UpcomingBatches />
      <MissionVideo />
      <BlogSection />
      <FinalCTA />
      <Footer />
    </div>
  );
};

export default Index;
