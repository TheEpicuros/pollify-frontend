
import { motion } from "framer-motion";
import Navbar from "@/components/Navbar";
import Footer from "@/components/Footer";
import PollForm from "@/components/PollForm";
import PluginDownloadButton from "@/components/PluginDownloadButton";

const PollCreate = () => {
  return (
    <div className="min-h-screen flex flex-col">
      <Navbar />
      
      <main className="flex-grow pt-20">
        <div className="page-container">
          <div className="max-w-3xl mx-auto">
            <div className="text-center mb-8">
              <motion.h1 
                initial={{ opacity: 0, y: -10 }}
                animate={{ opacity: 1, y: 0 }}
                className="heading-2 mb-4"
              >
                Create a New Poll
              </motion.h1>
              <motion.p 
                initial={{ opacity: 0 }}
                animate={{ opacity: 1 }}
                transition={{ delay: 0.1 }}
                className="text-muted-foreground"
              >
                Design your poll, add options, and start collecting responses
              </motion.p>
            </div>
            
            <motion.div
              initial={{ opacity: 0, y: 20 }}
              animate={{ opacity: 1, y: 0 }}
              transition={{ delay: 0.2 }}
              className="glass-card p-6 sm:p-8 rounded-xl"
            >
              <PollForm />
            </motion.div>
            
            <motion.div
              initial={{ opacity: 0, y: 20 }}
              animate={{ opacity: 1, y: 0 }}
              transition={{ delay: 0.3 }}
              className="mt-8 flex justify-center"
            >
              <div className="glass-card p-6 rounded-xl w-full max-w-md">
                <h3 className="font-semibold text-xl mb-3 text-center">WordPress Plugin</h3>
                <p className="text-muted-foreground text-sm mb-4 text-center">
                  Download the WordPress plugin files and install them on your WordPress site.
                </p>
                <PluginDownloadButton />
              </div>
            </motion.div>
          </div>
        </div>
      </main>
      
      <Footer />
    </div>
  );
};

export default PollCreate;
