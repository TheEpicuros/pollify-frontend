
import { motion } from "framer-motion";
import Navbar from "@/components/Navbar";
import Footer from "@/components/Footer";
import UserStats from "@/components/UserStats";
import { UserCircle } from "lucide-react";
import { Tabs, TabsContent, TabsList, TabsTrigger } from "@/components/ui/tabs";

const UserDashboard = () => {
  // In a real implementation, this would use authentication to get the current user
  const userName = "Demo User";
  
  return (
    <div className="min-h-screen flex flex-col">
      <Navbar />
      
      <main className="flex-grow pt-20">
        <div className="page-container">
          <div className="max-w-4xl mx-auto">
            <div className="text-center mb-8">
              <motion.div 
                initial={{ opacity: 0, y: -10 }}
                animate={{ opacity: 1, y: 0 }}
                className="inline-flex items-center justify-center w-20 h-20 rounded-full bg-primary/10 text-primary mb-4"
              >
                <UserCircle size={40} />
              </motion.div>
              
              <motion.h1 
                initial={{ opacity: 0, y: -10 }}
                animate={{ opacity: 1, y: 0 }}
                transition={{ delay: 0.1 }}
                className="heading-2 mb-2"
              >
                {userName}
              </motion.h1>
              
              <motion.p
                initial={{ opacity: 0 }}
                animate={{ opacity: 1 }}
                transition={{ delay: 0.2 }}
                className="text-muted-foreground"
              >
                Your poll participation and achievements
              </motion.p>
            </div>
            
            <motion.div
              initial={{ opacity: 0, y: 20 }}
              animate={{ opacity: 1, y: 0 }}
              transition={{ delay: 0.3 }}
              className="glass-card p-6 sm:p-8 rounded-xl"
            >
              <Tabs defaultValue="dashboard" className="w-full">
                <TabsList className="w-full grid grid-cols-2 mb-6">
                  <TabsTrigger value="dashboard">Dashboard</TabsTrigger>
                  <TabsTrigger value="my-polls">My Polls</TabsTrigger>
                </TabsList>
                
                <TabsContent value="dashboard">
                  <UserStats userName={userName} />
                </TabsContent>
                
                <TabsContent value="my-polls">
                  <div className="text-center py-8">
                    <p className="text-muted-foreground">
                      In the WordPress plugin, this tab would display all polls created by the current user.
                    </p>
                    <p className="text-sm text-muted-foreground mt-2">
                      Create polls using the <span className="font-medium">Create Poll</span> page.
                    </p>
                  </div>
                </TabsContent>
              </Tabs>
            </motion.div>
          </div>
        </div>
      </main>
      
      <Footer />
    </div>
  );
};

export default UserDashboard;
