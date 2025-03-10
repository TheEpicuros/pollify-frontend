
import React, { useState } from "react";
import { Card, CardContent, CardDescription, CardHeader, CardTitle } from "@/components/ui/card";
import { Tabs, TabsContent, TabsList, TabsTrigger } from "@/components/ui/tabs";
import { BarChartIcon, Activity, Users, CheckCircle2 } from "lucide-react";

// Import the tab components
import EngagementTab from "./tabs/EngagementTab";
import DemographicsTab from "./tabs/DemographicsTab";
import BehaviorTab from "./tabs/BehaviorTab";
import MetricCard from "./components/MetricCard";
import TopPollItem from "./components/TopPollItem";

const AdvancedAnalytics: React.FC = () => {
  const [timeRange, setTimeRange] = useState("week");

  return (
    <div className="space-y-6">
      <Card>
        <CardHeader>
          <CardTitle>User Engagement Analytics</CardTitle>
          <CardDescription>
            Detailed insights about user activity and engagement
          </CardDescription>
        </CardHeader>
        <CardContent>
          <Tabs defaultValue="engagement" className="w-full">
            <TabsList className="grid w-full grid-cols-3">
              <TabsTrigger value="engagement">Engagement Metrics</TabsTrigger>
              <TabsTrigger value="demographics">User Demographics</TabsTrigger>
              <TabsTrigger value="behavior">User Behavior</TabsTrigger>
            </TabsList>

            <TabsContent value="engagement" className="space-y-6">
              <EngagementTab />
            </TabsContent>

            <TabsContent value="demographics" className="space-y-6">
              <DemographicsTab />
            </TabsContent>

            <TabsContent value="behavior" className="space-y-6">
              <BehaviorTab />
            </TabsContent>
          </Tabs>
        </CardContent>
      </Card>
    </div>
  );
};

export default AdvancedAnalytics;
