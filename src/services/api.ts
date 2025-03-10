
import { PollFormData, Poll, PollOption } from "@/lib/types";

const API_ENDPOINT = "/wp-json/pollify/v1";

// Function to convert API poll data to our Poll interface
const formatPoll = (pollData: any): Poll => {
  return {
    id: pollData.id.toString(),
    title: pollData.title,
    description: pollData.description || '',
    options: pollData.options.map((opt: any): PollOption => ({
      id: opt.id.toString(),
      text: opt.text,
      votes: opt.votes,
      imageUrl: opt.image_url
    })),
    createdAt: pollData.created_at,
    createdBy: pollData.created_by,
    status: pollData.status,
    totalVotes: pollData.total_votes,
    type: pollData.type,
    endDate: pollData.end_date,
    settings: {
      showResults: Boolean(pollData.settings?.show_results),
      resultsDisplay: pollData.settings?.results_display || 'bar',
      allowComments: Boolean(pollData.settings?.allow_comments),
    }
  };
};

// Function to fetch all polls from the backend
export const fetchPolls = async (): Promise<Poll[]> => {
  try {
    const response = await fetch(`${API_ENDPOINT}/polls`);
    
    if (!response.ok) {
      throw new Error(`API error: ${response.status}`);
    }
    
    const data = await response.json();
    return data.polls.map(formatPoll);
  } catch (error) {
    console.error("Error fetching polls:", error);
    // Return mocked data when API fails or during development
    return import('./data').then(module => module.getAllPolls());
  }
};

// Function to fetch a single poll by ID
export const fetchPoll = async (id: string): Promise<Poll | undefined> => {
  try {
    const response = await fetch(`${API_ENDPOINT}/polls/${id}`);
    
    if (!response.ok) {
      throw new Error(`API error: ${response.status}`);
    }
    
    const data = await response.json();
    return formatPoll(data.poll);
  } catch (error) {
    console.error(`Error fetching poll ${id}:`, error);
    // Return mocked data when API fails or during development
    return import('./data').then(module => module.getPoll(id));
  }
};

// Function to create a new poll
export const createPollApi = async (formData: PollFormData): Promise<Poll> => {
  try {
    // In a real application, you'd convert image data to file uploads here
    const pollData = {
      title: formData.title,
      description: formData.description || '',
      options: formData.options.map((text, index) => ({
        text,
        image_data: formData.optionImages && formData.optionImages[index]
      })),
      type: formData.type,
      end_date: formData.endDate ? formData.endDate.toISOString() : null,
      settings: {
        show_results: formData.settings?.showResults,
        results_display: formData.settings?.resultsDisplay,
        allow_comments: formData.settings?.allowComments,
      }
    };
    
    const response = await fetch(`${API_ENDPOINT}/polls`, {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json',
      },
      body: JSON.stringify(pollData),
    });
    
    if (!response.ok) {
      throw new Error(`API error: ${response.status}`);
    }
    
    const data = await response.json();
    return formatPoll(data.poll);
  } catch (error) {
    console.error("Error creating poll:", error);
    // Use mock function when API fails or during development
    return import('./data').then(module => module.createPoll(formData));
  }
};

// Function to vote on a poll
export const voteOnPollApi = async (pollId: string, optionId: string): Promise<boolean> => {
  try {
    const response = await fetch(`${API_ENDPOINT}/polls/${pollId}/vote`, {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json',
      },
      body: JSON.stringify({ option_id: optionId }),
    });
    
    if (!response.ok) {
      throw new Error(`API error: ${response.status}`);
    }
    
    return true;
  } catch (error) {
    console.error(`Error voting on poll ${pollId}:`, error);
    // Use mock function when API fails or during development
    return import('./data').then(module => module.voteOnPoll(pollId, optionId));
  }
};
