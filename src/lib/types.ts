
export interface Poll {
  id: string;
  title: string;
  description?: string;
  options: PollOption[];
  createdAt: string;
  createdBy: string;
  status: 'active' | 'closed';
  totalVotes: number;
  type?: PollType;
  endDate?: string;
  settings?: PollSettings;
}

export type PollType = 
  | 'binary' 
  | 'multiple-choice' 
  | 'check-all' 
  | 'ranked-choice' 
  | 'rating-scale' 
  | 'open-ended' 
  | 'image-based'
  | 'quiz'
  | 'opinion'
  | 'straw'
  | 'interactive'
  | 'referendum';

export interface PollOption {
  id: string;
  text: string;
  votes: number;
  imageUrl?: string;
  isCorrect?: boolean; // For quiz type polls
  order?: number; // For ranked polls
  rating?: number; // For rating polls
}

export interface PollVote {
  pollId: string;
  optionId: string | string[]; // Allow multiple options for check-all polls
  votedAt: string;
  ip?: string;
  userId?: string;
  rating?: number; // For rating-based polls
  openResponse?: string; // For open-ended polls
}

export interface PollFormData {
  title: string;
  description?: string;
  options: string[];
  optionImages?: string[];
  type?: PollType;
  endDate?: Date;
  settings?: PollSettings;
  correctAnswers?: string[]; // For quiz type polls
}

export interface PollSettings {
  showResults: boolean;
  resultsDisplay: 'bar' | 'pie' | 'donut' | 'text';
  allowComments: boolean;
  allowMultipleVotes?: boolean; // For polls that allow voting more than once
  requireLogin?: boolean; // Whether voting requires user to be logged in
  isAnonymous?: boolean; // Whether to track who voted
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

export interface PollTypeInfo {
  slug: string;
  name: string;
  description: string;
  icon: string;
  features: string[];
}

export interface PollDisplayOptions {
  showResults?: boolean;
  showSocial?: boolean;
  showRatings?: boolean;
  showComments?: boolean;
  display?: 'bar' | 'pie' | 'donut' | 'text';
  width?: string;
  align?: 'left' | 'center' | 'right';
}
