
export interface Poll {
  id: string;
  title: string;
  description?: string;
  options: PollOption[];
  createdAt: string;
  createdBy: string;
  status: 'active' | 'closed';
  totalVotes: number;
  type?: string;
  endDate?: string;
  settings?: PollSettings;
}

export interface PollOption {
  id: string;
  text: string;
  votes: number;
  imageUrl?: string;
}

export interface PollVote {
  pollId: string;
  optionId: string;
  votedAt: string;
  ip?: string;
}

export interface PollFormData {
  title: string;
  description?: string;
  options: string[];
  optionImages?: string[];
  type?: string;
  endDate?: Date;
  settings?: PollSettings;
}

export interface PollSettings {
  showResults: boolean;
  resultsDisplay: 'bar' | 'pie' | 'donut' | 'text';
  allowComments: boolean;
}

export interface UserStats {
  userId: string;
  userName: string;
  totalPoints: number;
  voteCount: number;
  commentCount: number;
  ratingCount: number;
  pollCount: number;
  currentLevel: number;
  nextLevelPoints: number;
  progressPercentage: number;
  achievements: Achievement[];
}

export interface Achievement {
  id: string;
  title: string;
  description: string;
  icon: string;
  unlocked: boolean;
  dateUnlocked?: string;
}
