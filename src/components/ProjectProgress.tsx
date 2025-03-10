
import React, { useState } from "react";
import { Tabs, TabsContent, TabsList, TabsTrigger } from "@/components/ui/tabs";
import AnalyticsDashboard from "@/components/AnalyticsDashboard";
import ProjectOverview from "./project/ProjectOverview";

const ProjectProgress = () => {
  const [activeTab, setActiveTab] = useState("overview");

  return (
    <div className="space-y-6">
      <Tabs value={activeTab} onValueChange={setActiveTab} className="w-full">
        <TabsList className="grid w-full grid-cols-2">
          <TabsTrigger value="overview">Project Overview</TabsTrigger>
          <TabsTrigger value="analytics">Analytics Dashboard</TabsTrigger>
        </TabsList>
        
        <TabsContent value="overview" className="space-y-6 mt-6">
          <ProjectOverview />
        </TabsContent>
        
        <TabsContent value="analytics" className="space-y-6 mt-6">
          <AnalyticsDashboard />
        </TabsContent>
      </Tabs>
    </div>
  );
};

export default ProjectProgress;
