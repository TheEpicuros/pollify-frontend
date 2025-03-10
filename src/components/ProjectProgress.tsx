
import React, { useState } from "react";
import { CustomProgress } from "@/components/ui/custom-progress";
import { motion } from "framer-motion";
import { Card, CardContent, CardHeader, CardTitle } from "@/components/ui/card";
import { Button } from "@/components/ui/button";
import { CircleCheckBig, CircleArrowRight, Hourglass } from "lucide-react";

const ProjectProgress = () => {
  const [showAll, setShowAll] = useState(false);
  
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
    <div className="space-y-8">
      <div className="text-center mb-10">
        <h2 className="text-3xl md:text-4xl font-bold mb-3 bg-gradient-to-r from-indigo-600 via-purple-500 to-pink-500 bg-clip-text text-transparent">
          Poll Plugin Development Status
        </h2>
        <p className="text-muted-foreground max-w-2xl mx-auto">
          Track the progress of our WordPress Poll Plugin development. See which features are complete, in development, or coming soon.
        </p>
      </div>

      <Card className="border border-slate-200/50 dark:border-slate-700/50 shadow-md overflow-hidden">
        <CardHeader className="pb-3 bg-slate-50/50 dark:bg-slate-800/50">
          <CardTitle className="flex items-center justify-between">
            <span>Project Completion</span>
            <span className="text-lg font-bold bg-gradient-to-r from-indigo-600 via-purple-500 to-pink-500 bg-clip-text text-transparent">
              {Math.round(overallProgress)}%
            </span>
          </CardTitle>
        </CardHeader>
        <CardContent className="pt-6">
          <CustomProgress
            value={overallProgress}
            size="lg"
            animated={true}
            gradient={true}
            className="mb-4"
          />
          
          <div className="grid grid-cols-3 gap-4 text-center mt-4">
            <div className="bg-white/50 dark:bg-slate-800/50 backdrop-blur-sm border border-slate-200/20 dark:border-slate-700/20 p-4 rounded-lg">
              <div className="flex flex-col items-center">
                <div className="mb-2 p-2 rounded-full bg-emerald-100 dark:bg-emerald-900/20">
                  <CircleCheckBig className="h-6 w-6 text-emerald-500" />
                </div>
                <span className="text-2xl font-bold text-emerald-500">{completedFeatures.length}</span>
                <p className="text-sm text-muted-foreground">Completed</p>
              </div>
            </div>
            <div className="bg-white/50 dark:bg-slate-800/50 backdrop-blur-sm border border-slate-200/20 dark:border-slate-700/20 p-4 rounded-lg">
              <div className="flex flex-col items-center">
                <div className="mb-2 p-2 rounded-full bg-amber-100 dark:bg-amber-900/20">
                  <CircleArrowRight className="h-6 w-6 text-amber-500" />
                </div>
                <span className="text-2xl font-bold text-amber-500">{inProgressFeatures.length}</span>
                <p className="text-sm text-muted-foreground">In Progress</p>
              </div>
            </div>
            <div className="bg-white/50 dark:bg-slate-800/50 backdrop-blur-sm border border-slate-200/20 dark:border-slate-700/20 p-4 rounded-lg">
              <div className="flex flex-col items-center">
                <div className="mb-2 p-2 rounded-full bg-rose-100 dark:bg-rose-900/20">
                  <Hourglass className="h-6 w-6 text-rose-500" />
                </div>
                <span className="text-2xl font-bold text-rose-500">{upcomingFeatures.length}</span>
                <p className="text-sm text-muted-foreground">Upcoming</p>
              </div>
            </div>
          </div>
        </CardContent>
      </Card>

      <div className="grid gap-8 md:grid-cols-2">
        <Card className="border border-slate-200/50 dark:border-slate-700/50 shadow-md">
          <CardHeader className="pb-3 bg-slate-50/50 dark:bg-slate-800/50">
            <CardTitle className="text-xl">
              <span className="flex items-center gap-2">
                <CircleArrowRight className="h-5 w-5 text-amber-500" />
                In Progress
              </span>
            </CardTitle>
          </CardHeader>
          <CardContent className="pt-6">
            <motion.div 
              className="space-y-5"
              variants={container}
              initial="hidden"
              animate="show"
            >
              {inProgressFeatures.slice(0, showAll ? inProgressFeatures.length : 4).map((feature, index) => (
                <motion.div key={index} variants={item}>
                  <div className="mb-2 flex justify-between items-center">
                    <span className="font-medium text-sm">{feature.name}</span>
                    <span className={`text-sm font-semibold ${
                      feature.progress >= 75 ? 'text-emerald-500' : 
                      feature.progress >= 50 ? 'text-amber-500' : 'text-rose-500'
                    }`}>
                      {feature.progress}%
                    </span>
                  </div>
                  <CustomProgress
                    value={feature.progress}
                    size="md"
                    animated={feature.progress < 100}
                    className="mb-1"
                    fillClassName={`${
                      feature.progress >= 75 ? 'bg-emerald-500' : 
                      feature.progress >= 50 ? 'bg-amber-500' : 'bg-rose-500'
                    }`}
                  />
                  <p className="text-xs text-muted-foreground mt-1">{feature.description}</p>
                </motion.div>
              ))}
              
              {inProgressFeatures.length > 4 && (
                <div className="pt-2 text-center">
                  <Button 
                    variant="outline" 
                    size="sm" 
                    onClick={() => setShowAll(!showAll)}
                    className="text-muted-foreground"
                  >
                    {showAll ? "Show Less" : `Show ${inProgressFeatures.length - 4} More`}
                  </Button>
                </div>
              )}
            </motion.div>
          </CardContent>
        </Card>

        <Card className="border border-slate-200/50 dark:border-slate-700/50 shadow-md">
          <CardHeader className="pb-3 bg-slate-50/50 dark:bg-slate-800/50">
            <CardTitle className="text-xl">
              <span className="flex items-center gap-2">
                <CircleCheckBig className="h-5 w-5 text-emerald-500" />
                Completed
              </span>
            </CardTitle>
          </CardHeader>
          <CardContent className="pt-6">
            <motion.div 
              className="space-y-5"
              variants={container}
              initial="hidden"
              animate="show"
            >
              {completedFeatures.slice(0, showAll ? completedFeatures.length : 4).map((feature, index) => (
                <motion.div key={index} variants={item}>
                  <div className="mb-2 flex justify-between items-center">
                    <span className="font-medium text-sm">{feature.name}</span>
                    <span className="text-sm font-semibold text-emerald-500">
                      {feature.progress}%
                    </span>
                  </div>
                  <CustomProgress
                    value={feature.progress}
                    size="md"
                    fillClassName="bg-emerald-500"
                    className="mb-1"
                  />
                  <p className="text-xs text-muted-foreground mt-1">{feature.description}</p>
                </motion.div>
              ))}
              
              {completedFeatures.length > 4 && (
                <div className="pt-2 text-center">
                  <Button 
                    variant="outline" 
                    size="sm" 
                    onClick={() => setShowAll(!showAll)}
                    className="text-muted-foreground"
                  >
                    {showAll ? "Show Less" : `Show ${completedFeatures.length - 4} More`}
                  </Button>
                </div>
              )}
            </motion.div>
          </CardContent>
        </Card>
      </div>

      <div className="pt-6">
        <Card className="border border-slate-200/50 dark:border-slate-700/50 shadow-md">
          <CardHeader className="pb-3 bg-slate-50/50 dark:bg-slate-800/50">
            <CardTitle className="text-xl">Development Timeline</CardTitle>
          </CardHeader>
          <CardContent className="pt-6">
            <div className="relative pl-8 before:absolute before:top-1 before:left-3 before:h-full before:w-[2px] before:bg-slate-200 dark:before:bg-slate-700">
              {[
                { date: "Dec 2023", title: "Project Start", description: "Initial setup and core plugin structure" },
                { date: "Feb 2024", title: "Core Features", description: "Polls creation, display, and admin dashboard" },
                { date: "Apr 2024", title: "Extended Features", description: "Analytics, poll types and tracking" },
                { date: "July 2024", title: "Advanced Features", description: "Comments, ratings, and social integration" },
                { date: "Aug 2024", title: "Project Completion", description: "Final testing and deployment" }
              ].map((item, index) => (
                <div key={index} className="mb-8 relative">
                  <div className="absolute -left-6 mt-1.5 h-4 w-4 rounded-full bg-primary border-2 border-white dark:border-slate-900"></div>
                  <div className="text-sm text-muted-foreground">{item.date}</div>
                  <h3 className="font-medium mt-1">{item.title}</h3>
                  <p className="text-sm text-muted-foreground mt-1">{item.description}</p>
                </div>
              ))}
            </div>
          </CardContent>
        </Card>
      </div>
    </div>
  );
};

export default ProjectProgress;
