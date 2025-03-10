
import { PollFormData, Poll } from "@/lib/types";

// This file contains functions to handle WordPress-specific data transformations

/**
 * Converts a WordPress poll CPT (Custom Post Type) to our Poll interface
 */
export const wordpressPollToPoll = (wpPoll: any): Poll => {
  // Extract poll option data from meta fields
  const options = (wpPoll._poll_options || []).map((text: string, index: number) => {
    const optionId = `${wpPoll.id}-${index + 1}`;
    
    // For image-based polls, get the image URL
    let imageUrl;
    if (wpPoll._poll_type === 'image-based' && wpPoll._poll_option_images && wpPoll._poll_option_images[index]) {
      imageUrl = wpPoll._poll_option_images[index];
    }
    
    return {
      id: optionId,
      text,
      votes: wpPoll._poll_votes?.[optionId] || 0,
      imageUrl
    };
  });
  
  // Calculate total votes
  const totalVotes = options.reduce((sum, option) => sum + option.votes, 0);
  
  return {
    id: wpPoll.id.toString(),
    title: wpPoll.title.rendered || wpPoll.title,
    description: wpPoll.content.rendered || wpPoll.content || '',
    options,
    createdAt: wpPoll.date,
    createdBy: wpPoll.author_name || 'WordPress User',
    status: wpPoll._poll_end_date && new Date(wpPoll._poll_end_date) < new Date() ? 'closed' : 'active',
    totalVotes,
    type: wpPoll._poll_type || 'multiple-choice',
    endDate: wpPoll._poll_end_date,
    settings: {
      showResults: wpPoll._poll_show_results === '1',
      resultsDisplay: wpPoll._poll_results_display || 'bar',
      allowComments: wpPoll._poll_allow_comments === '1',
    }
  };
};

/**
 * Converts our PollFormData to WordPress poll CPT data
 */
export const pollFormDataToWordPress = (formData: PollFormData): any => {
  // Convert form data to WordPress format
  const wpPollData: any = {
    title: formData.title,
    content: formData.description || '',
    status: 'publish',
    meta: {
      _poll_type: formData.type || 'multiple-choice',
      _poll_options: formData.options,
      _poll_end_date: formData.endDate ? formData.endDate.toISOString() : '',
      _poll_show_results: formData.settings?.showResults ? '1' : '0',
      _poll_results_display: formData.settings?.resultsDisplay || 'bar',
      _poll_allow_comments: formData.settings?.allowComments ? '1' : '0',
      _poll_allowed_roles: ['all']
    }
  };
  
  // Handle image-based polls
  if (formData.type === 'image-based' && formData.optionImages) {
    // In a real implementation, this would handle actual file uploads
    // For now, we'll just include the image data
    wpPollData.meta._poll_option_images = formData.optionImages;
  }
  
  return wpPollData;
};
