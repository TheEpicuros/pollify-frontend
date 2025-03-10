
import { motion } from "framer-motion";
import Navbar from "@/components/Navbar";
import Footer from "@/components/Footer";
import ProjectProgress from "@/components/ProjectProgress";

const Progress = () => {
  return (
    <div className="min-h-screen flex flex-col bg-gradient-to-b from-slate-100 to-white dark:from-slate-900 dark:to-slate-800">
      <Navbar />
      
      <main className="flex-grow pt-24 pb-16">
        <div className="page-container">
          <div className="max-w-7xl mx-auto">
            <motion.div
              initial={{ opacity: 0, y: 20 }}
              animate={{ opacity: 1, y: 0 }}
              transition={{ duration: 0.5, delay: 0.2 }}
              className="glass-card p-6 sm:p-8 rounded-xl shadow-lg backdrop-blur-sm bg-white/80 dark:bg-slate-900/80 border border-slate-200/50 dark:border-slate-700/50"
            >
              <ProjectProgress />
            </motion.div>
          </div>
        </div>
      </main>
      
      <Footer />
    </div>
  );
};

export default Progress;
