
import { useQuery, useMutation, useQueryClient } from '@tanstack/react-query';
import { fetchPolls, fetchPoll, createPollApi, voteOnPollApi } from '@/services/api';
import { Poll, PollFormData } from '@/lib/types';
import { toast } from 'sonner';

export function usePolls() {
  return useQuery({
    queryKey: ['polls'],
    queryFn: fetchPolls,
  });
}

export function usePoll(id: string) {
  return useQuery({
    queryKey: ['poll', id],
    queryFn: () => fetchPoll(id),
    enabled: !!id, // Only run the query if id is defined
  });
}

export function useCreatePoll() {
  const queryClient = useQueryClient();
  
  return useMutation({
    mutationFn: (formData: PollFormData) => createPollApi(formData),
    onSuccess: (newPoll) => {
      // Invalidate and refetch polls list
      queryClient.invalidateQueries({ queryKey: ['polls'] });
      
      // Add the new poll to the cache
      queryClient.setQueryData(['poll', newPoll.id], newPoll);
      
      toast.success('Poll created successfully');
    },
    onError: (error) => {
      console.error('Error creating poll:', error);
      toast.error('Failed to create poll');
    }
  });
}

export function useVoteOnPoll(pollId: string) {
  const queryClient = useQueryClient();
  
  return useMutation({
    mutationFn: (optionId: string) => voteOnPollApi(pollId, optionId),
    onSuccess: () => {
      // Invalidate and refetch the specific poll
      queryClient.invalidateQueries({ queryKey: ['poll', pollId] });
      toast.success('Vote recorded successfully');
    },
    onError: (error) => {
      console.error('Error voting on poll:', error);
      toast.error('Failed to record vote');
    }
  });
}
