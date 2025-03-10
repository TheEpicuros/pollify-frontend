
// Task data structure
export interface Task {
  id: number;
  title: string;
  description: string;
  status: string;
  progress: number;
  assignee: string;
  priority: string;
  dueDate: string;
}

// Updated project tasks data with accurate completion status
export const tasks: Task[] = [
  { 
    id: 1, 
    title: "Core Poll Plugin Architecture",
    description: "WordPress plugin foundation and React integration",
    status: "completed", 
    progress: 100,
    assignee: "Dev Team",
    priority: "high",
    dueDate: "2023-09-30" 
  },
  { 
    id: 2, 
    title: "Poll Creation Interface", 
    description: "Form components for creating different poll types",
    status: "completed", 
    progress: 100,
    assignee: "Frontend Team", 
    priority: "high",
    dueDate: "2023-10-15"
  },
  { 
    id: 3, 
    title: "Poll Type Implementation", 
    description: "Support for multiple poll types (quiz, rating, etc.)",
    status: "completed", 
    progress: 100,
    assignee: "Dev Team", 
    priority: "high",
    dueDate: "2023-11-05"
  },
  { 
    id: 4, 
    title: "Results Visualization", 
    description: "Charts and graphs for poll results",
    status: "completed", 
    progress: 95,
    assignee: "UI Team", 
    priority: "medium",
    dueDate: "2023-11-20"
  },
  { 
    id: 5, 
    title: "Advanced Analytics", 
    description: "Detailed statistics and user insights",
    status: "in-progress", 
    progress: 70,
    assignee: "Data Team", 
    priority: "medium",
    dueDate: "2023-12-10"
  },
  {
    id: 6,
    title: "Social Sharing Integration",
    description: "Share polls across social media platforms",
    status: "in-progress",
    progress: 60,
    assignee: "Frontend Team",
    priority: "low",
    dueDate: "2024-01-05"
  },
  {
    id: 7,
    title: "User Dashboard Refinement",
    description: "Enhanced user poll management interface",
    status: "in-progress",
    progress: 40,
    assignee: "UX Team",
    priority: "medium",
    dueDate: "2024-01-20"
  }
];

// Calculate overall project progress
export const calculateOverallProgress = () => {
  const totalTasks = tasks.length;
  const totalProgress = tasks.reduce((acc, task) => acc + task.progress, 0);
  return Math.round(totalProgress / totalTasks);
};

// Group tasks by status
export const getCompletedTasks = () => tasks.filter(task => task.status === "completed");
export const getInProgressTasks = () => tasks.filter(task => task.status === "in-progress");
export const getPlannedTasks = () => tasks.filter(task => task.status === "planned");
