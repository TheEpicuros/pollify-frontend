
import { motion } from "framer-motion";
import { 
  Award, 
  BarChart4, 
  MessageSquare, 
  ThumbsUp, 
  Vote, 
  Star
} from "lucide-react";
import { Achievement } from "./UserTypes";

interface UserAchievementsProps {
  achievements: Achievement[];
}

export const getAchievementIcon = (icon: string) => {
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

const UserAchievements = ({ achievements }: UserAchievementsProps) => {
  return (
    <div className="space-y-4">
      {achievements.map((achievement) => (
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
  );
};

export default UserAchievements;
