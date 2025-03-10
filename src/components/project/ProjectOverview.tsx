
import React from "react";
import { Card, CardContent, CardHeader, CardTitle } from "@/components/ui/card";
import { CustomProgress } from "@/components/ui/custom-progress";
import { ArrowUpRight, CheckCircle, Clock } from "lucide-react";
import TaskCard from "./TaskCard";
import { tasks, calculateOverallProgress, getCompletedTasks, getInProgressTasks, getPlannedTasks } from "./ProjectData";

const ProjectOverview: React.FC = () => {
  const overallProgress = calculateOverallProgress();
  const completedTasks = getCompletedTasks();
  const inProgressTasks = getInProgressTasks();
  const plannedTasks = getPlannedTasks();

  return (
    <div className="space-y-6">
      {/* Project Overview Header */}
      <div className="grid gap-6 md:grid-cols-2 lg:grid-cols-4">
        <Card>
          <CardHeader className="pb-2">
            <CardTitle className="text-sm font-medium">Overall Progress</CardTitle>
          </CardHeader>
          <CardContent>
            <div className="text-2xl font-bold">{overallProgress}%</div>
            <CustomProgress 
              value={overallProgress} 
              className="h-2 mt-2" 
              gradient={true}
              animated={overallProgress < 100}
            />
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
                <CheckCircle className="h-3 w-3 mr-1" />
                Core functionality ready
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
              <div className="text-xs text-amber-600 font-medium flex items-center">
                <Clock className="h-3 w-3 mr-1" />
                Enhancements underway
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
              <div className="text-xs text-blue-600 font-medium flex items-center">
                <ArrowUpRight className="h-3 w-3 mr-1" />
                Ready for testing
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
    </div>
  );
};

export default ProjectOverview;
