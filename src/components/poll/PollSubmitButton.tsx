
import { CheckCircle } from "lucide-react";

interface PollSubmitButtonProps {
  isSubmitting: boolean;
}

const PollSubmitButton = ({ isSubmitting }: PollSubmitButtonProps) => {
  return (
    <button
      type="submit"
      disabled={isSubmitting}
      className="w-full bg-primary text-primary-foreground font-medium py-2.5 px-4 rounded-lg hover:bg-primary/90 focus:outline-none focus:ring-2 focus:ring-primary/20 transition-colors disabled:opacity-70 flex items-center justify-center"
    >
      {isSubmitting ? (
        <>
          <svg
            className="animate-spin -ml-1 mr-2 h-4 w-4 text-primary-foreground"
            xmlns="http://www.w3.org/2000/svg"
            fill="none"
            viewBox="0 0 24 24"
          >
            <circle
              className="opacity-25"
              cx="12"
              cy="12"
              r="10"
              stroke="currentColor"
              strokeWidth="4"
            ></circle>
            <path
              className="opacity-75"
              fill="currentColor"
              d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"
            ></path>
          </svg>
          Creating Poll...
        </>
      ) : (
        <>
          <CheckCircle size={18} className="mr-2" />
          Create Poll
        </>
      )}
    </button>
  );
};

export default PollSubmitButton;
