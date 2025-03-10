
import React from "react";
import { motion } from "framer-motion";
import { Badge } from "@/components/ui/badge";
import { CheckCircle, Clock, AlertCircle } from "lucide-react";
import { CustomProgress } from "@/components/ui/custom-progress";

export interface TaskProps {
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
      
      <CustomProgress 
        value={task.progress} 
        className="h-2 mt-4 mb-2" 
        gradient={task.progress === 100}
        animated={task.progress < 100 && task.progress > 0}
      />
      
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

export default TaskCard;
