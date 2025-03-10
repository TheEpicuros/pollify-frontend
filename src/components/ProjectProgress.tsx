
import { motion } from "framer-motion";
import { CalendarDays, Check, Code, FileText, ListChecks, Settings, Share2, Users } from "lucide-react";
import { CustomProgress } from "@/components/ui/custom-progress";

const ProjectProgress = () => {
  // Progress data for different project areas
  const progressData = [
    { 
      label: "Poll Creation Interface", 
      value: 100, 
      description: "Complete with multiple poll types, image options, and live preview" 
    },
    { 
      label: "Poll Display Component", 
      value: 100, 
      description: "Complete with various visualization options and responsive design" 
    },
    { 
      label: "User Authentication", 
      value: 90, 
      description: "Login, registration, and user profile management" 
    },
    { 
      label: "WordPress Plugin", 
      value: 95, 
      description: "Core functionality complete with shortcode support and specialized renderers" 
    },
    { 
      label: "Analytics Dashboard", 
      value: 85, 
      description: "Vote tracking, user engagement metrics, and data visualization" 
    },
    { 
      label: "Admin Controls", 
      value: 90, 
      description: "Moderation tools, settings management, and user permissions" 
    }
  ];

  // Overall project completion percentage
  const overallProgress = Math.round(
    progressData.reduce((sum, item) => sum + item.value, 0) / progressData.length
  );

  // Key development milestones
  const developmentTimeline = [
    { status: "completed", date: "March 15, 2023", title: "Project Kickoff", description: "Initial planning and architecture design" },
    { status: "completed", date: "April 10, 2023", title: "Core Functionality", description: "Basic poll creation and voting system" },
    { status: "completed", date: "May 20, 2023", title: "WordPress Integration", description: "Plugin structure and WordPress API integration" },
    { status: "completed", date: "July 8, 2023", title: "User Interface", description: "Responsive design implementation and user experience improvements" },
    { status: "completed", date: "September 15, 2023", title: "Advanced Features", description: "Shortcode support, analytics, and specialized poll types" },
    { status: "in-progress", date: "Current", title: "Final Refinements", description: "Code refactoring, optimization, and documentation" }
  ];

  return (
    <div className="space-y-8">
      <div className="text-center mb-10">
        <h2 className="text-3xl font-bold mb-3 bg-gradient-to-r from-blue-600 to-purple-600 bg-clip-text text-transparent">Project Progress</h2>
        <p className="text-muted-foreground max-w-2xl mx-auto">
          Track the development of our polling system, from core functionality to advanced features.
        </p>
      </div>

      {/* Overall Progress */}
      <div className="mb-8">
        <div className="flex justify-between items-center mb-2">
          <h3 className="text-xl font-bold">Overall Completion</h3>
          <span className="text-2xl font-bold text-primary">{overallProgress}%</span>
        </div>
        <CustomProgress 
          value={overallProgress} 
          size="lg" 
          animated={true} 
          gradient={true}
          className="mb-2"
        />
        <p className="text-sm text-muted-foreground italic text-center mt-2">
          Project target completion date: December 2023
        </p>
      </div>

      {/* Component Progress Cards */}
      <div className="grid grid-cols-1 md:grid-cols-2 gap-4 mb-10">
        {progressData.map((item, index) => (
          <motion.div 
            key={index}
            initial={{ opacity: 0, y: 20 }}
            animate={{ opacity: 1, y: 0 }}
            transition={{ duration: 0.3, delay: index * 0.1 }}
            className="border rounded-lg p-4 bg-card shadow-sm"
          >
            <div className="flex justify-between items-center mb-2">
              <h4 className="font-medium">{item.label}</h4>
              <span className="font-semibold">{item.value}%</span>
            </div>
            <CustomProgress 
              value={item.value} 
              size="md" 
              className="mb-2"
              fillClassName={item.value === 100 ? "bg-green-500" : undefined}
            />
            <p className="text-xs text-muted-foreground mt-2">{item.description}</p>
          </motion.div>
        ))}
      </div>

      {/* Development Timeline */}
      <div className="mb-4">
        <h3 className="text-xl font-bold mb-4">Development Timeline</h3>
        <div className="space-y-4">
          {developmentTimeline.map((milestone, index) => (
            <div key={index} className="flex items-start">
              <div className={`mt-1 rounded-full p-1 ${
                milestone.status === "completed" ? "bg-green-500" : 
                milestone.status === "in-progress" ? "bg-blue-500" : "bg-gray-300"
              }`}>
                {milestone.status === "completed" ? (
                  <Check size={16} className="text-white" />
                ) : (
                  <div className="w-4 h-4" />
                )}
              </div>
              <div className="ml-4 pb-5 border-l border-dashed pl-4 border-muted-foreground/30">
                <div className="flex items-center mb-1">
                  <CalendarDays size={14} className="text-muted-foreground mr-2" />
                  <span className="text-xs text-muted-foreground">{milestone.date}</span>
                </div>
                <h4 className="font-medium">{milestone.title}</h4>
                <p className="text-sm text-muted-foreground">{milestone.description}</p>
              </div>
            </div>
          ))}
        </div>
      </div>

      {/* Feature Highlights */}
      <div className="grid grid-cols-1 sm:grid-cols-2 gap-4 mt-6">
        <div className="bg-primary/5 rounded-lg p-4">
          <div className="flex items-center mb-2">
            <FileText size={18} className="text-primary mr-2" />
            <h4 className="font-medium">Poll Creation</h4>
          </div>
          <ul className="text-sm space-y-1 text-muted-foreground">
            <li>• Multiple poll types support</li>
            <li>• Image-based options</li>
            <li>• Advanced settings control</li>
          </ul>
        </div>
        
        <div className="bg-primary/5 rounded-lg p-4">
          <div className="flex items-center mb-2">
            <ListChecks size={18} className="text-primary mr-2" />
            <h4 className="font-medium">Voting System</h4>
          </div>
          <ul className="text-sm space-y-1 text-muted-foreground">
            <li>• Real-time results display</li>
            <li>• IP-based vote restriction</li>
            <li>• Specialized renderers for different poll types</li>
          </ul>
        </div>
        
        <div className="bg-primary/5 rounded-lg p-4">
          <div className="flex items-center mb-2">
            <Code size={18} className="text-primary mr-2" />
            <h4 className="font-medium">WordPress Integration</h4>
          </div>
          <ul className="text-sm space-y-1 text-muted-foreground">
            <li>• Comprehensive shortcode system</li>
            <li>• Custom post types and taxonomies</li>
            <li>• Theme-compatible styling</li>
          </ul>
        </div>
        
        <div className="bg-primary/5 rounded-lg p-4">
          <div className="flex items-center mb-2">
            <Settings size={18} className="text-primary mr-2" />
            <h4 className="font-medium">Admin Features</h4>
          </div>
          <ul className="text-sm space-y-1 text-muted-foreground">
            <li>• Comprehensive dashboard</li>
            <li>• Role-based permissions</li>
            <li>• Code refactoring for maintainability</li>
          </ul>
        </div>
      </div>
    </div>
  );
};

export default ProjectProgress;
