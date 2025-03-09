
import { Link } from "react-router-dom";
import { motion } from "framer-motion";
import { formatDistanceToNow } from "date-fns";
import { Poll } from "@/lib/types";
import { BarChart2, Clock, Users } from "lucide-react";

interface PollCardProps {
  poll: Poll;
  index: number;
}

const PollCard = ({ poll, index }: PollCardProps) => {
  const created = new Date(poll.createdAt);
  const timeAgo = formatDistanceToNow(created, { addSuffix: true });

  return (
    <motion.div
      initial={{ opacity: 0, y: 20 }}
      animate={{ opacity: 1, y: 0 }}
      transition={{ duration: 0.3, delay: index * 0.1 }}
      className="glass-card rounded-xl overflow-hidden"
    >
      <Link to={`/poll/${poll.id}`} className="block p-6 group">
        <div className="flex items-start justify-between">
          <div className="space-y-1.5">
            <span className="inline-block text-xs font-medium text-primary/80 bg-primary/5 px-2.5 py-0.5 rounded-full">
              {poll.status === "active" ? "Active Poll" : "Closed Poll"}
            </span>
            <h3 className="text-lg font-medium group-hover:text-primary transition-colors duration-200">
              {poll.title}
            </h3>
          </div>
          <div className="relative">
            <motion.div
              whileHover={{ rotate: 15 }}
              transition={{ type: "spring", stiffness: 400, damping: 10 }}
            >
              <BarChart2 className="text-primary h-5 w-5" />
            </motion.div>
          </div>
        </div>

        {poll.description && (
          <p className="mt-2 text-sm text-muted-foreground line-clamp-2">
            {poll.description}
          </p>
        )}

        <div className="mt-4 text-xs text-muted-foreground flex items-center flex-wrap gap-3">
          <div className="flex items-center">
            <Clock className="h-3.5 w-3.5 mr-1 inline" />
            {timeAgo}
          </div>
          <div className="flex items-center">
            <Users className="h-3.5 w-3.5 mr-1 inline" />
            {poll.totalVotes.toLocaleString()} votes
          </div>
          <div className="flex items-center">
            {poll.options.length} options
          </div>
        </div>

        <div className="mt-4 pt-4 border-t flex justify-end">
          <div className="text-sm group-hover:translate-x-0.5 transition-transform duration-200">
            View poll →
          </div>
        </div>
      </Link>
    </motion.div>
  );
};

export default PollCard;
