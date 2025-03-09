
import { Poll } from './types';

export const mockPolls: Poll[] = [
  {
    id: '1',
    title: 'What frontend framework do you prefer?',
    description: 'Help us understand the most popular frontend technologies in 2024',
    options: [
      { id: '1-1', text: 'React', votes: 1420 },
      { id: '1-2', text: 'Vue.js', votes: 980 },
      { id: '1-3', text: 'Angular', votes: 850 },
      { id: '1-4', text: 'Svelte', votes: 640 }
    ],
    createdAt: '2024-04-01T10:30:00Z',
    createdBy: 'John Doe',
    status: 'active',
    totalVotes: 3890
  },
  {
    id: '2',
    title: 'How many hours do you code per day?',
    description: 'We\'re curious about developer habits',
    options: [
      { id: '2-1', text: 'Less than 2 hours', votes: 320 },
      { id: '2-2', text: '2-4 hours', votes: 760 },
      { id: '2-3', text: '4-8 hours', votes: 1240 },
      { id: '2-4', text: 'More than 8 hours', votes: 580 }
    ],
    createdAt: '2024-04-05T14:15:00Z',
    createdBy: 'Jane Smith',
    status: 'active',
    totalVotes: 2900
  },
  {
    id: '3',
    title: 'What\'s your favorite code editor?',
    description: '',
    options: [
      { id: '3-1', text: 'VS Code', votes: 2340 },
      { id: '3-2', text: 'IntelliJ/WebStorm', votes: 1260 },
      { id: '3-3', text: 'Sublime Text', votes: 890 },
      { id: '3-4', text: 'Vim/Neovim', votes: 780 },
      { id: '3-5', text: 'Other', votes: 230 }
    ],
    createdAt: '2024-04-08T09:45:00Z',
    createdBy: 'Alex Johnson',
    status: 'active',
    totalVotes: 5500
  },
  {
    id: '4',
    title: 'Do you prefer working remotely or in office?',
    description: 'Post-pandemic work preferences survey',
    options: [
      { id: '4-1', text: 'Fully remote', votes: 1870 },
      { id: '4-2', text: 'Hybrid (mix of remote and office)', votes: 2340 },
      { id: '4-3', text: 'Fully in office', votes: 790 }
    ],
    createdAt: '2024-04-10T11:20:00Z',
    createdBy: 'Sarah Williams',
    status: 'active',
    totalVotes: 5000
  },
  {
    id: '5',
    title: 'What\'s your preferred deployment platform?',
    description: 'Share your go-to deployment solution',
    options: [
      { id: '5-1', text: 'Vercel', votes: 1560 },
      { id: '5-2', text: 'AWS', votes: 1890 },
      { id: '5-3', text: 'Netlify', votes: 1340 },
      { id: '5-4', text: 'Google Cloud', votes: 980 },
      { id: '5-5', text: 'Digital Ocean', votes: 760 },
      { id: '5-6', text: 'Other', votes: 470 }
    ],
    createdAt: '2024-04-12T16:30:00Z',
    createdBy: 'Michael Brown',
    status: 'active',
    totalVotes: 7000
  }
];

export const getPoll = (id: string): Poll | undefined => {
  return mockPolls.find(poll => poll.id === id);
};

export const getAllPolls = (): Poll[] => {
  return [...mockPolls].sort((a, b) => 
    new Date(b.createdAt).getTime() - new Date(a.createdAt).getTime()
  );
};

export const voteOnPoll = (pollId: string, optionId: string): boolean => {
  const poll = mockPolls.find(p => p.id === pollId);
  if (!poll) return false;
  
  const option = poll.options.find(o => o.id === optionId);
  if (!option) return false;
  
  option.votes += 1;
  poll.totalVotes += 1;
  
  return true;
};

export const createPoll = (title: string, description: string, options: string[]): Poll => {
  const newId = (mockPolls.length + 1).toString();
  
  const newPoll: Poll = {
    id: newId,
    title,
    description,
    options: options.map((text, index) => ({
      id: `${newId}-${index + 1}`,
      text,
      votes: 0
    })),
    createdAt: new Date().toISOString(),
    createdBy: 'Current User',
    status: 'active',
    totalVotes: 0
  };
  
  mockPolls.push(newPoll);
  return newPoll;
};
