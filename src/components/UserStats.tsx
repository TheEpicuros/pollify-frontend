
import { useState } from "react";
import { motion } from "framer-motion";
import { 
  Award, 
  BarChart4, 
  MessageSquare, 
  ThumbsUp, 
  Vote, 
  Star
} from "lucide-react";
import { Progress } from "@/components/ui/progress";
import { Card, CardContent, CardHeader, CardTitle } from "@/components/ui/card";
import { Tabs, TabsContent, TabsList, TabsTrigger } from "@/components/ui/tabs";

interface UserStatsProps {
  userId?: number;
  userName?: string;
}

interface UserActivity {
  total_points: number;
  vote_count: number;
  comment_count: number;
  rating_count: number;
  poll_count?: number;
  current_level: number;
  next_level_points: number;
  progress_percentage: number;
  achievements: Achievement[];
}

interface Achievement {
  id: string;
  title: string;
  description: string;
  icon: string;
  unlocked: boolean;
  date_unlocked?: string;
}

const UserStats = ({ userId, userName }: UserStatsProps) => {
  const [userActivity, setUserActivity] = useState<UserActivity>({
    total_points: 258,
    vote_count: 42,
    comment_count: 17,
    rating_count: 23,
    poll_count: 5,
    current_level: 3,
    next_level_points: 300,
    progress_percentage: 86,
    achievements: [
      {
        id: "first_vote",
        title: "First Vote",
        description: "Cast your first vote on a poll",
        icon: "vote",
        unlocked: true,
        date_unlocked: "2023-10-15"
      },
      {
        id: "community_voice",
        title: "Community Voice",
        description: "Leave 10 comments on polls",
        icon: "message",
        unlocked: true,
        date_unlocked: "2023-11-02"
      },
      {
        id: "poll_creator",
        title: "Poll Creator",
        description: "Create your first poll",
        icon: "chart",
        unlocked: true,
        date_unlocked: "2023-11-12"
      },
      {
        id: "popular_poll",
        title: "Popular Opinion",
        description: "Create a poll with 50+ votes",
        icon: "star",
        unlocked: false
      },
      {
        id: "poll_expert",
        title: "Poll Expert",
        description: "Earn 500 points in the system",
        icon: "award",
        unlocked: false
      }
    ]
  });

  // In a real implementation, this would fetch data from the WordPress REST API
  // using the userId parameter

  const getAchievementIcon = (icon: string) => {
    switch (icon) {
      case "vote":
        return <Vote size={20} />;
      case "message":
        return <MessageSquare size={20} />;
      case "chart":
        return <BarChart4 size={20} />;
      case "thumbs":
        return <ThumbsUp size={20} />;
      case "star":
        return <Star size={20} />;
      case "award":
      default:
        return <Award size={20} />;
    }
  };

  return (
    <div className="user-stats-container space-y-6">
      <motion.div 
        initial={{ opacity: 0, y: 10 }}
        animate={{ opacity: 1, y: 0 }}
        transition={{ duration: 0.3 }}
        className="user-level-progress"
      >
        <div className="flex justify-between items-center mb-2">
          <h3 className="text-lg font-medium">Level {userActivity.current_level}</h3>
          <span className="text-sm text-muted-foreground">
            {userActivity.total_points} / {userActivity.next_level_points} points
          </span>
        </div>
        <Progress value={userActivity.progress_percentage} className="h-2" />
        <p className="text-sm text-muted-foreground mt-2">
          {userActivity.next_level_points - userActivity.total_points} points until next level
        </p>
      </motion.div>

      <Tabs defaultValue="stats" className="w-full">
        <TabsList className="w-full grid grid-cols-2">
          <TabsTrigger value="stats">Activity Stats</TabsTrigger>
          <TabsTrigger value="achievements">Achievements</TabsTrigger>
        </TabsList>
        
        <TabsContent value="stats" className="mt-4">
          <div className="grid grid-cols-1 md:grid-cols-2 gap-4">
            <Card>
              <CardHeader className="pb-2">
                <CardTitle className="text-sm font-medium">Polls Created</CardTitle>
              </CardHeader>
              <CardContent>
                <div className="text-2xl font-bold">{userActivity.poll_count}</div>
              </CardContent>
            </Card>
            
            <Card>
              <CardHeader className="pb-2">
                <CardTitle className="text-sm font-medium">Votes Cast</CardTitle>
              </CardHeader>
              <CardContent>
                <div className="text-2xl font-bold">{userActivity.vote_count}</div>
              </CardContent>
            </Card>
            
            <Card>
              <CardHeader className="pb-2">
                <CardTitle className="text-sm font-medium">Comments</CardTitle>
              </CardHeader>
              <CardContent>
                <div className="text-2xl font-bold">{userActivity.comment_count}</div>
              </CardContent>
            </Card>
            
            <Card>
              <CardHeader className="pb-2">
                <CardTitle className="text-sm font-medium">Ratings</CardTitle>
              </CardHeader>
              <CardContent>
                <div className="text-2xl font-bold">{userActivity.rating_count}</div>
              </CardContent>
            </Card>
          </div>
        </TabsContent>
        
        <TabsContent value="achievements" className="mt-4">
          <div className="space-y-4">
            {userActivity.achievements.map((achievement) => (
              <motion.div
                key={achievement.id}
                initial={{ opacity: 0, y: 10 }}
                animate={{ opacity: 1, y: 0 }}
                className={`p-4 border rounded-lg flex items-start gap-4 ${
                  achievement.unlocked 
                    ? "border-primary/30 bg-primary/5" 
                    : "border-muted bg-muted/10 opacity-70"
                }`}
              >
                <div className={`p-2 rounded-full ${
                  achievement.unlocked ? "bg-primary/10 text-primary" : "bg-muted text-muted-foreground"
                }`}>
                  {getAchievementIcon(achievement.icon)}
                </div>
                <div>
                  <h4 className="font-medium">{achievement.title}</h4>
                  <p className="text-sm text-muted-foreground">{achievement.description}</p>
                  {achievement.unlocked && achievement.date_unlocked && (
                    <p className="text-xs text-muted-foreground mt-1">
                      Unlocked: {achievement.date_unlocked}
                    </p>
                  )}
                </div>
              </motion.div>
            ))}
          </div>
        </TabsContent>
      </Tabs>
    </div>
  );
};

export default UserStats;
