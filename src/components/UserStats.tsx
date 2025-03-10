
import { useState } from "react";
import { Tabs, TabsContent, TabsList, TabsTrigger } from "@/components/ui/tabs";
import UserLevelProgress from "./user/UserLevelProgress";
import UserActivityStats from "./user/UserActivityStats";
import UserAchievements from "./user/UserAchievements";
import { UserStatsProps } from "./user/UserTypes";
import { getMockUserActivity } from "./user/userMockData";

const UserStats = ({ userId, userName }: UserStatsProps) => {
  const [userActivity, setUserActivity] = useState(getMockUserActivity());

  // In a real implementation, this would fetch data from the WordPress REST API
  // using the userId parameter

  return (
    <div className="user-stats-container space-y-6">
      <UserLevelProgress activity={userActivity} />

      <Tabs defaultValue="stats" className="w-full">
        <TabsList className="w-full grid grid-cols-2">
          <TabsTrigger value="stats">Activity Stats</TabsTrigger>
          <TabsTrigger value="achievements">Achievements</TabsTrigger>
        </TabsList>
        
        <TabsContent value="stats" className="mt-4">
          <UserActivityStats activity={userActivity} />
        </TabsContent>
        
        <TabsContent value="achievements" className="mt-4">
          <UserAchievements achievements={userActivity.achievements} />
        </TabsContent>
      </Tabs>
    </div>
  );
};

export default UserStats;
