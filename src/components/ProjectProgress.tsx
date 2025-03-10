
import React from "react";
import { CustomProgress } from "@/components/ui/custom-progress";
import { motion } from "framer-motion";

const ProjectProgress = () => {
  const features = [
    { name: "Core Plugin Structure", progress: 100, description: "Basic plugin files and structure" },
    { name: "Poll Creation Interface", progress: 85, description: "Interface for creating polls" },
    { name: "Poll Display/Voting", progress: 80, description: "Viewing and voting on polls" },
    { name: "Poll Results Visualization", progress: 90, description: "Progress bars and statistics" },
    { name: "Admin Dashboard", progress: 100, description: "Complete admin interface" },
    { name: "Settings Panel", progress: 100, description: "Configuration options" },
    { name: "Analytics", progress: 95, description: "Data visualization and exports" },
    { name: "Poll Types", progress: 60, description: "Different poll types implementation" },
    { name: "Social Media Integration", progress: 50, description: "Sharing on social platforms" },
    { name: "User Voting Tracking", progress: 90, description: "Track who voted on what" },
    { name: "Rating System", progress: 40, description: "Up/down rating for polls" },
    { name: "Comments System", progress: 35, description: "Comment functionality" },
    { name: "Shortcode Support", progress: 85, description: "Embedding polls anywhere" }
  ];

  // Calculate overall progress
  const overallProgress = features.reduce((sum, feature) => sum + feature.progress, 0) / features.length;

  // Group features by completion status
  const completedFeatures = features.filter(f => f.progress === 100);
  const inProgressFeatures = features.filter(f => f.progress > 0 && f.progress < 100);
  const upcomingFeatures = features.filter(f => f.progress === 0);

  // Sort in-progress by highest completion first
  inProgressFeatures.sort((a, b) => b.progress - a.progress);

  const container = {
    hidden: { opacity: 0 },
    show: {
      opacity: 1,
      transition: {
        staggerChildren: 0.1
      }
    }
  };

  const item = {
    hidden: { opacity: 0, y: 20 },
    show: { opacity: 1, y: 0 }
  };
  
  return (
    <div className="space-y-8 p-6">
      <div className="text-center mb-8">
        <h2 className="text-3xl font-bold mb-2 bg-gradient-to-r from-indigo-600 to-purple-600 bg-clip-text text-transparent">
          WordPress Poll Plugin Progress
        </h2>
        <p className="text-muted-foreground">
          Current development status of features
        </p>
      </div>

      <div className="mb-8">
        <div className="flex items-center justify-between mb-3">
          <h3 className="font-semibold text-lg">Overall Completion</h3>
          <span className="text-lg font-bold bg-gradient-to-r from-indigo-600 to-purple-600 bg-clip-text text-transparent">
            {Math.round(overallProgress)}%
          </span>
        </div>
        <CustomProgress
          value={overallProgress}
          size="lg"
          animated={true}
          gradient={true}
        />
      </div>

      <div className="grid gap-6 md:grid-cols-2">
        <motion.div 
          className="space-y-4"
          variants={container}
          initial="hidden"
          animate="show"
        >
          <h3 className="font-medium text-lg border-b pb-2">In Progress</h3>
          {inProgressFeatures.map((feature, index) => (
            <motion.div key={index} className="bg-white/5 backdrop-blur-sm border border-slate-200/20 p-4 rounded-lg shadow-sm" variants={item}>
              <CustomProgress
                value={feature.progress}
                label={feature.name}
                showValue={true}
                size="md"
                animated={true}
                labelPosition="top"
                className="mb-1"
                fillClassName={`${feature.progress >= 75 ? 'bg-emerald-500' : 
                  feature.progress >= 50 ? 'bg-amber-500' : 'bg-rose-500'}`}
              />
              <p className="text-xs text-muted-foreground mt-1">{feature.description}</p>
            </motion.div>
          ))}
        </motion.div>

        <div className="grid gap-6">
          <motion.div 
            className="space-y-4"
            variants={container}
            initial="hidden"
            animate="show"
          >
            <h3 className="font-medium text-lg border-b pb-2">Completed</h3>
            {completedFeatures.map((feature, index) => (
              <motion.div key={index} className="bg-white/5 backdrop-blur-sm border border-emerald-200/20 p-4 rounded-lg shadow-sm" variants={item}>
                <CustomProgress
                  value={feature.progress}
                  label={feature.name}
                  showValue={true}
                  size="md"
                  fillClassName="bg-emerald-500"
                  className="mb-1"
                />
                <p className="text-xs text-muted-foreground mt-1">{feature.description}</p>
              </motion.div>
            ))}
          </motion.div>
        </div>
      </div>

      <div className="mt-8 pt-6 border-t">
        <h3 className="font-medium text-lg mb-4">Feature Status Breakdown</h3>
        <div className="grid grid-cols-3 gap-4 text-center">
          <div className="bg-white/5 backdrop-blur-sm border border-slate-200/20 p-4 rounded-lg">
            <span className="text-2xl font-bold text-emerald-500">{completedFeatures.length}</span>
            <p className="text-sm text-muted-foreground">Completed</p>
          </div>
          <div className="bg-white/5 backdrop-blur-sm border border-slate-200/20 p-4 rounded-lg">
            <span className="text-2xl font-bold text-amber-500">{inProgressFeatures.length}</span>
            <p className="text-sm text-muted-foreground">In Progress</p>
          </div>
          <div className="bg-white/5 backdrop-blur-sm border border-slate-200/20 p-4 rounded-lg">
            <span className="text-2xl font-bold text-rose-500">{upcomingFeatures.length}</span>
            <p className="text-sm text-muted-foreground">Upcoming</p>
          </div>
        </div>
      </div>
    </div>
  );
};

export default ProjectProgress;
