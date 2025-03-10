
import { PollType } from "@/lib/types";

// Helper function to get default options based on poll type
export const getDefaultOptionsForType = (type: PollType): string[] => {
  switch (type) {
    case "binary":
      return ["Yes", "No"];
    case "rating-scale":
      return ["1", "2", "3", "4", "5"];
    case "open-ended":
      return ["Open Response"];
    case "check-all":
      return ["Option 1", "Option 2", "Option 3"];
    case "ranked-choice":
      return ["Option 1", "Option 2", "Option 3", "Option 4"];
    case "image-based":
      return ["Image 1", "Image 2"];
    case "quiz":
      return ["Answer 1", "Answer 2", "Answer 3", "Answer 4"];
    default:
      return ["Option 1", "Option 2"];
  }
};
