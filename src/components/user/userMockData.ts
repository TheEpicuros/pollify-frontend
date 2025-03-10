
import { UserActivity } from "./UserTypes";

export const getMockUserActivity = (): UserActivity => {
  return {
    total_points: 258,
    vote_count: 42,
    comment_count: 17,
    rating_count: 23,
    poll_count: 5,
    current_level: 3,
    next_level_points: 300,
    progress_percentage: 86,
    achievements: [
      {
        id: "first_vote",
        title: "First Vote",
        description: "Cast your first vote on a poll",
        icon: "vote",
        unlocked: true,
        date_unlocked: "2023-10-15"
      },
      {
        id: "community_voice",
        title: "Community Voice",
        description: "Leave 10 comments on polls",
        icon: "message",
        unlocked: true,
        date_unlocked: "2023-11-02"
      },
      {
        id: "poll_creator",
        title: "Poll Creator",
        description: "Create your first poll",
        icon: "chart",
        unlocked: true,
        date_unlocked: "2023-11-12"
      },
      {
        id: "popular_poll",
        title: "Popular Opinion",
        description: "Create a poll with 50+ votes",
        icon: "star",
        unlocked: false
      },
      {
        id: "poll_expert",
        title: "Poll Expert",
        description: "Earn 500 points in the system",
        icon: "award",
        unlocked: false
      }
    ]
  };
};
