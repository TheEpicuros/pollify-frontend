
import React from "react";
import { Clock } from "lucide-react";
import { CustomProgress } from "@/components/ui/custom-progress";

interface TopPollItemProps {
  title: string;
  votes: number;
  completionRate: number;
  timeAgo: string;
}

const TopPollItem: React.FC<TopPollItemProps> = ({ title, votes, completionRate, timeAgo }) => (
  <div className="border rounded-lg p-4 hover:bg-slate-50 dark:hover:bg-slate-800 transition-colors">
    <div className="flex items-center justify-between mb-2">
      <h4 className="font-medium">{title}</h4>
      <div className="flex items-center text-sm text-muted-foreground">
        <Clock className="h-3 w-3 mr-1" />
        {timeAgo}
      </div>
    </div>
    <div className="grid grid-cols-2 gap-4">
      <div>
        <p className="text-sm text-muted-foreground mb-1">Total Votes</p>
        <p className="font-semibold">{votes.toLocaleString()}</p>
      </div>
      <div>
        <p className="text-sm text-muted-foreground mb-1">Completion Rate</p>
        <div className="flex items-center gap-2">
          <CustomProgress 
            value={completionRate} 
            size="sm" 
            className="flex-1" 
            fillClassName={completionRate > 80 ? "bg-emerald-500" : completionRate > 60 ? "bg-amber-500" : "bg-rose-500"}
          />
          <span className="font-semibold text-sm">{completionRate}%</span>
        </div>
      </div>
    </div>
  </div>
);

export default TopPollItem;
