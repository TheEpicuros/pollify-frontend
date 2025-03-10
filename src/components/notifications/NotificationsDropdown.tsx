import React from "react";
import { ScrollArea } from "@/components/ui/scroll-area";
import NotificationItem from "./NotificationItem";
import { Button } from "@/components/ui/button";
import { Notification } from "@/lib/types";

interface NotificationsDropdownProps {
  notifications: Notification[];
  onMarkAllRead: () => void;
  onMarkAsRead: (id: string) => void;
}

const NotificationsDropdown: React.FC<NotificationsDropdownProps> = ({
  notifications,
  onMarkAllRead,
  onMarkAsRead,
}) => {
  const unreadCount = notifications.filter((n) => !n.read).length;

  return (
    <div className="w-80 rounded-md border bg-background shadow-md">
      <div className="flex items-center justify-between border-b p-3">
        <h3 className="font-medium">Notifications</h3>
        {unreadCount > 0 && (
          <Button
            onClick={onMarkAllRead}
            variant="ghost"
            className="h-auto px-2 py-1 text-xs"
          >
            Mark all as read
          </Button>
        )}
      </div>
      
      {notifications.length === 0 ? (
        <div className="flex items-center justify-center p-6 text-sm text-muted-foreground">
          You have no notifications
        </div>
      ) : (
        <ScrollArea className="h-[calc(80vh-10rem)] max-h-80">
          {notifications.map((notification) => (
            <NotificationItem
              key={notification.id}
              notification={notification}
              onRead={onMarkAsRead}
            />
          ))}
        </ScrollArea>
      )}
    </div>
  );
};

export default NotificationsDropdown;
