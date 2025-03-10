
export interface UserActivity {
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

export interface Achievement {
  id: string;
  title: string;
  description: string;
  icon: string;
  unlocked: boolean;
  date_unlocked?: string;
}

export interface UserStatsProps {
  userId?: number;
  userName?: string;
}
