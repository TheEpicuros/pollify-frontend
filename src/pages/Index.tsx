
import { motion } from "framer-motion";
import { Link } from "react-router-dom";
import { ChartPie, BarChart3, Vote, ArrowRight } from "lucide-react";
import Navbar from "@/components/Navbar";
import Footer from "@/components/Footer";

const Index = () => {
  return (
    <div className="min-h-screen flex flex-col">
      <Navbar />
      
      <main className="flex-grow pt-20">
        {/* Hero Section */}
        <section className="relative pt-24 pb-20 md:pt-36 md:pb-32 overflow-hidden">
          <div className="absolute inset-0 z-0 overflow-hidden">
            <div className="absolute -top-1/4 -right-1/4 w-1/2 h-1/2 bg-primary/5 rounded-full blur-3xl" />
            <div className="absolute -bottom-1/4 -left-1/4 w-1/2 h-1/2 bg-primary/5 rounded-full blur-3xl" />
          </div>
          
          <div className="container relative z-10">
            <div className="max-w-3xl mx-auto text-center">
              <motion.div
                initial={{ opacity: 0, y: 20 }}
                animate={{ opacity: 1, y: 0 }}
                transition={{ duration: 0.5 }}
              >
                <span className="inline-block bg-primary/10 text-primary px-3 py-1 rounded-full text-sm font-medium mb-6">
                  Simple. Beautiful. Engaging.
                </span>
                
                <motion.h1 
                  className="heading-1 mb-6"
                  initial={{ opacity: 0, y: 20 }}
                  animate={{ opacity: 1, y: 0 }}
                  transition={{ duration: 0.5, delay: 0.1 }}
                >
                  The elegant way to <span className="gradient-text">collect opinions</span> and make decisions
                </motion.h1>
                
                <motion.p 
                  className="text-lg text-muted-foreground mb-8 max-w-2xl mx-auto"
                  initial={{ opacity: 0, y: 20 }}
                  animate={{ opacity: 1, y: 0 }}
                  transition={{ duration: 0.5, delay: 0.2 }}
                >
                  Create beautiful polls, gather insights, and visualize results with a minimalist polling system designed for clarity and engagement.
                </motion.p>
                
                <motion.div 
                  className="flex flex-col sm:flex-row items-center justify-center gap-4"
                  initial={{ opacity: 0, y: 20 }}
                  animate={{ opacity: 1, y: 0 }}
                  transition={{ duration: 0.5, delay: 0.3 }}
                >
                  <Link
                    to="/create"
                    className="bg-primary text-primary-foreground px-8 py-3 rounded-lg font-medium inline-flex items-center gap-2 hover:bg-primary/90 transition-colors"
                  >
                    Create a Poll
                    <ArrowRight size={16} />
                  </Link>
                  <Link
                    to="/polls"
                    className="bg-secondary text-secondary-foreground px-8 py-3 rounded-lg font-medium inline-flex items-center gap-2 hover:bg-secondary/70 transition-colors"
                  >
                    Browse Polls
                  </Link>
                </motion.div>
              </motion.div>
            </div>
          </div>
        </section>
        
        {/* Features Section */}
        <section className="py-20 bg-secondary/50">
          <div className="container">
            <div className="text-center mb-16">
              <h2 className="heading-2 mb-4">Beautifully Simple</h2>
              <p className="text-muted-foreground max-w-2xl mx-auto">
                Thoughtfully designed from the ground up to provide an exceptional polling experience.
              </p>
            </div>
            
            <div className="grid grid-cols-1 md:grid-cols-3 gap-8">
              {[
                {
                  icon: <ChartPie className="h-10 w-10 text-primary" />,
                  title: "Create Polls",
                  description: "Design beautiful polls with multiple options in seconds. Add descriptions and customize to your needs."
                },
                {
                  icon: <Vote className="h-10 w-10 text-primary" />,
                  title: "Collect Votes",
                  description: "Gather responses seamlessly with an elegant voting interface that works on any device."
                },
                {
                  icon: <BarChart3 className="h-10 w-10 text-primary" />,
                  title: "Visualize Results",
                  description: "See real-time results with beautiful, animated visualizations that make data easy to understand."
                }
              ].map((feature, index) => (
                <motion.div
                  key={index}
                  initial={{ opacity: 0, y: 20 }}
                  whileInView={{ opacity: 1, y: 0 }}
                  viewport={{ once: true }}
                  transition={{ duration: 0.5, delay: index * 0.1 }}
                  className="glass-card p-8 rounded-xl"
                >
                  <div className="bg-primary/10 rounded-lg inline-flex items-center justify-center p-3 mb-6">
                    {feature.icon}
                  </div>
                  <h3 className="text-xl font-medium mb-3">{feature.title}</h3>
                  <p className="text-muted-foreground">{feature.description}</p>
                </motion.div>
              ))}
            </div>
          </div>
        </section>
        
        {/* CTA Section */}
        <section className="py-20">
          <div className="container">
            <div className="max-w-3xl mx-auto text-center">
              <motion.div
                initial={{ opacity: 0, scale: 0.95 }}
                whileInView={{ opacity: 1, scale: 1 }}
                viewport={{ once: true }}
                transition={{ duration: 0.5 }}
                className="glass-container p-12 rounded-2xl"
              >
                <h2 className="heading-2 mb-6">Ready to start polling?</h2>
                <p className="text-lg text-muted-foreground mb-8">
                  Create your first poll in seconds and start collecting responses right away.
                </p>
                <Link
                  to="/create"
                  className="bg-primary text-primary-foreground px-8 py-3 rounded-lg font-medium inline-flex items-center gap-2 hover:bg-primary/90 transition-colors"
                >
                  Create a Poll Now
                  <ArrowRight size={16} />
                </Link>
              </motion.div>
            </div>
          </div>
        </section>
      </main>
      
      <Footer />
    </div>
  );
};

export default Index;
