
import { useState, useEffect } from "react";
import { motion } from "framer-motion";
import { Poll, PollOption } from "@/lib/types";

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
            <div className="h-2 bg-secondary rounded-full overflow-hidden">
              <motion.div
                className={`h-full ${
                  voted === option.id ? "bg-primary" : "bg-primary/70"
                }`}
                initial={{ width: "0%" }}
                animate={{
                  width: animationComplete
                    ? `${getPercentage(option.votes)}%`
                    : "0%",
                }}
                transition={{
                  duration: 0.6,
                  delay: index * 0.1,
                  ease: "easeOut",
                }}
              />
            </div>
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
