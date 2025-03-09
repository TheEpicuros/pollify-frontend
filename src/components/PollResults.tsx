
import { useState, useEffect } from "react";
import { motion } from "framer-motion";
import { Poll, PollOption } from "@/lib/types";
import { CustomProgress } from "@/components/ui/custom-progress";

interface PollResultsProps {
  poll: Poll;
  voted?: string;
  animate?: boolean;
}

const PollResults = ({ poll, voted, animate = true }: PollResultsProps) => {
  const [animationComplete, setAnimationComplete] = useState(!animate);
  
  useEffect(() => {
    if (animate) {
      const timer = setTimeout(() => {
        setAnimationComplete(true);
      }, 600);
      return () => clearTimeout(timer);
    }
  }, [animate]);

  const getPercentage = (votes: number) => {
    if (poll.totalVotes === 0) return 0;
    return Math.round((votes / poll.totalVotes) * 100);
  };

  const sortedOptions = [...poll.options].sort((a, b) => b.votes - a.votes);

  return (
    <div className="space-y-4">
      <h3 className="text-lg font-medium text-center sm:text-left">Poll Results</h3>
      <div className="space-y-3">
        {sortedOptions.map((option, index) => (
          <div key={option.id} className="space-y-1">
            <div className="flex justify-between text-sm">
              <span className={voted === option.id ? "font-medium text-primary" : ""}>
                {option.text}
              </span>
              <span className="text-muted-foreground">
                {getPercentage(option.votes)}%
              </span>
            </div>
            
            <motion.div
              initial={{ opacity: 0 }}
              animate={{ opacity: animationComplete ? 1 : 0 }}
              transition={{ duration: 0.3, delay: index * 0.1 }}
            >
              <CustomProgress 
                value={getPercentage(option.votes)}
                size="md"
                fillClassName={voted === option.id ? "bg-primary" : "bg-primary/70"}
                animated={voted === option.id}
              />
            </motion.div>
            
            <div className="text-xs text-muted-foreground">
              {option.votes.toLocaleString()} votes
            </div>
          </div>
        ))}
      </div>
      <div className="pt-3 border-t text-sm text-center text-muted-foreground">
        Total: {poll.totalVotes.toLocaleString()} votes
      </div>
    </div>
  );
};

export default PollResults;
