
import { motion } from "framer-motion";
import { Progress } from "@/components/ui/progress";
import { UserActivity } from "./UserTypes";

interface UserLevelProgressProps {
  activity: UserActivity;
}

const UserLevelProgress = ({ activity }: UserLevelProgressProps) => {
  return (
    <motion.div 
      initial={{ opacity: 0, y: 10 }}
      animate={{ opacity: 1, y: 0 }}
      transition={{ duration: 0.3 }}
      className="user-level-progress"
    >
      <div className="flex justify-between items-center mb-2">
        <h3 className="text-lg font-medium">Level {activity.current_level}</h3>
        <span className="text-sm text-muted-foreground">
          {activity.total_points} / {activity.next_level_points} points
        </span>
      </div>
      <Progress value={activity.progress_percentage} className="h-2" />
      <p className="text-sm text-muted-foreground mt-2">
        {activity.next_level_points - activity.total_points} points until next level
      </p>
    </motion.div>
  );
};

export default UserLevelProgress;
