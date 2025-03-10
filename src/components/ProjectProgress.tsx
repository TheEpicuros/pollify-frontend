
import React, { useState } from "react";
import { Tabs, TabsContent, TabsList, TabsTrigger } from "@/components/ui/tabs";
import { Progress } from "@/components/ui/progress";
import { Card, CardContent, CardDescription, CardHeader, CardTitle, CardFooter } from "@/components/ui/card";
import { Badge } from "@/components/ui/badge";
import { motion } from "framer-motion";
import { ArrowUpRight, CheckCircle, Clock, AlertCircle } from "lucide-react";
import AnalyticsDashboard from "@/components/AnalyticsDashboard";

// Sample project tasks data
const tasks = [
  { 
    id: 1, 
    title: "Database Implementation",
    description: "Set up database tables and relations",
    status: "completed", 
    progress: 100,
    assignee: "Alex",
    priority: "high",
    dueDate: "2023-09-30" 
  },
  { 
    id: 2, 
    title: "User Authentication", 
    description: "Implement login and registration",
    status: "completed", 
    progress: 100,
    assignee: "Maria", 
    priority: "high",
    dueDate: "2023-10-15"
  },
  { 
    id: 3, 
    title: "Poll Creation Interface", 
    description: "Frontend for creating polls",
    status: "in-progress", 
    progress: 85,
    assignee: "John", 
    priority: "medium",
    dueDate: "2023-11-05"
  },
  { 
    id: 4, 
    title: "Results Visualization", 
    description: "Charts and graphs for poll results",
    status: "in-progress", 
    progress: 60,
    assignee: "Sarah", 
    priority: "medium",
    dueDate: "2023-11-20"
  },
  { 
    id: 5, 
    title: "Social Sharing", 
    description: "Implement sharing features",
    status: "planned", 
    progress: 0,
    assignee: "David", 
    priority: "low",
    dueDate: "2023-12-10"
  }
];

// Calculate overall project progress
const calculateOverallProgress = () => {
  const totalTasks = tasks.length;
  const totalProgress = tasks.reduce((acc, task) => acc + task.progress, 0);
  return Math.round(totalProgress / totalTasks);
};

const ProjectProgress = () => {
  const [activeTab, setActiveTab] = useState("overview");
  const overallProgress = calculateOverallProgress();

  // Group tasks by status
  const completedTasks = tasks.filter(task => task.status === "completed");
  const inProgressTasks = tasks.filter(task => task.status === "in-progress");
  const plannedTasks = tasks.filter(task => task.status === "planned");

  return (
    <div className="space-y-6">
      <Tabs value={activeTab} onValueChange={setActiveTab} className="w-full">
        <TabsList className="grid w-full grid-cols-2">
          <TabsTrigger value="overview">Project Overview</TabsTrigger>
          <TabsTrigger value="analytics">Analytics Dashboard</TabsTrigger>
        </TabsList>
        
        <TabsContent value="overview" className="space-y-6 mt-6">
          {/* Project Overview Header */}
          <div className="grid gap-6 md:grid-cols-2 lg:grid-cols-4">
            <Card>
              <CardHeader className="pb-2">
                <CardTitle className="text-sm font-medium">Overall Progress</CardTitle>
              </CardHeader>
              <CardContent>
                <div className="text-2xl font-bold">{overallProgress}%</div>
                <Progress value={overallProgress} className="h-2 mt-2" />
                <p className="text-xs text-muted-foreground mt-2">
                  {completedTasks.length} of {tasks.length} tasks completed
                </p>
              </CardContent>
            </Card>
            
            <Card>
              <CardHeader className="pb-2">
                <CardTitle className="text-sm font-medium">Tasks Completed</CardTitle>
              </CardHeader>
              <CardContent>
                <div className="text-2xl font-bold">{completedTasks.length}</div>
                <div className="flex items-center mt-2">
                  <div className="text-xs text-emerald-600 font-medium flex items-center">
                    <ArrowUpRight className="h-3 w-3 mr-1" />
                    +2 this week
                  </div>
                </div>
              </CardContent>
            </Card>
            
            <Card>
              <CardHeader className="pb-2">
                <CardTitle className="text-sm font-medium">In Progress</CardTitle>
              </CardHeader>
              <CardContent>
                <div className="text-2xl font-bold">{inProgressTasks.length}</div>
                <div className="flex items-center mt-2">
                  <div className="text-xs text-muted-foreground">
                    Expected completion: 2 weeks
                  </div>
                </div>
              </CardContent>
            </Card>
            
            <Card>
              <CardHeader className="pb-2">
                <CardTitle className="text-sm font-medium">Remaining Tasks</CardTitle>
              </CardHeader>
              <CardContent>
                <div className="text-2xl font-bold">{plannedTasks.length}</div>
                <div className="flex items-center mt-2">
                  <div className="text-xs text-muted-foreground">
                    Planned for next sprint
                  </div>
                </div>
              </CardContent>
            </Card>
          </div>
          
          {/* Tasks List */}
          <div className="space-y-4">
            <h3 className="text-lg font-semibold">Project Tasks</h3>
            {tasks.map((task) => (
              <TaskCard key={task.id} task={task} />
            ))}
          </div>
        </TabsContent>
        
        <TabsContent value="analytics" className="space-y-6 mt-6">
          <AnalyticsDashboard />
        </TabsContent>
      </Tabs>
    </div>
  );
};

// Task Card Component
interface TaskProps {
  task: {
    id: number;
    title: string;
    description: string;
    status: string;
    progress: number;
    assignee: string;
    priority: string;
    dueDate: string;
  };
}

const TaskCard = ({ task }: TaskProps) => {
  const statusColors = {
    "completed": "bg-green-500",
    "in-progress": "bg-blue-500",
    "planned": "bg-gray-500"
  };

  const priorityColors = {
    "high": "bg-red-500 text-white",
    "medium": "bg-yellow-500 text-black",
    "low": "bg-green-500 text-white"
  };

  const statusIcons = {
    "completed": <CheckCircle className="h-4 w-4 text-green-500" />,
    "in-progress": <Clock className="h-4 w-4 text-blue-500" />,
    "planned": <AlertCircle className="h-4 w-4 text-gray-500" />
  };

  const formatDate = (dateString: string) => {
    const date = new Date(dateString);
    return date.toLocaleDateString("en-US", { month: "short", day: "numeric", year: "numeric" });
  };

  return (
    <motion.div
      initial={{ opacity: 0, y: 10 }}
      animate={{ opacity: 1, y: 0 }}
      transition={{ duration: 0.3 }}
      className="border rounded-lg p-4 hover:shadow-md transition-shadow"
    >
      <div className="flex justify-between items-start mb-2">
        <div>
          <h4 className="font-semibold">{task.title}</h4>
          <p className="text-sm text-muted-foreground">{task.description}</p>
        </div>
        <Badge className={priorityColors[task.priority as keyof typeof priorityColors]}>
          {task.priority}
        </Badge>
      </div>
      
      <Progress value={task.progress} className="h-2 mt-4 mb-2" />
      
      <div className="flex justify-between items-center mt-4 text-sm">
        <div className="flex items-center">
          {statusIcons[task.status as keyof typeof statusIcons]}
          <span className="ml-1 capitalize">{task.status.replace("-", " ")}</span>
        </div>
        <div>Assigned to: <span className="font-medium">{task.assignee}</span></div>
        <div>Due: <span className="font-medium">{formatDate(task.dueDate)}</span></div>
      </div>
    </motion.div>
  );
};

export default ProjectProgress;
