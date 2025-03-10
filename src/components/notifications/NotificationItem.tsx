
import React from "react";
import { cn } from "@/lib/utils";
import { Circle } from "lucide-react";
import { formatDistanceToNow } from "date-fns";

export interface Notification {
  id: string;
  message: string;
  timestamp: Date;
  read: boolean;
  type: "vote" | "comment" | "share" | "achievement";
  pollId?: string;
  pollTitle?: string;
}

interface NotificationItemProps {
  notification: Notification;
  onRead: (id: string) => void;
}

const NotificationItem: React.FC<NotificationItemProps> = ({
  notification,
  onRead,
}) => {
  const handleClick = () => {
    if (!notification.read) {
      onRead(notification.id);
    }
  };

  return (
    <div
      onClick={handleClick}
      className={cn(
        "flex items-start gap-3 p-3 text-sm border-b cursor-pointer transition-colors",
        notification.read
          ? "bg-background hover:bg-muted/50"
          : "bg-primary/5 hover:bg-primary/10"
      )}
    >
      {!notification.read && (
        <Circle className="h-2 w-2 mt-1 fill-primary text-primary flex-shrink-0" />
      )}
      <div className="flex-1 space-y-1">
        <p className="text-foreground">{notification.message}</p>
        {notification.pollTitle && (
          <p className="text-muted-foreground text-xs font-medium">
            Poll: {notification.pollTitle}
          </p>
        )}
        <p className="text-xs text-muted-foreground">
          {formatDistanceToNow(notification.timestamp, { addSuffix: true })}
        </p>
      </div>
    </div>
  );
};

export default NotificationItem;
