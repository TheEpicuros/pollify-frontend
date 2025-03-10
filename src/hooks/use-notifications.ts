
import { useState, useEffect } from 'react';
import { Notification } from '@/components/notifications/NotificationItem';
import { useToast } from '@/hooks/use-toast';

// Mock data for notifications
const mockNotifications: Notification[] = [
  {
    id: '1',
    message: 'Your poll "What frontend framework do you prefer?" received a new vote',
    timestamp: new Date(Date.now() - 1000 * 60 * 5), // 5 minutes ago
    read: false,
    type: 'vote',
    pollId: '1',
    pollTitle: 'What frontend framework do you prefer?'
  },
  {
    id: '2',
    message: 'Someone commented on your poll "How many hours do you code per day?"',
    timestamp: new Date(Date.now() - 1000 * 60 * 30), // 30 minutes ago
    read: false,
    type: 'comment',
    pollId: '2',
    pollTitle: 'How many hours do you code per day?'
  },
  {
    id: '3',
    message: 'Your poll "Do you prefer working remotely or in office?" was shared on Twitter',
    timestamp: new Date(Date.now() - 1000 * 60 * 60 * 2), // 2 hours ago
    read: true,
    type: 'share',
    pollId: '4',
    pollTitle: 'Do you prefer working remotely or in office?'
  },
  {
    id: '4',
    message: 'You earned the "Popular Pollster" achievement!',
    timestamp: new Date(Date.now() - 1000 * 60 * 60 * 12), // 12 hours ago
    read: true,
    type: 'achievement'
  },
  {
    id: '5',
    message: 'Your poll "What\'s your favorite code editor?" has reached 100 votes',
    timestamp: new Date(Date.now() - 1000 * 60 * 60 * 24), // 1 day ago
    read: true,
    type: 'vote',
    pollId: '3',
    pollTitle: 'What\'s your favorite code editor?'
  }
];

export const useNotifications = () => {
  const [notifications, setNotifications] = useState<Notification[]>(mockNotifications);
  const [unreadCount, setUnreadCount] = useState<number>(0);
  const { toast } = useToast();

  useEffect(() => {
    // Calculate unread count
    const count = notifications.filter(notification => !notification.read).length;
    setUnreadCount(count);
  }, [notifications]);

  const markAsRead = (id: string) => {
    setNotifications(prev => 
      prev.map(notification => 
        notification.id === id 
          ? { ...notification, read: true } 
          : notification
      )
    );
  };

  const markAllAsRead = () => {
    setNotifications(prev => 
      prev.map(notification => ({ ...notification, read: true }))
    );
  };

  const addNotification = (notification: Omit<Notification, 'id' | 'timestamp' | 'read'>) => {
    const newNotification: Notification = {
      ...notification,
      id: Date.now().toString(),
      timestamp: new Date(),
      read: false
    };
    
    setNotifications(prev => [newNotification, ...prev]);
    
    // Show toast for new notification
    toast({
      title: 'New Notification',
      description: notification.message,
    });
  };

  return {
    notifications,
    unreadCount,
    markAsRead,
    markAllAsRead,
    addNotification
  };
};
