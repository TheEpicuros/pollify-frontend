
export interface Poll {
  id: string;
  title: string;
  description?: string;
  options: PollOption[];
  createdAt: string;
  createdBy: string;
  status: 'active' | 'closed';
  totalVotes: number;
}

export interface PollOption {
  id: string;
  text: string;
  votes: number;
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
}
